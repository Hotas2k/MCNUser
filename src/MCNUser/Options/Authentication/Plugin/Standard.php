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

/**
 * Class Standard
 * @package MCNUser\Options\Authentication\Plugin
 */
class Standard extends AbstractPluginOptions
{
    /**
     * @var string
     */
    protected $entity_identity_property = 'email';

    /**
     * @var string
     */
    protected $entity_credential_property = 'password';

    /**
     * @var string
     */
    protected $http_identity_field = 'identity';

    /**
     * @var string
     */
    protected $http_credential_field = 'credential';

    /**
     * @var int
     */
    protected $bcrypt_cost = 10;

    /**
     * @var string
     */
    protected $bcrypt_salt = ':((*^&!@#!(@#^*(&!@)';

    /**
     * Class name of representing plugin
     *
     * @return string
     */
    public function getClassName()
    {
        return 'MCNUser\Authentication\Plugin\Standard';
    }

    /**
     * @return string
     */
    public function getPluginManagerAlias()
    {
        return 'standard';
    }

    /**
     * @return string
     */
    public function getServiceManagerAlias()
    {
        return 'mcn.authentication.plugin.standard';
    }

    /**
     * @param string $entity_credential_property
     */
    public function setEntityCredentialProperty($entity_credential_property)
    {
        $this->entity_credential_property = $entity_credential_property;
    }

    /**
     * @return string
     */
    public function getEntityCredentialProperty()
    {
        return $this->entity_credential_property;
    }

    /**
     * @param string $entity_identity_property
     */
    public function setEntityIdentityProperty($entity_identity_property)
    {
        $this->entity_identity_property = $entity_identity_property;
    }

    /**
     * @return string
     */
    public function getEntityIdentityProperty()
    {
        return $this->entity_identity_property;
    }

    /**
     * @param string $http_credential_field
     */
    public function setHttpCredentialField($http_credential_field)
    {
        $this->http_credential_field = $http_credential_field;
    }

    /**
     * @return string
     */
    public function getHttpCredentialField()
    {
        return $this->http_credential_field;
    }

    /**
     * @param string $http_identity_field
     */
    public function setHttpIdentityField($http_identity_field)
    {
        $this->http_identity_field = $http_identity_field;
    }

    /**
     * @return string
     */
    public function getHttpIdentityField()
    {
        return $this->http_identity_field;
    }

    /**
     * @return int
     */
    public function getBcryptCost()
    {
        return $this->bcrypt_cost;
    }

    /**
     * @param int $bcrypt_cost
     */
    public function setBcryptCost($bcrypt_cost)
    {
        $this->bcrypt_cost = $bcrypt_cost;
    }

    /**
     * @return string
     */
    public function getBcryptSalt()
    {
        return $this->bcrypt_salt;
    }

    /**
     * @param string $bcrypt_salt
     */
    public function setBcryptSalt($bcrypt_salt)
    {
        $this->bcrypt_salt = $bcrypt_salt;
    }
}
