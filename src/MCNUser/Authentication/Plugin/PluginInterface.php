<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication\Plugin;

use Zend\Http\Request as HttpRequest;
use MCNUser\Service\UserInterface;
use Zend\Stdlib\AbstractOptions;

/**
 * Interface PluginInterface
 * @package MCNUser\Authentication\Plugin
 */
interface PluginInterface
{
    /**
     * @param \Zend\Http\Request $request
     * @param \MCNUser\Service\UserInterface $service
     * @return \MCNUser\Authentication\Result
     */
    public function authenticate(HttpRequest $request, UserInterface $service);
}
