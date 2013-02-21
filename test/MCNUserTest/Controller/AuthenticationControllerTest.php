<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\Controller;

use DoctrineORMModuleTest\Util\ServiceManagerFactory;
use Exception;
use MCNUser\Authentication\Result;
use MCNUser\Controller\AuthenticationController;
use MCNUser\Entity\User;
use MCNUserTest\Bootstrap;
use MCNUserTest\TestAsset\Authentication\Plugin\Successful;
use MCNUserTest\TestAsset\UserService;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Literal;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Stdlib\Parameters;

class AuthenticationControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \MCNUser\Controller\AuthenticationController
     */
    protected $controller;

    /**
     * @var \Zend\Http\Request
     */
    protected $request;

    /**
     * @var \Zend\Http\Response
     */
    protected $response;

    /**
     * @var \Zend\Mvc\Router\RouteMatch
     */
    protected $routeMatch;

    /**
     * @var \Zend\Mvc\MvcEvent
     */
    protected $event;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $authService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userService;

    protected function setUp()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();

        $this->userService = $this->getMock('MCNUser\Service\UserInterface');
        $this->authService = $this->getMock(
            'MCNUser\Authentication\AuthenticationService',
            array('authenticate'),
            array($this->userService)
        );

        $this->controller = new AuthenticationController($this->authService);
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'mcn.authentication'));
        $this->event      = new MvcEvent();

        // setup a router
        $router = HttpRouter::factory();
        $router->addRoute('home', new Literal('/'), array('controller' => 'index'));

        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
    }

    public function testAuthenticateThrowExceptionOnMissingSuccessfulLoginRoute()
    {
        $this->routeMatch->setParam('action', 'authenticate');

        $authResult = new Result(array('code' => Result::SUCCESS));

        $this->authService
            ->expects($this->once())
            ->method('authenticate')
            ->withAnyParameters()
            ->will($this->returnValue($authResult));

        try {

            $this->controller->dispatch($this->request);

        } catch(Exception $e) {

            // DO NOT DO ANY TESTING IN CATCH
        }

        $this->assertInstanceOf('MCNUser\Controller\Exception\MissingRouteException', $e);
        $this->assertEquals('No successful login route has been specified', $e->getMessage());
    }

    public function testAuthenticateThrowExceptionOnMissingFailedLoginRoute()
    {
        $this->routeMatch->setParam('action', 'authenticate');

        $authResult = new Result(array('code' => Result::FAILURE_INVALID_CREDENTIAL));

        $this->authService
            ->expects($this->once())
            ->method('authenticate')
            ->withAnyParameters()
            ->will($this->returnValue($authResult));


        try {

            $this->controller->dispatch($this->request);

        } catch(Exception $e) {

            // DO NOT DO ANY TESTING IN CATCH
        }

        $this->assertInstanceOf('MCNUser\Controller\Exception\MissingRouteException', $e);
        $this->assertEquals('No failed login route has been specified', $e->getMessage());
    }

    /**
     * @expectedException \MCNUser\Controller\Exception\MissingRouteException
     */
    public function testLogoutThrowsExceptionOnMissingRoute()
    {
        $this->routeMatch->setParam('action', 'logout');
        $this->controller->dispatch($this->request);
    }

    public function testRedirectOnSuccessfulLogin()
    {
        $this->routeMatch->setParam('action', 'authenticate');

        $authResult = new Result(array('code' => Result::SUCCESS));

        $this->authService
            ->expects($this->once())
            ->method('authenticate')
            ->withAnyParameters()
            ->will($this->returnValue($authResult));

        $this->authService->getOptions()->setSuccessfulLoginRoute('home');

        /**
         * @var $response \Zend\Http\Response
         */
        $response = $this->controller->dispatch($this->request);

        $this->assertInstanceOf('Zend\Http\Response', $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/', $response->getHeaders()->get('location')->getFieldValue());
    }

    public function testFailedLogin()
    {
        $this->routeMatch->setParam('action', 'authenticate');

        $authResult = Result::create(Result::FAILURE_INVALID_CREDENTIAL, null, Result::MSG_INVALID_CREDENTIAL);

        $this->authService
            ->expects($this->once())
            ->method('authenticate')
            ->withAnyParameters()
            ->will($this->returnValue($authResult));

        $this->authService->getOptions()->setFailedLoginRoute('home');

        /**
         * @var $response \Zend\Http\Response
         */
        $response = $this->controller->dispatch($this->request);

        $this->assertInstanceOf('Zend\Http\Response', $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/', $response->getHeaders()->get('location')->getFieldValue());

        $this->assertContains(
            Result::MSG_INVALID_CREDENTIAL,
            $this->controller->flashMessenger()->getCurrentErrorMessages()
        );
    }

    public function testReturnOnSuccessfulLogin()
    {
        $this->routeMatch->setParam('action', 'authenticate');
        $this->routeMatch->setParam('return', '/hello/world');

        $authResult = new Result(array('code' => Result::SUCCESS));

        $this->authService
            ->expects($this->once())
            ->method('authenticate')
            ->withAnyParameters()
            ->will($this->returnValue($authResult));

        /**
         * @var $response \Zend\Http\Response
         */
        $response = $this->controller->dispatch($this->request);

        $this->assertInstanceOf('Zend\Http\Response', $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/hello/world', $response->getHeaders()->get('location')->getFieldValue());
    }
}
