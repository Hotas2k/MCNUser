<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication;

/**
 * Class AuthTokenConsumerInterface
 * @package MCNUser\Entity
 */
interface TokenConsumerInterface
{
    /**
     * @return mixed
     */
    public function getId();
}
