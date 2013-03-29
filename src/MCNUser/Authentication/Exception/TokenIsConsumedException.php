<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication\Exception;

/**
 * Class TokenAlreadyConsumedException
 * @package MCNUser\Service\User\Exception
 */
class TokenIsConsumedException extends DomainException implements ExceptionInterface
{
    public function __construct()
    {
        parent::__construct('The token has been consumed and can no longer be used.');
    }
}
