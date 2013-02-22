<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Factory;

use MCNUser\Options\Authentication\AuthenticationOptions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AuthenticationOptionsFactory
 * @package MCNUser\Factory
 */
class AuthenticationOptionsFactory implements FactoryInterface
{
    /**
     * Create the options
     *
     * @param ServiceLocatorInterface $sl
     *
     * @throws Exception\RuntimeException If no configuration has been specified
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        // Make sure configuration has been specified
        if (! isSet($sl->get('Config')['MCNUser']['authentication'])) {

            throw new Exception\RuntimeException('No configuration specified.');
        }

        $config = $sl->get('Config')['MCNUser']['authentication'];

        return new AuthenticationOptions($config);
    }
}
