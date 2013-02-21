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
use Zend\View\Model\JsonModel;

/**
 * Class AuthenticationController
 *
 * @method \MCN\Controller\Plugin\Http http
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
        if ($this->http()->acceptsMimeType('application/json')) {

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
