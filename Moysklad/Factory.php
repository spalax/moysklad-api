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

/** Moysklad_Rest_Client */
require_once 'Moysklad/Rest/Client.php';

/** Moysklad_Settings */
require_once 'Moysklad/Settings.php';

/** Moysklad_Exception */
require_once 'Moysklad/Exception.php';

/** Moysklad_Rest_Client */
require_once 'Moysklad/Rest/Client.php';

/**
 * @package    Moysklad
 * @copyright  Copyright (c) 2010-2013 Milcrew Inc. (http://www.milcrew.com)
 * @license    http://milcrew.com/public/LICENSE.txt    New BSD License
 */
class Moysklad_Factory
{
    /**
     * @var Moysklad_Settings
     */
    protected $_settings = null;

    /**
     * @var unknown
     */
    protected $_restClient = null;

    public function __construct(Moysklad_Settings $settings)
    {
        $this->_settings = $settings;
    }

    /**
     * @return Moysklad_Rest_Client_Interface
     */
    protected function _getRestClient()
    {
        if (!is_null($this->_restClient)) {
            return $this->_restClient;
        }

        $this->_restClient = new Moysklad_Rest_Client($this->_settings->getApiUrl());

        $this->_restClient->getHttpClient()->setAuth($this->_settings->getUsername(),
                                                     $this->_settings->getPassword());
        $this->_restClient->getHttpClient()->setHeaders('Content-Type', 'application/xml');

        return $this->_restClient;
    }

    /**
     * Method of the abstract factory for return
     * requested Repository.
     *
     * @param string $name
     * @throws Moysklad_Exception
     * @return Moysklad_Repository_Abstract
     */
    public function getRepository($name)
    {
        require_once 'Moysklad/Repository/'.$name.'.php';
        $className = "Moysklad_Repository_$name";
        if (!class_exists($className)) {
            throw new Moysklad_Exception("Could not found requested repository".
                                         " on path Moysklad/Repository/{$name}.php and className ".
                                         "Moysklad_Repository_$name");
        }

        return new $className($this->_getRestClient(), $this->_settings);
    }
}
