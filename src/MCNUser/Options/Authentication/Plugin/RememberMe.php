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

namespace MCNUser\Options\Authentication\Plugin;

use DateInterval;

/**
 * Class RememberMe
 * @package MCNUser\Options\Authentication\Plugin
 */
class RememberMe extends AbstractPluginOptions
{
    /**
     * @var string
     */
    protected $entity_identity_property = 'email';

    /**
     * @var \DateInterval
     */
    protected $valid_interval;

    /**
     * Namespace for token
     *
     * @var string
     */
    protected $token_namespace = 'mcn.remember.me';

    /**
     * @param string $token_namespace
     */
    public function setTokenNamespace($token_namespace)
    {
        $this->token_namespace = $token_namespace;
    }

    /**
     * @return string
     */
    public function getTokenNamespace()
    {
        return $this->token_namespace;
    }

    /**
     * Class name of representing plugin
     *
     * @return string
     */
    public function getClassName()
    {
        return 'MCNUser\Authentication\Plugin\RememberMe';
    }

    /**
     * Plugin alias
     *
     * @return string
     */
    public function getPluginManagerAlias()
    {
        return 'remember-me';
    }

    /**
     * @return string
     */
    public function getServiceManagerAlias()
    {
        return 'mcn.authentication.plugin.remember-me';
    }

    /**
     * @return \DateInterval
     */
    public function getValidInterval()
    {
        return $this->valid_interval;
    }

    /**
     * @param \DateInterval $valid_until
     */
    public function setValidInterval(DateInterval $valid_until)
    {
        $this->valid_interval = $valid_until;
    }

    /**
     * @return string
     */
    public function getEntityIdentityProperty()
    {
        return $this->entity_identity_property;
    }

    /**
     * @param string $entity_identity_property
     */
    public function setEntityIdentityProperty($entity_identity_property)
    {
        $this->entity_identity_property = $entity_identity_property;
    }
}
