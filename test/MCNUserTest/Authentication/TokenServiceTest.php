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

namespace MCNUserTest\Authentication;

use ArrayObject;
use DateInterval;
use DateTime;
use MCNUser\Authentication\TokenService;
use MCNUser\Entity\AuthToken;
use MCNUserTest\TestAsset\Authentication\AuthTokenOwnerEntity;

class TokenServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectRepository;

    /**
     * @var \MCNUser\Authentication\TokenService
     */
    protected $service;

    protected function setUp()
    {
        $this->objectManager    = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->objectRepository = $this->getMock('MCNUser\Repository\AuthTokenInterface');

        $this->objectManager
             ->expects($this->any())
             ->method('getRepository')
             ->will($this->returnValue($this->objectRepository));

        $this->service = new TokenService($this->objectManager);
    }

    protected function getEntity()
    {
        return new AuthTokenOwnerEntity();
    }

    public function testCreateToken()
    {
        $this->objectManager->expects($this->once())->method('persist');
        $this->objectManager->expects($this->once())->method('flush');

        $token = $this->service->create($this->getEntity());

        $this->assertInstanceOf('MCNUser\Entity\AuthToken', $token);
        $this->assertEquals(1, $token->getOwner());
    }

    public function testCreateTokenWithValidUntilConstraint()
    {
        $this->objectManager->expects($this->once())->method('persist');
        $this->objectManager->expects($this->once())->method('flush');

        $interval = new DateInterval('PT1H');

        $token = $this->service->create($this->getEntity(), $interval);

        $this->assertInstanceOf('MCNUser\Entity\AuthToken', $token);

        $dt = new DateTime();
        $dt->add($interval);

        $this->assertEquals($dt, $token->getValidUntil());
    }

    /**
     * @expectedException \MCNUser\Authentication\Exception\TokenNotFoundException
     */
    public function testUseTokenWithNonExistingToken()
    {
        $this->service->useToken($this->getEntity(), 'i do not exists');
    }

    /**
     * @expectedException \MCNUser\Authentication\Exception\TokenIsConsumedException
     */
    public function testUseTokenForTokenIsConsumedException()
    {
        $token = new AuthToken();
        $token->setConsumed(true);

        $this->objectRepository->expects($this->once())->method('getByOwnerAndToken')->will($this->returnValue($token));

        $this->service->useToken($this->getEntity(), 'mock token');
    }

    /**
     * @expectedException \MCNUser\Authentication\Exception\TokenHasExpiredException
     */
    public function testUseTokenForTokenHasExpiredException()
    {
        $token = new AuthToken();
        $token->setValidUntil(DateTime::createFromFormat('U', time() - 1));

        $this->objectRepository->expects($this->once())->method('getByOwnerAndToken')->will($this->returnValue($token));

        $this->service->useToken($this->getEntity(), 'mock token');
    }

    /**
     *
     */
    public function testUseTokenSuccessfully()
    {
        $token = new AuthToken();

        $this->objectRepository
            ->expects($this->once())
            ->method('getByOwnerAndToken')
            ->will($this->returnValue($token));

        $this->objectManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function($entity) use ($token) {

                $this->assertEquals('test', $entity->getHttpUserAgent());
                $this->assertEquals('127.0.0.1', $entity->getIp());
                $this->assertEquals($token, $entity->getToken());

                return $entity instanceof AuthToken\History;
            }));

        $this->objectManager
            ->expects($this->once())
            ->method('flush');

        $_SERVER['REMOTE_ADDR']     = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'test';

        $result = $this->service->useToken($this->getEntity(), 'mock token');

        $this->assertEquals($token, $result);
    }
}
