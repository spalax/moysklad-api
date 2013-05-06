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

/** Zend_Http_Response */
require_once 'Zend/Http/Response.php';

/** Moysklad_Http_Response_Interface */
require_once 'Moysklad/Http/Response/Interface.php';

/**
 * It is not valid REST client, because it is
 * send XML in body instead Query String. But this
 * trick is required for communicate with MoySklad.
 * In method _prepareRest removed resetParameters for
 * HTTP Client for ability to set content type to application/xml
 *
 * FIXME: Replace this client with standard Zend_Rest_Client
 *
 *
 * @package    Moysklad
 * @copyright  Copyright (c) 2010-2013 Milcrew Inc. (http://www.milcrew.com)
 * @license    http://milcrew.com/public/LICENSE.txt    New BSD License
 *
 */
class Moysklad_Http_Response extends Zend_Http_Response
    implements Moysklad_Http_Response_Interface
{
    /**
     * @var Zend_Http_Response
     */
    protected $_response = null;

    /**
     * @param Zend_Http_Response $response
     */
    public function __construct(Zend_Http_Response $response)
    {
        $this->_response = $response;
    }

    /* (non-PHPdoc)
     * @see Moysklad_Http_Response_Interface::getStatus()
     */
    public function getStatus()
    {
        return $this->_response->getStatus();
    }

    /* (non-PHPdoc)
     * @see Moysklad_Http_Response_Interface::getBody()
     */
    public function getBody()
    {
        return $this->_response->getBody();
    }
}
