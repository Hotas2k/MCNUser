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
use MCNUser\Factory\AuthenticationServiceFactory;
use MCNUserTest\Bootstrap;
use MCNUserTest\TestAsset\UserService;
use PHPUnit_Framework_TestCase;
use Zend\EventManager\Event;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;

class AuthenticationServiceTest extends PHPUnit_Framework_TestCase
{
    protected $userService;

    public function setUp()
    {
        $this->userService = new UserService;
    }

    /**
     * @expectedException \MCNUser\Authentication\Exception\DomainException
     */
    public function testAuthenticateThrowsExceptionOnInvalidPlugin()
    {
        $service = new AuthenticationService($this->userService);
        $service->authenticate(new Request, 'i don\'t exist');
    }

    public function testSetResultCodeFailureUncategorizedIfEventAuthSuccessEventStops()
    {
        $service = new AuthenticationService($this->userService);
        $service->getEventManager()->attach(AuthEvent::EVENT_AUTH_SUCCESS, function(Event $e) {

            $e->stopPropagation(true);
        });

        $request = new Request();
        $request->setPost(
            new Parameters(array(
                'identity' => 'hello@world.com',
                'credential' => 'password'
            ))
        );

        $result = $service->authenticate($request);

        $this->assertTrue($result->getCode() == Result::FAILURE_UNCATEGORIZED);
        $this->assertTrue(!$service->hasIdentity());
    }
}
