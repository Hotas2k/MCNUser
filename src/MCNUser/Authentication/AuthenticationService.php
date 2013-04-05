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

namespace MCNUser\Authentication;

use MCNStdlib\Interfaces\UserServiceInterface;
use Zend\Authentication\Storage\Session;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventsCapableInterface;
use Zend\Http\Request;
use MCNUser\Options\Authentication\AuthenticationOptions as Options;

/**
 * Class AuthenticationService
 * @package MCNUser\Authentication
 */
class AuthenticationService implements EventsCapableInterface
{
    /**
     * @var \Zend\EventManager\EventManager
     */
    protected $evm;

    /**
     * @var \Zend\Authentication\Storage\Session
     */
    protected $storage;

    /**
     * @var \MCNUser\Options\Authentication\AuthenticationOptions
     */
    protected $options;

    /**
     * @var PluginManager
     */
    protected $pluginManager;

    /**
     * @param \MCNStdlib\Interfaces\UserServiceInterface            $service
     * @param \MCNUser\Options\Authentication\AuthenticationOptions $options
     * @param PluginManager                                         $pluginManager
     */
    public function __construct(UserServiceInterface $service, Options $options = null, PluginManager $pluginManager = null)
    {
        $this->service       = $service;
        $this->storage       = new Session();
        $this->options       = ($options === null) ? new Options() : $options;
        $this->pluginManager = ($pluginManager === null) ? new PluginManager() : $pluginManager;
    }

    /**
     * Get the plugin manager
     *
     * @return \MCNUser\Authentication\PluginManager
     */
    public function getPluginManager()
    {
        return $this->pluginManager;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return \Zend\EventManager\EventManagerInterface
     */
    public function getEventManager()
    {
        if ($this->evm === null) {

            $this->evm = new EventManager();
            $this->evm->setEventClass('MCNUser\Authentication\AuthEvent');
        }

        return $this->evm;
    }

    /**
     * Get the options for authentication
     *
     * @return \MCNUser\Options\Authentication\AuthenticationOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Authenticate a request
     *
     * @see \MCNUser\Authentication\AuthEvent for available events to hook into
     *
     * @param \Zend\Http\Request $request
     * @param string             $plugin
     *
     * @throws Exception\DomainException If an attempt to authenticate via a unregistered plugin occurs
     *
     * @return Result
     */
    public function authenticate(Request $request, $plugin = 'standard')
    {
        if (! $this->getPluginManager()->has($plugin)) {

            throw new Exception\DomainException(
                sprintf('The plugin %s has not been registered with the plugin manager', $plugin)
            );
        }

        /**
         * @var $plugin \MCNUser\Authentication\Plugin\AbstractPlugin
         */
        $plugin = $this->getPluginManager()->get($plugin);

        $result = $plugin->authenticate($request, $this->service);

        if ($result->getCode() == Result::SUCCESS) {

            $response = $this->getEventManager()->trigger(
                AuthEvent::EVENT_AUTH_SUCCESS,
                $result->getIdentity(),
                compact('plugin', 'request')
            );

            if (! $response->stopped()) {

                $this->storage->write($result->getIdentity());

            } else {

                $result->setCode(Result::FAILURE_UNCATEGORIZED);
                $result->setMessage($response->last());
            }
        }

        // Do not use a else on the previous if statement
        // Because if the event loop fails and we use an else this will not be triggered
        if ($result->getCode() != Result::SUCCESS) {

            $this->getEventManager()->trigger(AuthEvent::EVENT_AUTH_FAILURE, $result, compact('plugin', 'request'));
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->storage->read();
    }

    /**
     * @return bool
     */
    public function hasIdentity()
    {
        return !$this->storage->isEmpty();
    }

    /**
     * Remove the current identity
     */
    public function clearIdentity()
    {
        $this->getEventManager()->trigger(AuthEvent::EVENT_LOGOUT, $this->getIdentity());
        $this->storage->clear();
    }
}
