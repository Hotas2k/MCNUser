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
     * @return \Zend\Http\Response
     */
    public function authenticateAction()
    {
        $plugin = $this->params('plugin', 'standard');
        $return = $this->params('return', null);

        try {

            $result = $this->service->authenticate($this->getRequest(), $plugin);

            if ($result->getCode() == Result::SUCCESS) {

                if ($this->service->getOptions()->isEnableRedirection() && $return) {

                    return $this->redirect()->toUrl($return);
                }

                $route = $this->service->getOptions()->getSuccessfulLoginRoute();


                if (! $route) {

                    throw new AuthenticationException\DomainException('No successful login route has been specified');
                }

                return $this->redirect()->toRoute($route);

            } else {

                $this->flashMessenger()->addErrorMessage($result->getMessage());
            }

        } catch (AuthenticationException\DomainException $e) {

            // todo: add logging

            // return with a invalid page
            return $this->getResponse()->setStatusCode(500);
        }
    }

    public function logoutAction()
    {

    }

    public function changePasswordAction()
    {

    }
}
