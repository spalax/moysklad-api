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

/** Moysklad_Repository_AbstractWrite */
require_once 'Moysklad/Repository/AbstractWrite.php';

/**
 * @package    Moysklad
 * @copyright  Copyright (c) 2010-2013 Milcrew Inc. (http://www.milcrew.com)
 * @license    http://milcrew.com/public/LICENSE.txt    New BSD License
 */
class Moysklad_Repository_Agents extends Moysklad_Repository_AbstractWrite
{
    /**
     * @var string
     */
    protected $_serviceUrl = '/exchange/rest/ms/xml/Company';

    /**
     * @var string
     */
    protected $_itemClass = 'Moysklad_Repository_Item_Agent';

    /**
     * @param string $phone
     * @throws Zend_Service_Exception
     * @return Moysklad_Repository_Item_Agent | NULL
     */
    public function find($phone)
    {
        $response = $this->_client->restGet($this->_serviceUrl.'/list');
        if ($response->getStatus() != '200') {
            throw new Zend_Service_Exception("Requesting company list finished with exception");
        }

        $xmlIterator = new SimpleXMLIterator($response->getBody());

        /* @var $element SimpleXMLIterator */
        foreach ($xmlIterator as $element) {
            if ($element->contact) {
                if ($element->contact->attributes() &&
                    preg_match('/'.preg_quote($phone).'$/',$element->contact->attributes()->mobiles)) {
                    return $this->_getItemObject($element);
                }
            }
        }

        return null;
    }
}
