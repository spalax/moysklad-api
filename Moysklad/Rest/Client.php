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

/** Zend_Service_Abstract */
require_once 'Zend/Service/Abstract.php';

/** Zend_Rest_Client_Result */
require_once 'Zend/Rest/Client/Result.php';

/** Zend_Http_Client_Exception */
require_once 'Zend/Http/Client/Exception.php';

/** Zend_Uri */
require_once 'Zend/Uri.php';

/** Moysklad_Rest_Client_Interface */
require_once 'Moysklad/Rest/Client/Interface.php';

/** Moysklad_Http_Response */
require_once 'Moysklad/Http/Response.php';

/** Moysklad_Http_Client_Exception */
require_once 'Moysklad/Http/Client/Exception.php';

/**
 *
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
class Moysklad_Rest_Client extends Zend_Service_Abstract
    implements Moysklad_Rest_Client_Interface
{
    /**
     * Data for the query
     * @var array
     */
    protected $_data = array();

     /**
     * Zend_Uri of this web service
     * @var Zend_Uri_Http
     */
    protected $_uri = null;

    /**
     * Constructor
     *
     * @param string|Zend_Uri_Http $uri URI for the web service
     * @return void
     */
    public function __construct($uri = null)
    {
        if (!empty($uri)) {
            $this->setUri($uri);
        }
    }

    /**
     * Set the URI to use in the request
     *
     * @param string|Zend_Uri_Http $uri URI for the web service
     * @return Moysklad_Rest_Client
     */
    public function setUri($uri)
    {
        if ($uri instanceof Zend_Uri_Http) {
            $this->_uri = $uri;
        } else {
            $this->_uri = Zend_Uri::factory($uri);
        }

        return $this;
    }

    /**
     * Retrieve the current request URI object
     *
     * @return Zend_Uri_Http
     */
    public function getUri()
    {
        return $this->_uri;
    }

    /**
     * Call a remote REST web service URI and return the Zend_Http_Response object
     *
     * @param  string $path            The path to append to the URI
     * @throws Moysklad_Rest_Client_Exception
     * @return void
     */
    final private function _prepareRest($path)
    {
        // Get the URI object and configure it
        if (!$this->_uri instanceof Zend_Uri_Http) {
            require_once 'Moysklad/Rest/Client/Exception.php';
            throw new Moysklad_Rest_Client_Exception('URI object must be set before performing call');
        }

        $uri = $this->_uri->getUri();

        if ($path[0] != '/' && $uri[strlen($uri)-1] != '/') {
            $path = '/' . $path;
        }

        $this->_uri->setPath($path);

        /**
         * Get the HTTP client and configure it for the endpoint URI.  Do this each time
         * because the Zend_Http_Client instance is shared among all Zend_Service_Abstract subclasses.
         */
        self::getHttpClient()->setUri($this->_uri);
    }

    /* (non-PHPdoc)
     * @see Moysklad_Rest_Client_Interface::restGet()
     */
    final public function restGet($path, array $query = null)
    {
        try {
            $this->_prepareRest($path);
            $client = self::getHttpClient();
            $client->setParameterGet($query);
            return new Moysklad_Http_Response($client->request('GET'));
        } catch (Zend_Http_Client_Exception $e) {
            throw new Moysklad_Http_Client_Exception('Client exception happened in restGet', null, $e);
        }
    }

    /**
     * Perform a POST or PUT
     *
     * Performs a POST or PUT request. Any data provided is set in the HTTP
     * client. String data is pushed in as raw POST data; array or object data
     * is pushed in as POST parameters.
     *
     * @param mixed $method
     * @param mixed $data
     * @return Zend_Http_Response
     */
    protected function _performPost($method, $data = null)
    {
        $client = self::getHttpClient();
        if (is_string($data)) {
            $client->setRawData($data);
        } elseif (is_array($data) || is_object($data)) {
            $client->setParameterPost((array) $data);
        }
        return $client->request($method);
    }

    /* (non-PHPdoc)
     * @see Moysklad_Rest_Client_Interface::restPost()
     */
    final public function restPost($path, $data = null)
    {
        try {
            $this->_prepareRest($path);
            return new Moysklad_Http_Response($this->_performPost('POST', $data));
        } catch (Zend_Http_Client_Exception $e) {
            throw new Moysklad_Http_Client_Exception('Client exception happened in restPost', null, $e);
        }
    }

    /* (non-PHPdoc)
     * @see Moysklad_Rest_Client_Interface::restPut()
     */
    final public function restPut($path, $data = null)
    {
        try {
            $this->_prepareRest($path);
            return new Moysklad_Http_Response($this->_performPost('PUT', $data));
        } catch (Zend_Http_Client_Exception $e) {
            throw new Moysklad_Http_Client_Exception('Client exception happened in restPut', null, $e);
        }
    }

    /* (non-PHPdoc)
     * @see Moysklad_Rest_Client_Interface::restDelete()
     */
    final public function restDelete($path)
    {
        try {
            $this->_prepareRest($path);
            return self::getHttpClient()->request('DELETE');
        } catch (Zend_Http_Client_Exception $e) {
            throw new Moysklad_Http_Client_Exception('Client exception happened in restDelete', null, $e);
        }
    }
}