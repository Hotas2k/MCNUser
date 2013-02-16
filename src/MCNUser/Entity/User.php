<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Entity;

use MCN\Object\Entity\AbstractEntity;
use MCN\Object\Entity\Behavior\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class User
 * @package MCNUser\Entity
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class User extends AbstractEntity
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
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=40)
     */
    protected $password;

    /**
     * Last known ip-address
     *
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $last_login_ip;

    /**
     * DateTime of last login
     *
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $last_login_at;

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
     * @param \DateTime|null $last_login_at
     */
    public function setLastLoginAt($last_login_at)
    {
        $this->last_login_at = $last_login_at;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastLoginAt()
    {
        return $this->last_login_at;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getLastLoginIp()
    {
        return $this->last_login_ip;
    }

    /**
     * @param string $last_login_ip
     */
    public function setLastLoginIp($last_login_ip)
    {
        $this->last_login_ip = $last_login_ip;
    }
}
