<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication\Plugin;

use MCNUser\Service\UserInterface;
use Zend\Http\Request as HttpRequest;

class RememberMe implements PluginInterface
{
    public function __construct(AuthTokenService $service)
    {

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
