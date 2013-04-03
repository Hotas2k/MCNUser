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

namespace MCNUser\Factory;

use MCNUser\Authentication\AuthenticationService;
use MCNUser\Authentication\PluginManager;
use MCNUser\Options\Authentication\AuthenticationOptions;
use MCNUser\Service\UserInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AuthenticationServiceFactory
 * @package MCNUser\Authentication
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * Create an instance of the authentication service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $sl
     *
     * @throws Exception\InvalidArgumentException When an unknown name/alias for the user service is provided
     * @throws Exception\RuntimeException         If no configuration has been specified.
     * @throws Exception\LogicException           When the instance of the user service does not
     *                                            implement the specified class
     *
     * @return \MCNUser\Authentication\AuthenticationService|mixed
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        $options = $sl->get('mcn.options.user.authentication');

        if (! $sl->has($options->getUserServiceSlKey())) {

            throw new Exception\InvalidArgumentException(
                sprintf('Unknown name/alias %s for user service implementation', $options->getUserServiceSlKey())
            );
        }

        $userService = $sl->get($options->getUserServiceSlKey());

        if (! $userService instanceof UserInterface) {

            throw new Exception\LogicException(
                sprintf(
                    'The user service implemented provided, class: %s, sl_key: %s ' .
                    'must implement MCNUser\Service\UserServiceInterface',
                    get_class($userService),
                    $options->getUserServiceSlKey()
                )
            );
        }

        $pluginManager = new PluginManager();

        /**
         * @var $alias         string
         * @var $pluginOptions \MCNUser\Options\Authentication\Plugin\Standard
         */
        foreach ($options->getPlugins() as $alias => $pluginOptions) {

            $pluginManager->setFactory($alias, function() use ($sl, $pluginOptions) {

                $plugin = $sl->get($pluginOptions->getServiceManagerAlias());
                $plugin->setOptions($pluginOptions);

                return $plugin;
            });
        }

        $service = new AuthenticationService($userService, $options, $pluginManager);

        foreach ($options->getListeners() as $listener) {

            if (is_string($listener)) {

                $listener = $sl->get($listener);
            }

            $service->getEventManager()->attach($listener);
        }

        return $service;
    }
}
