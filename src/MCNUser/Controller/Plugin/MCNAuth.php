<?php
/**
 * @Author Jonas Eriksson <jonas@pmg.se>
 * Date: 2/22/13
 * Time: 1:46 PM
 */

namespace MCNUser\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use MCNUser\Authentication\AuthenticationService;

class MCNAuth extends AbstractPlugin
{

    protected $authService;

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    public function __invoke()
    {
        if ($this->authService->hasIdentity()) {
            return $this->authService->getIdentity();
        } else {
            return false;
        }
    }

    /**
     * Proxy convenience method
     *
     * @return bool
     */
    public function hasIdentity()
    {
        return $this->authService->hasIdentity();
    }

    /**
     * Proxy convenience method
     *
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->authService->getIdentity();
    }
}
