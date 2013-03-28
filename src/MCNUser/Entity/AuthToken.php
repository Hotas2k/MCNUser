<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use MCN\Object\Entity\AbstractEntity;
use MCN\Object\Entity\Behavior\TimestampableTrait;

/**
 * Class AuthToken
 * @package MCNUser\Entity\User
 *
 * @ORM\Table(name="mcn_user_auth_tokens")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class AuthToken extends AbstractEntity
{
    use TimestampableTrait;

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $owner;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $token;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $valid_until;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $consumed = false;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isConsumed()
    {
        return $this->consumed;
    }

    /**
     * @param bool $consumed
     */
    public function setConsumed($consumed)
    {
        $this->consumed = (bool) $consumed;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param \DateTime|null $valid_until
     */
    public function setValidUntil(DateTime $valid_until = null)
    {
        $this->valid_until = $valid_until;
    }

    /**
     * @return \DateTime|null
     */
    public function getValidUntil()
    {
        return $this->valid_until;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }
}
