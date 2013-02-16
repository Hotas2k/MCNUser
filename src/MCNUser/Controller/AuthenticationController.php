<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Controller;

use MCNUser\Authentication\AuthenticationService;
use MCNUser\Authentication\Result;
use Zend\Mvc\Controller\AbstractActionController;
use MCNUser\Authentication\Exception as AuthenticationException;

/**
 * Class AuthenticationController
 *
 * @method \Zend\Http\Response getResponse
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
     * @throws \MCNUser\Authentication\Exception\InvalidArgumentException
     *
     * @return \Zend\Http\Response
     */
    public function authenticateAction()
    {
        $return = $this->params('return');
        $plugin = $this->params('plugin', 'standard');

        $result = $this->service->authenticate($this->getRequest(), $plugin);

        if ($result->getCode() == Result::SUCCESS) {

            if ($this->service->getOptions()->isEnableRedirection() && $return) {

                return $this->redirect()->toUrl($return);
            }

            $route = $this->service->getOptions()->getSuccessfulLoginRoute();

            if (! $route) {

                throw new AuthenticationException\InvalidArgumentException('No successful login route has been specified');
            }

            return $this->redirect()->toRoute($route);

        } else {

            $this->flashMessenger()->addErrorMessage($result->getMessage());

            $route = $this->service->getOptions()->getFailedLoginRoute();

            if (! $route) {

                throw new AuthenticationException\InvalidArgumentException('No failed login route has been specified');
            }

            return $this->redirect()->toRoute($route);
        }
    }

    /**
     * Logout and redirect user
     *
     * @throws \MCNUser\Authentication\Exception\InvalidArgumentException
     *
     * @return \Zend\Http\Response
     */
    public function logoutAction()
    {
        $route = $this->service->getOptions()->getLogoutRoute();

        if (! $route) {

            throw new AuthenticationException\InvalidArgumentException('No logout route has been specified.');
        }

        $this->service->clearIdentity();

        return $this->redirect()->toRoute($route);
    }
}