<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\Authentication\Plugin;

use MCNUser\Authentication\Plugin\RememberMe;
use MCNUser\Authentication\Result;
use MCNUser\Options\Authentication\Plugin\RememberMe as RememberMeOptions;
use MCNUserTest\TestAsset;
use Zend\Http\Client\Cookies;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class RememberMeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \MCNUser\Authentication\Plugin\RememberMe
     */
    protected $plugin;

    /**
     * @var \MCNUser\Authentication\TokenServiceInterface
     */
    protected $service;

    /**
     * @var \MCNUser\Options\Authentication\Plugin\RememberMe
     */
    protected $options;

    /**
     * @var \MCNUser\Service\UserInterface
     */
    protected $userService;

    /**
     * @var \Zend\Http\Request
     */
    protected $request;

    public function setUp()
    {
        $this->options = new RememberMeOptions(array(
            'entity_identity_property' => 'email'
        ));

        $this->request = new Request();
        $this->request->getHeaders()->addHeader(new Cookie());

        $this->service     = new TestAsset\AuthTokenService();
        $this->userService = new TestAsset\UserService();

        $this->plugin = new RememberMe($this->service, $this->options);
    }

    /**
     * @expectedException \MCNUser\Authentication\Exception\DomainException
     */
    public function testThrowExceptionOnNoCookie()
    {
        $this->plugin->authenticate($this->request, $this->userService);
    }

    public function testForNonExistingUserAccount()
    {
        $this->request->getCookie()->remember_me = 'i do not exist|token';

        $result = $this->plugin->authenticate($this->request, $this->userService);

        $this->assertEquals($result->getCode(), Result::FAILURE_IDENTITY_NOT_FOUND);
    }

    public function testForUsedAuthToken()
    {
        $this->request->getCookie()->remember_me = 'hello@world.com|already-used';

        $result = $this->plugin->authenticate($this->request, $this->userService);

        $this->assertEquals(Result::FAILURE_UNCATEGORIZED, $result->getCode());
        $this->assertEquals('Token has already been consumed.', $result->getMessage());
    }

    public function testForInvalidToken()
    {
        $this->request->getCookie()->remember_me = 'hello@world.com|not-found';

        $result = $this->plugin->authenticate($this->request, $this->userService);

        $this->assertEquals(Result::FAILURE_UNCATEGORIZED, $result->getCode());
        $this->assertEquals('Token was not found.', $result->getMessage());
    }

    public function testForPassedExpirationDate()
    {
        $this->request->getCookie()->remember_me = 'hello@world.com|has-expired';

        $result = $this->plugin->authenticate($this->request, $this->userService);

        $this->assertEquals(Result::FAILURE_UNCATEGORIZED, $result->getCode());
        $this->assertEquals('Token has expired.', $result->getMessage());
    }

    public function testSuccessfulLogin()
    {
        $this->request->getCookie()->remember_me = 'hello@world.com|success';

        $result = $this->plugin->authenticate($this->request, $this->userService);

        $this->assertEquals(Result::SUCCESS, $result->getCode());
    }
}
