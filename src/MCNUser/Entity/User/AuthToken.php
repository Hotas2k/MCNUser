<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Entity\User;

use MCN\Object\Entity\AbstractEntity;

/**
 * Class AuthToken
 * @package MCNUser\Entity\User
 */
class AuthToken extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime")
     */
    protected $valid_until;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime")
     */
    protected $used_at;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $used_by_ip;
}
