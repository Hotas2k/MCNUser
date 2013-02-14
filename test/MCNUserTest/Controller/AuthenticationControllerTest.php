<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\Controller;

use MCNUser\Authentication\Result;
use MCNUser\Controller\AuthenticationController;
use MCNUserTest\Authentication\TestAsset\UserService;
use MCNUserTest\Bootstrap;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Literal;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Stdlib\Parameters;

class AuthenticationControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $controller;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;
    protected $authService;

    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('mcn.service.user', new UserService);

        $this->authService = $serviceManager->get('mcn.service.user.authentication');
        $this->controller = new AuthenticationController($this->authService);
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'mcn.authentication'));
        $this->event      = new MvcEvent();
        $config = $serviceManager->get('Config');
        $routerConfig = isset($config['router']) ? $config['router'] : array();

        // setup a router
        $router = HttpRouter::factory($routerConfig);
        $router->addRoute('home', new Literal('/'), array('controller' => 'index'));



        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
    }

    public function testAuthenticationThrowExceptionOnNoSuccessfulLoginRoute()
    {
        $this->routeMatch->setParam('action', 'authenticate');

        $this->request->setPost(
            new Parameters(array(
                'identity' => 'hello@world.com',
                'credential' => 'password'
            ))
        );

        $options = $this->authService->getOptions();
        $options->setSuccessfulLoginRoute(null);


        $response = $this->controller->dispatch($this->request);

        $this->assertTrue($response instanceof Response);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testStatusCode500ForIllegalPlugin()
    {
        $this->routeMatch->setParam('action', 'authenticate');
        $this->routeMatch->setParam('plugin', 'a plugin that does not exist');

        $this->controller->dispatch($this->request);

        $response = $this->controller->getResponse();

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testSuccessfulLoginRedirect()
    {
        $this->routeMatch->setParam('action', 'authenticate');

        $this->request->setPost(
            new Parameters(array(
                'identity' => 'hello@world.com',
                'credential' => 'password'
            ))
        );


        // set the successful login route
        $options = $this->authService->getOptions();
        $options->setSuccessfulLoginRoute('home');

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals('/', $result->getHeaders()->get('location')->getUri());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testSuccessfulLoginRedirectToReturnPosition()
    {
        $this->routeMatch->setParam('action', 'authenticate');
        $this->routeMatch->setParam('return', '/hello/world');

        $this->request->setPost(
            new Parameters(array(
                'identity' => 'hello@world.com',
                'credential' => 'password'
            ))
        );

        $result = $this->controller->dispatch($this->request);

        $this->assertEquals('/hello/world', $result->getHeaders()->get('location')->getUri());
    }

    public function testErrorMessageOnUnsuccessfulLogin()
    {
        $this->routeMatch->setParam('action', 'authenticate');

        $this->request->setPost(
            new Parameters(array(
                'identity' => 'wrong email',
                'credential' => 'password'
            ))
        );

        $this->controller->dispatch($this->request);


        $this->assertTrue(
            in_array(Result::MSG_IDENTITY_NOT_FOUND, $this->controller->flashMessenger()->getCurrentErrorMessages())
        );

        $this->request->setPost(
            new Parameters(array(
                'identity' => 'hello@world.com',
                'credential' => 'wrong password'
            ))
        );

        $this->controller->dispatch($this->request);

        $this->assertTrue(
            in_array(Result::MSG_INVALID_CREDENTIAL, $this->controller->flashMessenger()->getCurrentErrorMessages())
        );
    }
}
