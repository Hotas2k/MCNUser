<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\Authentication;


use MCNUser\Authentication\AuthEvent;
use MCNUser\Authentication\AuthenticationService;
use MCNUser\Authentication\Result;
use MCNUser\Entity\User;
use PHPUnit_Framework_TestCase;
use Zend\Http\Request;

class AuthenticationServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Http\Request
     */
    protected $request;

    /**
     * @var \MCNUser\Authentication\AuthenticationService
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userService;

    protected function setUp()
    {
        $this->request     = new Request();
        $this->userService = $this->getMock('MCNUser\Service\UserInterface');

        $this->service = new AuthenticationService($this->userService);
    }

    /**
     * @expectedException \MCNUser\Authentication\Exception\DomainException
     */
    public function testAuthenticateThrowsExceptionOnInvalidPlugin()
    {
        $this->service->authenticate($this->request, 'i am a plugin that does not exist');
    }

    public function testAuthFailsIfSuccessEventIsStopped()
    {
        $this->service->getEventManager()->attach(AuthEvent::EVENT_AUTH_SUCCESS, function(AuthEvent $e) {

            $e->stopPropagation(true);
        });

        $result = Result::create(Result::SUCCESS);
        $plugin = $this->getMock('MCNUser\Authentication\Plugin\AbstractPlugin');

        $plugin->expects($this->once())
               ->method('authenticate')
               ->withAnyParameters()
               ->will($this->returnValue($result));

        $this->service->getPluginManager()->setService('success', $plugin);

        $result = $this->service->authenticate($this->request, 'success');

        $this->assertEquals(Result::FAILURE_UNCATEGORIZED, $result->getCode());
    }

    public function testSuccessfulLogin()
    {
        $user = new User();

        $result = Result::create(Result::SUCCESS, $user);
        $plugin = $this->getMock('MCNUser\Authentication\Plugin\AbstractPlugin');

        $plugin->expects($this->once())
            ->method('authenticate')
            ->withAnyParameters()
            ->will($this->returnValue($result));

        $this->service->getPluginManager()->setService('success', $plugin);

        $this->assertFalse($this->service->hasIdentity());

        $result = $this->service->authenticate($this->request, 'success');

        $this->assertTrue($this->service->hasIdentity());
        $this->assertEquals(Result::SUCCESS, $result->getCode());
        $this->assertEquals($result->getIdentity(), $user);
        $this->assertEquals($this->service->getIdentity(), $user);
    }
}
