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

/**Moysklad_Repository_Abstract*/
require_once 'Moysklad/Repository/Abstract.php';

/**Moysklad_Repository_InterfaceRead*/
require_once 'Moysklad/Repository/InterfaceRead.php';

/**Moysklad_Exception*/
require_once 'Moysklad/Exception.php';

/**
 * @package    Moysklad
 * @copyright  Copyright (c) 2010-2013 Milcrew Inc. (http://www.milcrew.com)
 * @license    http://milcrew.com/public/LICENSE.txt    New BSD License
 */
abstract class Moysklad_Repository_AbstractRead extends
               Moysklad_Repository_Abstract
    implements Moysklad_Repository_InterfaceRead
{

    /**
     * @var url
     */
    protected $_serviceUrl = '';

    /**
     * @param string $url
     * @param string $filter
     * @throws InvalidArgumentException
     * @return string
     */
    protected function _transformFilterToQuery(array $filter = array())
    {
        if (empty($filter)) {
            return $filter;
        }

        $filterPieces = array();
        foreach ($filter as $k => $v) {
            $filterPieces[] = str_replace('?', $v, $k);
        }

        return array('filter'=>join(";", $filterPieces));
    }

    /**
     * @return string
     */
    protected function _getListUrl()
    {
        return $this->_serviceUrl.'/list';
    }

    /**
     * @throws Moysklad_Exception
     * @return array
     */
    public function fetchAll(array $filter = array())
    {
        $startTime = time();
        $response = $this->_client->restGet($this->_getListUrl(), $this->_transformFilterToQuery($filter));

        if ($response->getStatus() != '200') {
            throw new Moysklad_Exception("Requesting list finished with exception with body ".$response->getBody());
        }

        $xmlIterator = new SimpleXMLIterator($response->getBody());

        $results = array();

        /* @var $element SimpleXMLIterator */
        foreach ($xmlIterator as $element) {
            $results[] = $this->_getItemObject($element);
        }

        return $results;
    }
}