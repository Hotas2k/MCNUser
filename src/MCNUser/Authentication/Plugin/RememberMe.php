<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication\Plugin;

use MCNUser\Service\UserInterface;
use Zend\Http\Client\Cookies;
use Zend\Http\Request as HttpRequest;
use MCNUser\Service\User\AuthToken as AuthTokenService;

class RememberMe implements PluginInterface
{
    /**
     * @param \MCNUser\Service\User\AuthToken $service
     */
    public function __construct(AuthTokenServiceInterface $service, Cookies $cookies = null)
    {
        $this->service = $service;
        $this->cookies = ($cookies == null) ? new Cookies : $cookies;
    }

    /**
     * @param \Zend\Http\Request $request
     * @param \MCNUser\Service\UserInterface $service
     * @return \MCNUser\Authentication\Result
     */
    public function authenticate(HttpRequest $request, UserInterface $service)
    {
    }
}
