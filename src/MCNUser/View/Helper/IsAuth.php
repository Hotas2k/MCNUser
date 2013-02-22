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

/**
 * Class IsAuth
 * @package MCNUser\View\Helper
 */
class IsAuth extends AbstractHelper
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
     * @return \MCNUser\Entity\UserInterface|null
     */
    public function __invoke()
    {
        return $this->authService->hasIdentity() ? $this->authService->getIdentity() : null;
    }
}
