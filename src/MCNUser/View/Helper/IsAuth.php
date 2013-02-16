<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\View\Helper;

use MCNUser\Authentication\AuthenticationService;
use Zend\View\Helper\AbstractHelper;

class IsAuth extends AbstractHelper
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
}