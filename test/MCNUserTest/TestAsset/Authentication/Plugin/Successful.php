<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\TestAsset\Authentication\Plugin;

use MCNUser\Authentication\Plugin\PluginInterface;
use MCNUser\Authentication\Result;
use MCNUser\Service\UserInterface;
use Zend\Http\Request as HttpRequest;

class Successful implements PluginInterface
{
    /**
     * @param \Zend\Http\Request $request
     * @param \MCNUser\Service\UserInterface $service
     * @return \MCNUser\Authentication\Result
     */
    public function authenticate(HttpRequest $request, UserInterface $service)
    {
        return Result::create(Result::SUCCESS, $service->getByEmail('hello@world.com'));
    }
}
