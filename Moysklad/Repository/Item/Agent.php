<?php
/**
 * Copyright (c) 2010-2013 Milcrew Inc. (http://www.milcrew.com)
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *  1) Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 *  2) Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *  3) Neither the name of the Milcrew Inc. nor the
 *    names of its contributors may be used to endorse or promote products
 *    derived from this software without specific prior written permission.

 *  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 *  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 *  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 *  DISCLAIMED. IN NO EVENT SHALL Milcrew Inc. BE LIABLE FOR ANY
 *  DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 *  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 *  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 *  ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 *  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 *  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *  @author Milotskiy Alexey (aka SpalaX)
 */

/**Moysklad_Repository_Item_Interfae*/
require_once 'Moysklad/Repository/Item/Interface.php';

/**Moysklad_Repository_Item_InterfaceWrite*/
require_once 'Moysklad/Repository/Item/InterfaceWrite.php';

/**
 * @package    Moysklad
 * @copyright  Copyright (c) 2010-2013 Milcrew Inc. (http://www.milcrew.com)
 * @license    http://milcrew.com/public/LICENSE.txt    New BSD License
 */
class Moysklad_Repository_Item_Agent implements Moysklad_Repository_Item_Interface,
                                                Moysklad_Repository_Item_InterfaceWrite
{
    /**
     * @var string
     */
    protected $_name = '';
    /**
     * @var string
     */
    protected $_phone = '';
    /**
     * @var Moysklad_Rest_Client
     */
    protected $_client = null;

    /**
     * @var number
     */
    protected $_id = 0;

    /**
     * @var boolean
     */
    protected $_persist = false;

    public function __construct(SimpleXMLElement $element = null, Moysklad_Rest_Client $client)
    {

        if (!is_null($element)) {
            if (!$element->id) {
                throw new InvalidArgumentException('Could not found id in element');
            }
            $this->_id = (string)$element->id;

            if (!$element->attributes()->name) {
                throw new InvalidArgumentException('Could not found name in element');
            }
            $this->_name = (string)$element->attributes()->name;

            if (!$element->contact->attributes()->mobiles) {
                throw new InvalidArgumentException('Could not found phone in element');
            }
            $this->_phone = (string)$element->contact->attributes()->mobiles;

            $this->_persist = true;
        }
        $this->_client = $client;
    }

    /* (non-PHPdoc)
     * @see Moysklad_Repository_Item_Interface::isPersisted()
    */
    public function isPersisted()
    {
        return $this->_persist;
    }

    /**
     * @param string $name
     * @return Moysklad_Repository_Item_Agent
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * @param string $phone
     * @return Moysklad_Repository_Item_Agent
     */
    public function setPhone($phone)
    {
        $this->_phone = $phone;
        return $this;
    }

    /**
     * @param number $id
     * @return Moysklad_Repository_Item_Agent
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * @return number
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @throws DomainException
     * @throws Zend_Service_Exception
     * @return boolean
     */
    public function delete()
    {
        if ($this->_persist !== true) {
            throw new DomainException("Could not delete not persisted object");
        }

        $response = $this->_client->restDelete('/exchange/rest/ms/xml/Company/'.$this->getId());
        if ($response->getStatus() != '200') {
            throw new Zend_Service_Exception('Could not delete Agent on remote service');
        }

        $this->_id = 0;
        $this->_persist = 0;
        return true;
    }

    /**
     * Put Agent to remote service
     *
     * @throws DomainException
     * @throws Zend_Service_Exception
     * @return Moysklad_Repository_Item_Agent
     */
    public function create()
    {
        if ($this->_persist === true) {
            throw new DomainException('Agent already persisted');
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                 <company
                  companyType="URLI"
                  discount="0.0"
                  autoDiscount="0.0"
                  archived="false"
                  name="'.$this->_name.'">
                  <contact
                   address=""
                   email=""
                   faxes=""
                   mobiles="'.$this->_phone.'"
                   phones="'.$this->_phone.'" />
                 </company>';

        $response = $this->_client->restPut('/exchange/rest/ms/xml/Company', $xml);

        if ($response->getStatus() != '200') {
            throw new Zend_Service_Exception('Could not create Agent on remote service');
        }

        $xmlElement = new SimpleXMLElement($response->getBody());

        if (!$xmlElement->id) {
            throw new Zend_Service_Exception('Could not find identifier in responsed Agent');
        }

        $this->setId($xmlElement->id);
        $this->_persist = true;
        return $this;
    }
}
