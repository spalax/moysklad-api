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
 *  3) Neither the name of the <organization> nor the
 *    names of its contributors may be used to endorse or promote products
 *    derived from this software without specific prior written permission.

 *  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 *  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 *  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 *  DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
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
class Moysklad_Repository_Item_Order implements Moysklad_Repository_Item_Interface,
                                                Moysklad_Repository_Item_InterfaceWrite
{
    /**
     * @var Moysklad_Rest_Client
     */
    protected $_client = null;

    /**
     * @var array
     */
    protected $_positions = array();

    /**
     * @var number
     */
    protected $_id = 0;

    /**
     * @var string
     */
    protected $_comment = '';

    /**
     * @var string
     */
    protected $_sourceStoreId = '';

    /**
     * @var string
     */
    protected $_sourceAgentId = '';

    /**
     * @var string
     */
    protected $_targetAgentId = '';

    /**
     * @var boolean
     */
    protected $_persist = false;

    /**
     * @var Moysklad_Settings
     */
    protected $_settings = null;

    public function __construct(SimpleXMLElement $element = null,
                                Moysklad_Rest_Client $client,
                                Moysklad_Settings $settings = null)
    {
        if (!is_null($element)) {
            if (!$element->id) {
                throw new InvalidArgumentException('Could not found id in element');
            }

            $this->_id = $element->id;
            $this->_sourceStoreId = $element->sourceStoreId;
            $this->_sourceAgentId = $element->sourceAgentId;
            $this->_persist = true;
        } else {
            if (!is_null($settings)) {
                $this->_sourceStoreId = $this->_settings->getOrderSourceStoreId();
                $this->_sourceAgentId = $this->_settings->getOrderSourceAgentId();
            }
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
     * @return number
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param string $storeId
     * @return Moysklad_Repository_Item_Order
     */
    public function setSourceStoreId($storeId)
    {
        $this->_sourceStoreId = $storeId;
        return $this;
    }

    /**
     * @param string $agentId
     * @return Moysklad_Repository_Item_Order
     */
    public function setSourceAgentId($agentId)
    {
        $this->_sourceAgentId = $agentId;
        return $this;
    }

    /**
     * @param string $agentId
     * @return Moysklad_Repository_Item_Order
     */
    public function setTargetAgentId(Moysklad_Repository_Item_Agent $agent)
    {
        if (!$agent->isPersisted()) {
            throw new InvalidArgumentException("Agent item must be persisted");
        }
        $this->_targetAgentId = $agent->getId();
        return $this;
    }

    /**
     * @param string $comment
     * @return Moysklad_Repository_Item_Order
     */
    public function setComment($comment)
    {
        if (get_magic_quotes_gpc()) {
            $comment = stripslashes($comment);
        }
        $this->_comment = substr($comment, 0, 4000);
        return $this;
    }

    public function addPosition($goodId, $quantity, $price)
    {
        $this->_positions[$goodId] = '<customerOrderPosition goodId="'.$goodId.'" quantity="'.$quantity.'.0">'.
                                     '<basePrice sum="'.$price.'00.0" /><reserve>'.$quantity.'.0</reserve>'.
                                     '</customerOrderPosition>';
        return $this;
    }

    /**
     * Put order to remote service
     *
     * @throws DomainException
     * @throws Zend_Service_Exception
     * @return Moysklad_Repository_Item_Contragent
     */
    public function create()
    {
        if ($this->_persist === true) {
            throw new DomainException('Contragent already persisted');
        }

        if (count($this->_positions) < 1) {
            throw new DomainException('Count of ordered items must not be less then 1');
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <customerOrder applicable="false"
                               sourceStoreId="'.$this->_sourceStoreId.'"
                               sourceAgentId="'.$this->_targetAgentId.'"
                               targetAgentId="'.$this->_sourceAgentId.'">
                    <description>'.$this->_comment.'</description>
                    '.join("\n", $this->_positions).'
                </customerOrder>';

        $response = $this->_client->restPut('/exchange/rest/ms/xml/CustomerOrder', $xml);

        if ($response->getStatus() != '200') {
            throw new Zend_Service_Exception('Could not create order on the remote service');
        }

        $xmlElement = new SimpleXMLElement($response->getBody());

        if (!$xmlElement->id) {
            throw new Zend_Service_Exception('Could not find identifier in responsed contragent');
        }

        $this->_id = $xmlElement->id;
        $this->_persist = true;
        return $this;
    }
}
