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
namespace MCNUser\Controller;

use MCNUser\Authentication\AuthenticationService;
use MCNUser\Authentication\Result;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

/**
 * Class AuthenticationController
 *
 * @method \Zend\Http\Request getRequest
 * @method \MCNStdlib\Controller\Plugin\Http http
 *
 * @package MCNUser\Controller
 */
class AuthenticationController extends AbstractActionController
{
    /**
     * @var \MCNUser\Authentication\AuthenticationService
     */
    protected $service;

    /**
     * Constructor
     *
     * @param \MCNUser\Authentication\AuthenticationService $service
     */
    public function __construct(AuthenticationService $service)
    {
        $this->service = $service;
    }

    /**
     * Authentication action
     *
     * @throws Exception\MissingRouteException
     *
     * @return \Zend\Http\Response|\Zend\View\Model\JsonModel
     */
    public function authenticateAction()
    {
        $return   = $this->params('return');
        $plugin   = $this->params('plugin', 'standard');

        $result = $this->service->authenticate($this->getRequest(), $plugin);

        // short circuit on ajax request
        if ($this->http()->acceptsMimeType('application/json') && $this->getRequest()->isXmlHttpRequest()) {

            $data = $result->toArray();

            if ($result->getCode() != Result::SUCCESS) {

                unset($data['identity']);
            }

            return new JsonModel($data);
        }

        if ($result->getCode() == Result::SUCCESS) {

            if ($this->service->getOptions()->isEnabledRedirection() && $return) {

                return $this->redirect()->toUrl($return);
            }

            $route = $this->service->getOptions()->getSuccessfulLoginRoute();

            if (! $route) {

                throw new Exception\MissingRouteException('No successful login route has been specified');
            }

            return $this->redirect()->toRoute($route);

        } else {

            $this->flashMessenger()->addErrorMessage($result->getMessage());

            $route = $this->service->getOptions()->getFailedLoginRoute();

            if (! $route) {

                throw new Exception\MissingRouteException('No failed login route has been specified');
            }

            return $this->redirect()->toRoute($route);
        }
    }

    /**
     * Logout and redirect user
     *
     * @throws Exception\MissingRouteException
     *
     * @return \Zend\Http\Response
     */
    public function logoutAction()
    {
        $route = $this->service->getOptions()->getLogoutRoute();

        if (! $route) {

            throw new Exception\MissingRouteException('No logout route has been specified.');
        }

        $this->service->clearIdentity();

        return $this->redirect()->toRoute($route);
    }
}
