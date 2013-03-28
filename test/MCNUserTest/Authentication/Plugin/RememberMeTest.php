<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\Authentication\Plugin;

use MCNUser\Authentication\Exception\TokenIsConsumedException;
use MCNUser\Authentication\Exception\TokenHasExpiredException;
use MCNUser\Authentication\Exception\TokenNotFoundException;
use MCNUser\Authentication\Plugin\RememberMe;
use MCNUser\Authentication\Result;
use MCNUser\Entity\AuthToken;
use MCNUser\Entity\User;
use MCNUser\Options\Authentication\Plugin\RememberMe as RememberMeOptions;
use MCNUserTest\TestAsset;
use Zend\Http\Client\Cookies;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;
use Zend\Http\Response;
use DateTime;

/**
 * Class RememberMeTest
 * @package MCNUserTest\Authentication\Plugin
 */
class RememberMeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Http\Request
     */
    protected $request;

    /**
     * @var \Zend\Http\Response
     */
    protected $response;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userService;

    /**
     * @var \MCNUser\Authentication\Plugin\RememberMe
     */
    protected $plugin;

    /**
     * @var \MCNUser\Options\Authentication\Plugin\RememberMe
     */
    protected $options;

    public function setUp()
    {
        $this->options = new RememberMeOptions(array(
            'entity_identity_property' => 'email'
        ));

        $this->request = new Request();
        $this->request->getHeaders()->addHeader(new Cookie(array('remember_me' => 'a coookie for |  you sir!')));

        $this->response = new Response();

        $this->service     = $this->getMock('MCNUser\Authentication\TokenServiceInterface');
        $this->userService = $this->getMock('MCNUser\Service\UserInterface');

        $this->plugin = new RememberMe($this->service, $this->response, $this->options);
    }

    /**
     * @expectedException \MCNUser\Authentication\Exception\DomainException
     */
    public function testThrowExceptionOnNoCookie()
    {
        $this->request->getHeaders()->clearHeaders();

        $this->plugin->authenticate($this->request, $this->userService);
    }

    public function testForNonExistingUserAccount()
    {
        $result = $this->plugin->authenticate($this->request, $this->userService);

        $this->assertEquals($result->getCode(), Result::FAILURE_IDENTITY_NOT_FOUND);
    }

    public function testForUsedAuthToken()
    {
        $this->userService
             ->expects($this->once())
             ->method('getOneBy')
             ->will($this->returnValue(new TestAsset\Authentication\AuthTokenOwnerEntity()));

        $this->service
             ->expects($this->once())
             ->method('useAndConsume')
             ->will($this->throwException(new TokenIsConsumedException()));

        $result = $this->plugin->authenticate($this->request, $this->userService);

        $this->assertEquals(Result::FAILURE_UNCATEGORIZED, $result->getCode());
        $this->assertEquals('Token has already been consumed.', $result->getMessage());
    }

    public function testForInvalidToken()
    {
        $this->userService
            ->expects($this->once())
            ->method('getOneBy')
            ->will($this->returnValue(new TestAsset\Authentication\AuthTokenOwnerEntity()));

        $this->service
            ->expects($this->once())
            ->method('useAndConsume')
            ->will($this->throwException(new TokenNotFoundException()));


        $result = $this->plugin->authenticate($this->request, $this->userService);

        $this->assertEquals(Result::FAILURE_UNCATEGORIZED, $result->getCode());
        $this->assertEquals('Token was not found.', $result->getMessage());
    }

    public function testForPassedExpirationDate()
    {
        $this->userService
            ->expects($this->once())
            ->method('getOneBy')
            ->will($this->returnValue(new TestAsset\Authentication\AuthTokenOwnerEntity()));

        $this->service
            ->expects($this->once())
            ->method('useAndConsume')
            ->will($this->throwException(new TokenHasExpiredException()));

        $result = $this->plugin->authenticate($this->request, $this->userService);

        $this->assertEquals(Result::FAILURE_UNCATEGORIZED, $result->getCode());
        $this->assertEquals('Token has expired.', $result->getMessage());
    }

    public function testSuccessfulLogin()
    {

        $token = new AuthToken();
        $token->prePersist();
        $token->setToken('hello.world');
        $token->setOwner(1);

        $this->userService
            ->expects($this->once())
            ->method('getOneBy')
            ->will($this->returnValue(new TestAsset\Authentication\AuthTokenOwnerEntity()));

        $this->service
            ->expects($this->once())
            ->method('useAndConsume');

        $this->service
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($token));

        $result = $this->plugin->authenticate($this->request, $this->userService);


        /**
         * @var $cookie \Zend\Http\Header\SetCookie
         */
        $cookie = $this->response->getHeaders()->get('set-cookie')[0];

        $this->assertEquals($cookie->getName(), 'remember_me');
        $this->assertEquals(Result::SUCCESS, $result->getCode());
    }
}
