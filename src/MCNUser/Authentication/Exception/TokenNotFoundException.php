<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication\Exception;

/**
 * Class TokenNotFoundException
 * @package MCNUser\Authentication\Exception
 */
class TokenNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Token not found');
    }
}
