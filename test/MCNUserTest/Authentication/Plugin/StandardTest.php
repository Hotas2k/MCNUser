<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\Authentication\Plugin;

use MCNUser\Authentication\Plugin\Standard;
use MCNUser\Authentication\Result;
use MCNUser\Entity\User;
use MCNUser\Options\Authentication\Plugin\Standard as StandardOptions;
use MCNUserTest\TestAsset\UserService;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;

class StandardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \MCNUser\Entity\User
     */
    protected $user;

    /**
     * @var \MCNUser\Authentication\Plugin\Standard
     */
    protected $plugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userService;

    public function setUp()
    {
        $this->user = new User();
        $this->user->fromArray(array(
            'id'    => 1,
            'email' => 'hello@world.com',
            'password' => sha1('password')
        ));

        $this->userService = $this->getMock('MCNUser\Service\UserInterface');
        $this->plugin = new Standard();
    }

    public function testForIdentityNotFound()
    {
        $request = new Request();
        $request->setPost(
            new Parameters(array(
                'email'    => 'wrong email',
                'password' => 'password'
            ))
        );

        $result = $this->plugin->authenticate($request, $this->userService);

        $this->assertTrue($result->getCode() == Result::FAILURE_IDENTITY_NOT_FOUND);
        $this->assertNull($result->getIdentity());
    }

    public function testForInvalidCredential()
    {
        $request = new Request();
        $request->setPost(
            new Parameters(array(
                'identity' => 'hello@world.com',
                'credential' => 'wrong password'
            ))
        );

        $this->userService
            ->expects($this->any())
            ->method('getOneBy')
            ->will($this->returnValue($this->user));

        $result = $this->plugin->authenticate($request, $this->userService);

        $this->assertTrue($result->getCode()     == Result::FAILURE_INVALID_CREDENTIAL);
        $this->assertTrue($result->getIdentity() == $this->user);
    }

    public function testForSuccessfulLogin()
    {
        $this->userService
            ->expects($this->any())
            ->method('getOneBy')
            ->will($this->returnValue($this->user));

        $request = new Request();
        $request->setPost(
            new Parameters(array(
                'identity' => 'hello@world.com',
                'credential' => 'password'
            ))
        );

        $result = $this->plugin->authenticate($request, $this->userService);

        $this->assertTrue($result->getCode()     == Result::SUCCESS);
        $this->assertTrue($result->getIdentity() == $this->user);
    }
}
