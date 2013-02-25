<?php
/**
 * @Author Jonas Eriksson <jonas@pmg.se>
 * Date: 2/22/13
 * Time: 1:46 PM
 */

namespace MCNUser\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use MCNUser\Authentication\AuthenticationService;

/**
 * Class Auth
 * @package MCNUser\Controller\Plugin
 */
class Auth extends AbstractPlugin
{
    /**
     * @var \MCNUser\Authentication\AuthenticationService
     */
    protected $authService;

    /**
     * @param \MCNUser\Authentication\AuthenticationService $authService
     */
    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @return $this
     */
    public function __invoke()
    {
        return $this;
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
