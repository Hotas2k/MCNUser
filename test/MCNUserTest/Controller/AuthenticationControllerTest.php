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

namespace MCNUserTest\Controller;

use Exception;
use MCNUser\Authentication\Result;
use MCNUser\Controller\AuthenticationController;
use MCNUser\Entity\User;
use MCNUserTest\Util\ServiceManagerFactory;
use Zend\Http\Header\Accept;
use Zend\Http\Header\GenericHeader;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Literal;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;

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

        $this->userService = $this->getMock('MCNStdlib\Interfaces\UserServiceInterface');
        $this->authService = $this->getMock(
            'MCNUser\Authentication\AuthenticationService',
            array('authenticate', 'clearIdentity'),
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
        $this->controller->setPluginManager($serviceManager->get('controllerpluginmanager'));
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
            ->expects($this->any())
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

    public function testLogout()
    {
        $this->routeMatch->setParam('action', 'logout');

        $this->authService
            ->expects($this->once())
            ->method('clearIdentity');

        $this->authService->getOptions()->setLogoutRoute('home');

        $response = $this->controller->dispatch($this->request);

        $this->assertInstanceOf('Zend\Http\Response', $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/', $response->getHeaders()->get('location')->getFieldValue());
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

    public function testJsonResponse()
    {
        $this->routeMatch->setParam('action', 'authenticate');

        $this->request->getHeaders()->addHeaders(array(
            Accept::fromString('Accept: application/json'),
            GenericHeader::fromString('X_REQUESTED_WITH: XMLHttpRequest')
        ));

        $user = new User();
        $user->setId(1);
        $user->setEmail('hello@world.com');

        $authResult = Result::create(Result::SUCCESS, $user);

        $this->authService
            ->expects($this->once())
            ->method('authenticate')
            ->withAnyParameters()
            ->will($this->returnValue($authResult));

        $response = $this->controller->dispatch($this->request);


        $user = new User();
        $user->fromArray(array(
            'id' => 1,
            'email' => 'hello@world.com'
        ));

        $this->assertInstanceOf('Zend\View\Model\JsonModel', $response);
        $this->assertEquals(
            array(
                'code' => 1,
                'message' => '',
                'identity' => $user
            ),
            $response->getVariables()
        );
    }

    public function testJsonResponseDoesNotContainIdentityOnFailedLogin()
    {
        $this->request->getHeaders()->addHeaders(array(
            Accept::fromString('Accept: application/json'),
            GenericHeader::fromString('X_REQUESTED_WITH: XMLHttpRequest')
        ));

        $this->routeMatch->setParam('action', 'authenticate');

        $authResult = Result::create(Result::FAILURE_INVALID_CREDENTIAL, null, Result::MSG_INVALID_CREDENTIAL);

        $this->authService
            ->expects($this->once())
            ->method('authenticate')
            ->withAnyParameters()
            ->will($this->returnValue($authResult));

        $response = $this->controller->dispatch($this->request);

        $this->assertInstanceOf('Zend\View\Model\JsonModel', $response);
        $this->assertNull($response->getVariable('identity'));
    }
}
