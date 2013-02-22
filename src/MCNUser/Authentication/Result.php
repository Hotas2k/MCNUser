<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication;

use MCNUser\Entity\UserInterface;
use Zend\Stdlib\AbstractOptions;

class Result extends AbstractOptions
{
    const SUCCESS = 1;
    const FAILURE_DISABLED_PLUGIN    = -4;
    const FAILURE_IDENTITY_NOT_FOUND = -1;
    const FAILURE_INVALID_CREDENTIAL = -2;
    const FAILURE_UNCATEGORIZED      = -3;

    const MSG_INVALID_CREDENTIAL = 'Wrong identity or credential specified.';
    const MSG_IDENTITY_NOT_FOUND = 'No entity with the given identity was found.';

    /**
     * @var integer
     */
    protected $code;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var mixed
     */
    protected $identity;

    /**
     * Create a new instance
     *
     * @param integer $code
     * @param mixed   $identity
     * @param string  $message
     *
     * @return static
     */
    public static function create($code, UserInterface $identity = null, $message = '')
    {
        return new static(array(
            'code'     => $code,
            'message'  => $message,
            'identity' => $identity
        ));
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param integer $code
     * @throws Exception\InvalidArgumentException
     */
    public function setCode($code)
    {
        if (!is_int($code) || $code > static::SUCCESS || $code < static::FAILURE_UNCATEGORIZED) {

            throw new Exception\OutOfBoundsException(
                sprintf('Illegal error code %d provided', $code)
            );
        }

        $this->code = $code;

        return $this;
    }

    /**
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param mixed $identity
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
    }
}
