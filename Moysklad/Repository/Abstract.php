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

/**
 * @package    Moysklad
 * @copyright  Copyright (c) 2010-2013 Milcrew Inc. (http://www.milcrew.com)
 * @license    http://milcrew.com/public/LICENSE.txt    New BSD License
 */
abstract class Moysklad_Repository_Abstract
{
    /**
     * @var Class of the item
     */
    protected $_itemClass = '';

    /**
     * @var Moysklad_Rest_Client_Interface
     */
    protected $_client = null;

    /**
     * @var Moysklad_Settings
     */
    protected $_settings = null;


    /**
     * @param Moysklad_Rest_Client_Interface $client
     * @param Moysklad_Settings $settings
     */
    public function __construct(Moysklad_Rest_Client_Interface $client, Moysklad_Settings $settings)
    {
        $this->_client = $client;
        $this->_settings = $settings;
    }

    /**
     * @param SimpleXMLElement $element
     * @return Moysklad_Repository_Item_Interface
     */
    protected function _getItemObject(SimpleXMLElement $element = null)
    {
        if (!class_exists($this->_itemClass)) {
            $loadPath = str_replace('_', '/', $this->_itemClass).'.php';
            require_once $loadPath;
            if (!class_exists($this->_itemClass)) {
                throw new Moysklad_Exception("Could not load itemClass from path $loadPath ".
                                             "with name {$this->_itemClass}");
            }
        }

        $reflectionClass = new ReflectionClass($this->_itemClass);
        /* @var $constructor ReflectionMethod */
        $constructor = $reflectionClass->getConstructor();

        $args = array();
        /* @var $parameter ReflectionParameter */
        foreach($constructor->getParameters() as $k=>$parameter) {
            switch($parameter->getClass()->getName()) {
                case 'SimpleXMLElement' :
                    $args[$k] = $element;
                    break;
                case 'Moysklad_Rest_Client' :
                    $args[$k] = $this->_client;
                    break;
                case 'Moysklad_Settings' :
                    $args[$k] = $this->_settings;
                    break;
            }
        }
        return $reflectionClass->newInstanceArgs($args);
    }
}
