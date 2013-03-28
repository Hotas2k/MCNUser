<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
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
    public function testConsumeTokenForTokenNotFoundException()
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

    public function testUseTokenSuccessfully()
    {
        $token = new AuthToken();

        $this->objectRepository->expects($this->once())->method('getByOwnerAndToken')->will($this->returnValue($token));
        $this->objectManager->expects($this->once())->method('flush');

        $result = $this->service->useToken($this->getEntity(), 'mock token');

        $this->assertEquals($token, $result);
    }
}
