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
use Zend\Http\Client\Cookies;
use Zend\Http\Header\SetCookie;
use Zend\Http\Request as HttpRequest;
use MCNUser\Authentication\Exception;
use Zend\Http\Response as HttpResponse;

/**
 * Class RememberMe
 * @package MCNUser\Authentication\Plugin
 */
class RememberMe extends AbstractPlugin
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
     * @param \Zend\Http\Response $response
     * @param \MCNUser\Options\Authentication\Plugin\RememberMe $options
     */
    public function __construct(TokenServiceInterface $service, HttpResponse $response, Options $options = null)
    {
        $this->service  = $service;
        $this->options  = $options;
        $this->response = $response;
    }

    /**
     * Uses a stored token to renew
     *
     * @param \Zend\Http\Request             $request
     * @param \MCNUser\Service\UserInterface $service
     *
     * @throws \MCNUser\Authentication\Exception\DomainException
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

            $token = $this->service->consumeAndRenewToken($user, $token);

            $validUntil = $token->getValidUntil() !== null ? $token->getValidUntil()->getTimestamp() : null;

            $cookie = new SetCookie('remember_me', $identity . '|' . $token->getToken(), $validUntil);

            $this->response->getHeaders()->addHeader($cookie);

        } catch (Exception\TokenAlreadyConsumedException $e) {

            return Result::create(Result::FAILURE_UNCATEGORIZED, $user, 'Token has already been consumed.');

        } catch(Exception\TokenHasExpiredException $e) {

            return Result::create(Result::FAILURE_UNCATEGORIZED, $user, 'Token has expired.');

        } catch(Exception\TokenNotFoundException $e) {

            return Result::create(Result::FAILURE_UNCATEGORIZED, $user, 'Token was not found.');
        }

        return Result::create(Result::SUCCESS, $user);
    }
}
