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

namespace MCNUser\Service;

use DateInterval;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectManager;
use MCNUser\Entity\Token as TokenEntity;
use MCNUser\Service\Token\ConsumerInterface;
use MCNUser\Service\Token\ServiceInterface;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Math;

/**
 * Class Token
 * @package MCNUser\Service
 */
class Token implements ServiceInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return \MCNUser\Repository\TokenInterface
     */
    protected function getRepository()
    {
        return $this->objectManager->getRepository('MCNUser\Entity\Token');
    }

    /**
     * Consume the given token of a consumer
     *
     * @param \MCNUser\Service\Token\ConsumerInterface $owner
     * @param string                                   $token
     * @param string                                   $namespace
     *
     * @throws Exception\TokenIsConsumedException
     * @throws Exception\TokenNotFoundException
     */
    public function consumeToken(ConsumerInterface $owner, $token, $namespace)
    {
        $token = $this->getRepository()->get($owner, $token, $namespace);

        if (! $token) {

            throw new Exception\TokenNotFoundException;
        }

        if ($token->isConsumed()) {

            throw new Exception\TokenIsConsumedException;
        }

        $token->setConsumed(true);
        $this->objectManager->flush($token);
    }

    /**
     * Consume all the tokens of a given consumer
     *
     * Will consume all the tokens of the given Consumer object and returns the number of tokens affected
     *
     * @param \MCNUser\Service\Token\ConsumerInterface $owner
     *
     * @return integer The number of tokens affected
     */
    public function consumeAllTokens(ConsumerInterface $owner)
    {
        $this->getRepository()->consumeAllTokensAndReturnCount($owner);
    }

    /**
     * @param \MCNUser\Service\Token\ConsumerInterface $owner
     * @param string                                   $namespace
     * @param DateInterval                             $validUntil
     * @param int                                      $byteCount
     *
     * @return \MCNUser\Entity\Token
     */
    public function create(ConsumerInterface $owner, $namespace, DateInterval $validUntil = null, $byteCount = 100)
    {
        $entity = new TokenEntity();
        $entity->setToken(base64_encode(Math\Rand::getBytes($byteCount)));
        $entity->setOwner($owner->getId());
        $entity->setNamespace($namespace);

        if ($validUntil) {

            $dt = new DateTime();
            $dt->add($validUntil);

            $entity->setValidUntil($dt);
        }

        $this->objectManager->persist($entity);
        $this->objectManager->flush($entity);

        return $entity;
    }

    /**
     * @param \MCNUser\Service\Token\ConsumerInterface $owner
     * @param string                                   $token
     * @param string                                   $namespace
     *
     * @throws Exception\TokenHasExpiredException
     * @throws Exception\TokenNotFoundException
     * @throws Exception\TokenIsConsumedException
     *
     * @return TokenEntity
     */
    public function useToken(ConsumerInterface $owner, $token, $namespace)
    {
        $token = $this->getRepository()->get($owner, $token, $namespace);

        if (! $token) {

            throw new Exception\TokenNotFoundException;
        }

        if ($token->isConsumed()) {

            throw new Exception\TokenIsConsumedException;
        }

        if ($token->getValidUntil() && new DateTime() > $token->getValidUntil()) {

            throw new Exception\TokenHasExpiredException;
        }

        $remoteAddress = new RemoteAddress();

        $history = new TokenEntity\History();
        $history->setToken($token);
        $history->setCreatedAt(new DateTime());
        $history->setIp($remoteAddress->getIpAddress());

        if (isSet($_SERVER['HTTP_USER_AGENT'])) {

            $history->setHttpUserAgent($_SERVER['HTTP_USER_AGENT']);
        }

        $this->objectManager->persist($history);
        $this->objectManager->flush($history);

        return $token;
    }

    /**
     * Uses a token and then marks it as invalid
     *
     * @uses self::useToken
     *
     * @param ConsumerInterface $owner
     * @param string                  $token
     * @param string                  $namespace
     *
     * @return void
     */
    public function useAndConsume(ConsumerInterface $owner, $token, $namespace)
    {
        $token = $this->useToken($owner, $token, $namespace);
        $token->setConsumed(true);

        $this->objectManager->flush($token);
    }
}
