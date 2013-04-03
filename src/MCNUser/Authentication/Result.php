<?php
/**
 * Copyright (c) 2011-2013 Antoine Hedgecock.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author      Antoine Hedgecock <antoine@pmg.se>
 * @author      Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright   2011-2013 Antoine Hedgecock
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace MCNUser\Authentication;

use MCNUser\Entity\UserInterface;
use Zend\Stdlib\AbstractOptions;

/**
 * Class Result
 * @package MCNUser\Authentication
 */
class Result extends AbstractOptions
{
    const SUCCESS = 1;
    const FAILURE_DISABLED_PLUGIN    = -4;
    const FAILURE_IDENTITY_NOT_FOUND = -1;
    const FAILURE_INVALID_CREDENTIAL = -2;
    const FAILURE_UNCATEGORIZED      = -3;

    const MSG_INVALID_CREDENTIAL = 'Wrong identity or credential specified.';
    const MSG_IDENTITY_NOT_FOUND = 'No entity with the given identity was found.';

    /**
     * @var integer
     */
    protected $code;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var mixed
     */
    protected $identity;

    /**
     * Create a new instance
     *
     * @param integer $code
     * @param mixed   $identity
     * @param string  $message
     *
     * @return static
     */
    public static function create($code, UserInterface $identity = null, $message = '')
    {
        return new static(array(
            'code'     => $code,
            'message'  => $message,
            'identity' => $identity
        ));
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param integer $code
     * @throws Exception\InvalidArgumentException
     */
    public function setCode($code)
    {
        if (!is_int($code) || $code > static::SUCCESS || $code < static::FAILURE_UNCATEGORIZED) {

            throw new Exception\OutOfBoundsException(
                sprintf('Illegal error code %d provided', $code)
            );
        }

        $this->code = $code;

        return $this;
    }

    /**
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param mixed $identity
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
    }
}
