<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\Authentication\Plugin;

use MCNUser\Authentication\Plugin\RememberMe;
use MCNUserTest\TestAsset\AuthTokenService;
use Zend\Http\Client\Cookies;

class RememberMeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \MCNUser\Authentication\Plugin\RememberMe
     */
    protected $plugin;

    /**
     * @var \MCNUser\Service\AuthTokenInterface
     */
    protected $service;

    /**
     * @var \Zend\Http\Client\Cookies
     */
    protected $cookies;

    public function setUp()
    {
        $this->cookies = new Cookies();
        $this->service = new AuthTokenService();

        $this->plugin = new RememberMe($this->service, $this->cookies);
    }

    /**
     * @expectedException \MCNUser\Service\Exception\DomainException
     */
    public function testThrowExceptionOnNoCookie()
    {

    }

    public function testForUsedAuthToken()
    {

    }

    public function testForInvalidToken()
    {

    }

    public function testForPassedExpirationDate()
    {

    }

    public function testSuccessfulLogin()
    {

    }
}
