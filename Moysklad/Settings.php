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

/** Moysklad_Exception */
require_once 'Moysklad/Exception.php';

/**
 * @package    Moysklad
 * @copyright  Copyright (c) 2010-2013 Milcrew Inc. (http://www.milcrew.com)
 * @license    http://milcrew.com/public/LICENSE.txt    New BSD License
 */
class Moysklad_Settings
{
    /**
     * @var string
     */
    protected $_apiUrl = '';

    /**
     * @var string
     */
    protected $_username = '';

    /**
     * @var string
     */
    protected $_password = '';

    /**
     * @var string
     */
    protected $_orderSourceStoreId = '';

    /**
     * @var string
     */
    protected $_orderSourceAgentId = '';

    /**
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        if (!array_key_exists('apiUrl', $settings)) {
            throw new Moysklad_Exception('apiUrl key must be defined');
        }

        if (!array_key_exists('username', $settings) ||
            !array_key_exists('password', $settings)) {
            throw new Moysklad_Exception('username and password keys must exists');
        }

        if (array_key_exists('orderSourceStoreId', $settings)) {
            $this->setOrderSourceStoreId($settings['orderSourceStoreId']);
        }
        if (array_key_exists('orderSourceAgentId', $settings)) {
            $this->setOrderSourceAgentId($settings['orderSourceAgentId']);
        }

        $this->setApiUrl($settings['apiUrl']);
        $this->setUsername($settings['username']);
        $this->setPassword($settings['password']);
    }

    /**
     * @return number
     */
    public function getOrderSourceStoreId()
    {
        return $this->_orderSourceStoreId;
    }

	/**
     * @return number
     */
    public function getOrderSourceAgentId()
    {
        return $this->_orderSourceAgentId;
    }

	/**
     * @param string $orderSourceStoreId
     * @return Moysklad_Settings
     */
    public function setOrderSourceStoreId($orderSourceStoreId)
    {
        $this->_orderSourceStoreId = $orderSourceStoreId;
        return $this;
    }

	/**
     * @param string $orderSourceAgentId
     * @return Moysklad_Settings
     */
    public function setOrderSourceAgentId($orderSourceAgentId)
    {
        $this->_orderSourceAgentId = $orderSourceAgentId;
        return $this;
    }

	/**
     * @param string $apiUrl
     * @return Moysklad_Settings
     */
    public function setApiUrl($apiUrl)
    {
        $this->_apiUrl = $apiUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->_apiUrl;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * @param string $username
     * @return Moysklad_Settings
     */
    public function setUsername ($username)
    {
        $this->_username = $username;
        return $this;
    }

    /**
     * @param string $password
     * @return Moysklad_Settings
     */
    public function setPassword ($password)
    {
        $this->_password = $password;
        return $this;
    }
}
