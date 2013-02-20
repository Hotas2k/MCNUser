<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Entity;

use ArrayAccess;
use DateTime;

/**
 * Class UserInterface
 * @package MCNUser\Entity
 */
interface UserInterface extends ArrayAccess
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     *
     * @return void
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @param string $password
     * @return void
     */
    public function setPassword($password);

    /**
     * @return string
     */
    public function getLastLoginIp();

    /**
     * @param string $ip
     * @return void
     */
    public function setLastLoginIp($ip);

    /**
     * @return string
     */
    public function getLastLoginAt();

    /**
     * @param \DateTime $at
     * @return void
     */
    public function setLastLoginAt(DateTime $at);
}
