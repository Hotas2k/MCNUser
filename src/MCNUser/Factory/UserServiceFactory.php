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

use MCNUser\Options\UserOptions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class UserServiceFactory
 * @package MCNUser\Factory
 */
class UserServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $sl
     *
     * @throws Exception\LogicException When a listener does not exist in the service locator
     * @return \MCNStdlib\Interfaces\UserServiceInterface
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        $options       = $sl->get('mcn.options.user.service');
        $className     = $options->getServiceClass();
        $objectManager = $sl->get('doctrine.entitymanager.ormdefault');

        /**
         * @var $instance \MCNStdlib\Interfaces\UserServiceInterface
         */
        $instance = new $className($objectManager, $options);

        if (method_exists($instance, 'setSearchService') && $options->getSearchServiceSlKey() !== null) {

            if (! $sl->has($options->getSearchServiceSlKey())) {

                throw new Exception\LogicException(
                    sprintf(
                        'Invalid service locator key "%s" specified for the search service',
                        $options->getSearchServiceSlKey()
                    )
                );
            }

            $instance->setSearchService($sl->get($options->getSearchServiceSlKey()));
        }

        foreach ($options->getListeners() as $alias) {

            if (! $sl->has($alias)) {

                throw new Exception\LogicException(
                    sprintf('The service locator has no service with the name/alias %s', $alias)
                );
            }

            $instance->getEventManager()->attach(
                $sl->get($alias)
            );
        }

        return $instance;
    }
}
