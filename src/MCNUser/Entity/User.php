<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use MCN\Object\Entity\AbstractEntity;
use MCN\Object\Entity\Behavior\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use MCNUser\Authentication\TokenConsumerInterface;

/**
 * Class User
 * @package MCNUser\Entity
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class User extends AbstractEntity implements UserInterface
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
     * @ORM\Column(type="string", length=60)
     */
    protected $password;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $activated = false;

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
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MCNUser\Entity\AuthToken")
     * @ORM\JoinTable(name="mcn_user_auth_tokens_reference",
     *  joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="token_id", referencedColumnName="id", unique=true)}
     * )
     */
    protected $auth_tokens;

    /**
     * Construct a new user instance
     */
    public function __construct()
    {
        $this->auth_tokens = new ArrayCollection();
    }

    /**
     * (PHP 5 >= 5.4.0)
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link http://docs.php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed Returns data which can be serialized by json_encode(), which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return array(
            'id'            => $this->id,
            'email'         => $this->email,
            'created_at'    => $this->created_at,
            'last_login_ip' => $this->last_login_ip,
            'last_login_at' => $this->last_login_at
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\MCNUser\Entity\AuthToken>
     */
    public function getAuthTokens()
    {
        return $this->auth_tokens;
    }

    /**
     * @return bool
     */
    public function isActivated()
    {
        return $this->activated;
    }

    /**
     * @param bool $activated
     */
    public function setActivated($activated)
    {
        $this->activated = (bool) $activated;
    }

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
     * @param \DateTime $last_login_at
     */
    public function setLastLoginAt(DateTime $last_login_at)
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
