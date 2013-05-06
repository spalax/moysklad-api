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
class Moysklad_Repository_Item_Good implements Moysklad_Repository_Item_Interface
{

    /**
     * @var string
     */
    protected $_id = 0;

    /**
     * @var number
     */
    protected $_price = 0;

    /**
     * @var string
     */
    protected $_parentUuid = 0;

    /**
     * @var string
     */
    protected $_externalcode = '';

    /**
     * @var string
     */
    protected $_description = '';

    /**
     * @var string
     */
    protected $_name = '';

    /**
     * @var string
     */
    protected $_article = 0;

    public function __construct(SimpleXMLElement $element = null)
    {
        if (!is_null($element)) {
            if (!$element->id) {
                throw new InvalidArgumentException('Could not found id in element');
            }
            $this->_id = (string)$element->id;

            if (!$element->salePrices->price->attributes()->value) {
                throw new InvalidArgumentException('Could not found price in element');
            }
            $price = (string)$element->salePrices->price->attributes()->value;
            $explodedPrice = explode('.', $price);

            $this->_article = (string)$element->attributes()->productCode;
            $this->_price = substr($explodedPrice[0], 0, -2);
            $this->_parentUuid = (string)$element->attributes()->parentUuid;
            $this->_externalcode = (string)$element->externalcode;
            $this->_description = (string)$element->description;
            $this->_name = (string)$element->attributes()->name;
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return number
     */
    public function getPrice()
    {
        return $this->_price;
    }

    /**
     * @return string
     */
    public function getExternalCode()
    {
        return $this->_externalcode;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
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
    public function getParentUuid()
    {
        return $this->_parentUuid;
    }

    /**
     * @return string
     */
    public function getArticle()
    {
        return $this->_article;
    }
}
