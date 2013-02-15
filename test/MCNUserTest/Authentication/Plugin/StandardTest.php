<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\Authentication\Plugin;

use MCNUser\Authentication\Plugin\Standard;
use MCNUser\Authentication\Result;
use MCNUser\Options\Authentication\Plugin\Standard as StandardOptions;
use MCNUserTest\TestAsset\UserService;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;

class StandardTest extends \PHPUnit_Framework_TestCase
{
    protected $userService;

    public function setUp()
    {
        $this->userService = new UserService;
    }

    public function tearDown()
    {
        unset($this->userService);
    }

    protected function getOptions()
    {
        return new StandardOptions(array(

            'http_identity_field'   => 'identity',
            'http_credential_field' => 'credential',

            'entity_identity_property'   => 'email',
            'entity_credential_property' => 'password',

            // Credential treatment is applied on the user supplied password
            // before comparing it with the password stored in the backend
            'credential_treatment' => function($password) {

                return sha1($password);
            }
        ));
    }

    protected function getPlugin()
    {
        return new Standard($this->getOptions());
    }

    public function testForIdentityNotFound()
    {
        $request = new Request();
        $request->setPost(
            new Parameters(array(
                'email'    => 'wrong email',
                'password' => 'password'
            ))
        );

        $result = $this->getPlugin()->authenticate($request, $this->userService);

        $this->assertTrue($result->getCode() == Result::FAILURE_IDENTITY_NOT_FOUND);
        $this->assertNull($result->getIdentity());
    }

    public function testForInvalidCredential()
    {
        $request = new Request();
        $request->setPost(
            new Parameters(array(
                'identity' => 'hello@world.com',
                'credential' => 'wrong password'
            ))
        );

        $result = $this->getPlugin()->authenticate($request, $this->userService);

        $this->assertTrue($result->getCode()     == Result::FAILURE_INVALID_CREDENTIAL);
        $this->assertTrue($result->getIdentity() == $this->userService->users[0]);
    }

    public function testForSuccessfulLogin()
    {
        $request = new Request();
        $request->setPost(
            new Parameters(array(
                'identity' => 'hello@world.com',
                'credential' => 'password'
            ))
        );

        $result = $this->getPlugin()->authenticate($request, $this->userService);

        $this->assertTrue($result->getCode()     == Result::SUCCESS);
        $this->assertTrue($result->getIdentity() == $this->userService->users[0]);
    }
}
