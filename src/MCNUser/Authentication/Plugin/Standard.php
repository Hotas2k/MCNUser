<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication\Plugin;

use MCN\Stdlib\NamingConvention;
use MCNUser\Authentication\Result;
use MCNUser\Service\UserInterface;
use Zend\Crypt\Password\Bcrypt;
use Zend\Http\Request as HttpRequest;
use MCNUser\Options\Authentication\Plugin\Standard as Options;

/**
 * Class Standard
 * @package MCNUser\Authentication\Plugin
 */
class Standard extends AbstractPlugin
{
    /**
     * @param \MCNUser\Options\Authentication\Plugin\Standard $options
     */
    public function __construct(Options $options = null)
    {
        $this->options = ($options === null) ? new Options : $options;
    }

    /**
     * Authenticate the request
     *
     * Authenticate the user against the current http request
     *
     * @param \Zend\Http\Request             $request
     * @param \MCNUser\Service\UserInterface $service
     *
     * @return \MCNUser\Authentication\Result|void
     */
    public function authenticate(HttpRequest $request, UserInterface $service)
    {
        $identity   = $request->getPost($this->options->getHttpIdentityField());
        $credential = $request->getPost($this->options->getHttpCredentialField());

        $user = $service->getOneBy($this->options->getEntityIdentityProperty(), $identity);

        $bcrypt = new Bcrypt(array(
            'salt' => $this->options->getBcryptSalt(),
            'cost' => $this->options->getBcryptCost()
        ));

        if (! $user) {

            return Result::create(Result::FAILURE_IDENTITY_NOT_FOUND, null, Result::MSG_IDENTITY_NOT_FOUND);
        }

        $method = NamingConvention::toCamelCase('get_' . $this->options->getEntityCredentialProperty());

        if (! $bcrypt->verify($credential, $user->$method())) {

            return Result::create(Result::FAILURE_INVALID_CREDENTIAL, $user, Result::MSG_INVALID_CREDENTIAL);
        }

        return Result::create(Result::SUCCESS, $user);
    }
}
