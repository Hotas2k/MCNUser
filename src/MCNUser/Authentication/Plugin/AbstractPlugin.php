<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication\Plugin;

use InvalidArgumentException;
use MCNUser\Options\Authentication\Plugin\AbstractPluginOptions;
use Zend\Http\Request as HttpRequest;
use MCNUser\Service\UserInterface;
use Zend\Stdlib\AbstractOptions;

/**
 * Interface AbstractPlugin
 * @package MCNUser\Authentication\Plugin
 */
abstract class AbstractPlugin
{
    /**
     * @var \MCNUser\Options\Authentication\Plugin\AbstractPluginOptions
     */
    protected $options;

    /**
     * Set the options
     *
     * @param \MCNUser\Options\Authentication\Plugin\AbstractPluginOptions $options
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setOptions(AbstractPluginOptions $options)
    {
        if ($options->getClassName() != get_class($this)) {

            throw new InvalidArgumentException('Wrong plugins options to given to wrong class');
        }

        $this->options = $options;

        return $this;
    }

    /**
     * @param \Zend\Http\Request $request
     * @param \MCNUser\Service\UserInterface $service
     * @return \MCNUser\Authentication\Result
     */
    abstract public function authenticate(HttpRequest $request, UserInterface $service);
}
