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

namespace MCNUser;

use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Request as HttpRequest;

/**
 * Class Module
 *
 * @package MCNUser
 */
class Module
{
    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();

        $e->getApplication()->getEventManager()->attach(
            $sm->get('mcn.listener.user.authentication.remember-me-auth-trigger')
        );
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            )
        );
    }

    /**
     * @return array
     */
    public function getControllerConfig()
    {
        return array(
            'factories' => array(

                'mcn.user.authentication' => function(ControllerManager $sm) {

                    return new Controller\AuthenticationController(
                        $sm->getServiceLocator()->get('mcn.service.user.authentication')
                    );
                }
            )
        );
    }

    /**
     * @return array
     */
    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(

                'auth' => function($sm) {

                    return new Controller\Plugin\Auth(
                        $sm->getServiceLocator()->get('mcn.service.user.authentication')
                    );
                }
            )
        );
    }

    /**
     * @return array
     */
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'isAuth' => function ($sm) {
                    return new View\Helper\IsAuth($sm->getServiceLocator()->get('mcn.service.user.authentication'));
                },
            ),
        );

    }

    /**
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(

                'mcn.authentication.plugin.remember-me' => function(ServiceLocatorInterface $sm) {

                    return new Authentication\Plugin\RememberMe(
                        $sm->get('mcn.service.user.authentication.token'),
                        $sm->get('response')
                    );
                },

                'mcn.listener.user.authentication.remember-me-auth-trigger' => function(ServiceLocatorInterface $sm) {

                    return new Listener\Authentication\RememberMe\AuthTrigger(
                        $sm->get('mcn.service.user.authentication')
                    );
                },

                'mcn.listener.user.authentication.remember-me-cookie-handler' => function(ServiceLocatorInterface $sm) {

                    return new Listener\Authentication\RememberMe\CookieHandler(
                        $sm->get('mcn.service.user.authentication.token'),
                        $sm->get('response'),
                        $sm->get('MCNUser\Options\Authentication\Plugin\RememberMe')
                    );
                },

                'mcn.listener.user.authentication.update-login' => function(ServiceLocatorInterface $sm) {

                    return new Listener\Authentication\LastLogin(
                        $sm->get('mcn.service.user')
                    );
                },

                'mcn.service.user.authentication.token' => function(ServiceLocatorInterface $sm) {

                    return new Authentication\TokenService($sm->get('doctrine.entitymanager.ormdefault'));
                },

                'identity' => function(ServiceLocatorInterface $sm) {

                    /**
                     * @var $userService \MCNUser\Service\User
                     * @var $authService \MCNUser\Authentication\AuthenticationService
                     */
                    $userService = $sm->get('mcn.service.user');
                    $authService = $sm->get('mcn.service.user.authentication');

                    if ($authService->hasIdentity()) {

                        return $userService->getById($authService->getIdentity()->getId());
                    }

                    return null;
                }
            )
        );
    }
}
