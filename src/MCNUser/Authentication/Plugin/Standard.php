<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication\Plugin;

use MCNUser\Authentication\Result;
use MCNUser\Service\UserInterface;
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

        if ($this->options->getCredentialTreatment() !== null) {

            // apply credential treatment
            $credential = call_user_func_array($this->options->getCredentialTreatment(), array($credential));
        }

        $user = $service->getOneBy($this->options->getEntityIdentityProperty(), $identity);

        if (! $user) {

            return Result::create(Result::FAILURE_IDENTITY_NOT_FOUND, null, Result::MSG_IDENTITY_NOT_FOUND);
        }

        if (strcmp($user[$this->options->getEntityCredentialProperty()], $credential) != 0) {

            return Result::create(Result::FAILURE_INVALID_CREDENTIAL, $user, Result::MSG_INVALID_CREDENTIAL);
        }

        return Result::create(Result::SUCCESS, $user);
    }
}
