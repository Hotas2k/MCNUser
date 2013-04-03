<?php
/**
 * Copyright (c) 2011-2013 Antoine Hedgecock.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author      Antoine Hedgecock <antoine@pmg.se>
 * @author      Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright   2011-2013 Antoine Hedgecock
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace MCNUser\Authentication\Plugin;

use InvalidArgumentException;
use MCNUser\Options\Authentication\Plugin\AbstractPluginOptions;
use Zend\Http\Request as HttpRequest;
use MCNUser\Service\UserInterface;
use MCNUser\Authentication\Exception;
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
     * @throws \MCNUser\Authentication\Exception\InvalidArgumentException
     *
     * @return $this
     */
    public function setOptions(AbstractPluginOptions $options)
    {
        if ($options->getClassName() != get_class($this)) {

            throw new Exception\InvalidArgumentException('Wrong plugins options to given to wrong class');
        }

        $this->options = $options;

        return $this;
    }

    /**
     * @return \MCNUser\Options\Authentication\Plugin\AbstractPluginOptions
     * @throws \MCNUser\Authentication\Exception\DomainException
     */
    public function getOptions()
    {
        if ($this->options === null) {

            throw new Exception\DomainException('No options have been specified');
        }

        return $this->options;
    }

    /**
     * @param \Zend\Http\Request $request
     * @param \MCNUser\Service\UserInterface $service
     * @return \MCNUser\Authentication\Result
     */
    abstract public function authenticate(HttpRequest $request, UserInterface $service);
}
