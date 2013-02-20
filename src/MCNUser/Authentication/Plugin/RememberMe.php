<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication\Plugin;

use MCNUser\Authentication\Result;
use MCNUser\Options\Authentication\Plugin\RememberMe as Options;
use MCNUser\Authentication\TokenServiceInterface;
use MCNUser\Service\UserInterface;
use Zend\Http\Request as HttpRequest;
use MCNUser\Authentication\Exception;

/**
 * Class RememberMe
 * @package MCNUser\Authentication\Plugin
 */
class RememberMe implements PluginInterface
{
    /**
     * @var \MCNUser\Authentication\TokenServiceInterface
     */
    protected $service;

    /**
     * @var \MCNUser\Options\Authentication\Plugin\RememberMe
     */
    protected $options;

    /**
     * @param \MCNUser\Authentication\TokenServiceInterface $service
     * @param \MCNUser\Options\Authentication\Plugin\RememberMe $options
     */
    public function __construct(TokenServiceInterface $service, Options $options = null)
    {
        $this->service = $service;
        $this->options = $options;
    }

    /**
     *
     *
     * @param \Zend\Http\Request             $request
     * @param \MCNUser\Service\UserInterface $service
     *
     * @return \MCNUser\Authentication\Result
     */
    public function authenticate(HttpRequest $request, UserInterface $service)
    {
        if (! $request->getCookie() || !isSet($request->getCookie()->remember_me)) {

            throw new Exception\DomainException('No remember me cookie has been set');
        }

        list ($identity, $token) = explode('|', $request->getCookie()->remember_me);

        $user = $service->getOneBy($this->options->getEntityIdentityProperty(), $identity);

        if (! $user) {

            return Result::create(Result::FAILURE_IDENTITY_NOT_FOUND, null, Result::MSG_IDENTITY_NOT_FOUND);
        }

        try {

            $this->service->consumeAndRenewToken($user, $token);

        } catch (Exception\AlreadyConsumedException $e) {

            return Result::create(Result::FAILURE_UNCATEGORIZED, $user, 'Token has already been consumed.');

        } catch(Exception\ExpiredTokenException $e) {

            return Result::create(Result::FAILURE_UNCATEGORIZED, $user, 'Token has expired.');

        } catch(Exception\TokenNotFoundException $e) {

            return Result::create(Result::FAILURE_UNCATEGORIZED, $user, 'Token was not found.');
        }

        return Result::create(Result::SUCCESS, $user);
    }
}
