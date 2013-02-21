<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication;

use Zend\EventManager\Event;

/**
 * Class AuthEvent
 * @package MCNUser\Authentication
 */
class AuthEvent extends Event
{
    const EVENT_AUTH_SUCCESS = 'authenticate.success';
    const EVENT_AUTH_FAILURE = 'authenticate.failure';
}
