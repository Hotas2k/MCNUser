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

namespace MCNUser\Authentication\Plugin;

use MCNStdlib\Stdlib\NamingConvention;
use MCNUser\Authentication\Result;
use MCNUser\Service\UserInterface;
use Zend\Crypt\Password\Bcrypt;
use Zend\Http\Request as HttpRequest;
use MCNUser\Options\Authentication\Plugin\Standard as Options;

/**
 * Class Standard
 * @package MCNUser\Authentication\Plugin
 */
class Standard extends AbstractPlugin
{
    /**
     * @param \MCNUser\Options\Authentication\Plugin\Standard $options
     */
    public function __construct(Options $options = null)
    {
        $this->options = ($options === null) ? new Options : $options;
    }

    /**
     * Authenticate the request
     *
     * Authenticate the user against the current http request
     *
     * @param \Zend\Http\Request             $request
     * @param \MCNUser\Service\UserInterface $service
     *
     * @return \MCNUser\Authentication\Result|void
     */
    public function authenticate(HttpRequest $request, UserInterface $service)
    {
        $identity   = $request->getPost($this->options->getHttpIdentityField());
        $credential = $request->getPost($this->options->getHttpCredentialField());

        $user = $service->getOneBy($this->options->getEntityIdentityProperty(), $identity);

        $bcrypt = new Bcrypt(array(
            'salt' => $this->options->getBcryptSalt(),
            'cost' => $this->options->getBcryptCost()
        ));

        if (! $user) {

            return Result::create(Result::FAILURE_IDENTITY_NOT_FOUND, null, Result::MSG_IDENTITY_NOT_FOUND);
        }

        $method = NamingConvention::toCamelCase('get_' . $this->options->getEntityCredentialProperty());

        if (! $bcrypt->verify($credential, $user->$method())) {

            return Result::create(Result::FAILURE_INVALID_CREDENTIAL, $user, Result::MSG_INVALID_CREDENTIAL);
        }

        return Result::create(Result::SUCCESS, $user);
    }
}
