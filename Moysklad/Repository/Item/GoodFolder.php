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

/**
 * @package    Moysklad
 * @copyright  Copyright (c) 2010-2013 Milcrew Inc. (http://www.milcrew.com)
 * @license    http://milcrew.com/public/LICENSE.txt    New BSD License
 */
class Moysklad_Repository_Item_GoodFolder implements Moysklad_Repository_Item_Interface
{
    /**
     * @var number
     */
    protected $_id = 0;

    /**
     * @var number
     */
    protected $_uuid = 0;

    /**
     * @var number
     */
    protected $_parentId = 0;

    /**
     * @var string
     */
    protected $_name = '';

    /**
     * @var string
     */
    protected $_description = '';

    public function __construct(SimpleXMLElement $element = null)
    {
        if (!is_null($element)) {
            if (!$element->id) {
                throw new InvalidArgumentException('Could not found id in element');
            }
            $this->_id = (string)$element->id;
            $this->_parentId = (string)$element->attributes()->parentId;
            $this->_uuid = (string)$element->uuid;
            $this->_name = (string)$element->attributes()->name;
            $this->_description = (string)$element->description;
        }
    }

    /**
     * @return number
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return number
     */
    public function getUuid()
    {
        return $this->_uuid;
    }

    /**
     * @return number
     */
    public function getParentId()
    {
        return $this->_parentId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }
}
