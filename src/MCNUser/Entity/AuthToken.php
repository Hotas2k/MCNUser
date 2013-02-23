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
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $valid_until;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $used_at;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $used_by_ip;

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtOnPersist()
    {
        $this->created_at = new DateTime();
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @param \DateTime|null $used_at
     */
    public function setUsedAt(DateTime $used_at = null)
    {
        $this->used_at = $used_at;
    }

    /**
     * @return \DateTime|null
     */
    public function getUsedAt()
    {
        return $this->used_at;
    }

    /**
     * @param string $used_by_ip
     */
    public function setUsedByIp($used_by_ip)
    {
        $this->used_by_ip = $used_by_ip;
    }

    /**
     * @return string
     */
    public function getUsedByIp()
    {
        return $this->used_by_ip;
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
     * @return int
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param int $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }
}
