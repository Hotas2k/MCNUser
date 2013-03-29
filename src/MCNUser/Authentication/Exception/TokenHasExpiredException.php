<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication\Exception;

/**
 * Class TokenHasExpiredException
 * @package MCNUser\Service\User\Exception
 */
class TokenHasExpiredException extends RuntimeException implements ExceptionInterface
{
    public function __construct()
    {
        parent::__construct('The token has already expired.');
    }
}
