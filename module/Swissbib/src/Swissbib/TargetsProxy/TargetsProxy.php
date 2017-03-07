<?php
/**
 * TargetsProxy
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  TargetsProxy
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\TargetsProxy;

use Zend\Config\Config;
use Zend\Di\ServiceLocator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Http\PhpEnvironment\Request;

use Zend\Log\Logger as ZendLogger;

/**
 * Targets proxy
 * Analyze connection parameters (IP address + requested hostname)
 * and switch target config respectively
 *
 * @category Swissbib_VuFind2
 * @package  TargetsProxy
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class TargetsProxy implements ServiceLocatorAwareInterface
{
    /**
     * ServiceLocator
     *
     * @var ServiceLocator
     */
    protected $serviceLocator;

    /**
     * SearchClass
     *
     * @var string
     */
    protected $searchClass = 'Summon';

    /**
     * Config
     *
     * @var Config
     */
    protected $config;

    /**
     * ClientIP
     *
     * @var String
     */
    protected $clientIp;

    /**
     * ClientUri
     *
     * @var \Zend\Uri\Http
     */
    protected $clientUri;

    /**
     * TargetKey
     *
     * @var Boolean|String
     */
    protected $targetKey = false;

    /**
     * TargetApiKey
     *
     * @var Boolean|String
     */
    protected $targetApiKey = false;

    /**
     * TargetApiId
     *
     * @var Boolean|String
     */
    protected $targetApiId = false;

    protected $logger;

    /**
     * Initialize proxy with config
     *
     * @param Config     $config  Config
     * @param ZendLogger $logger  ZendLogger
     * @param Request    $request Request
     */
    public function __construct(Config $config, ZendLogger $logger, Request $request)
    {
        $this->config = $config;
        $this->logger = $logger;
        $trustedProxies = explode(
            ',', $this->config->get('TrustedProxy')->get('loadbalancer')
        );

        // Populate client info properties from request
        $RemoteAddress = new RemoteAddress();
        $RemoteAddress->setUseProxy();
        $RemoteAddress->setTrustedProxies($trustedProxies);

        $ipAddress = $RemoteAddress->getIpAddress();
        $this->clientIp = [
            'IPv4' => $ipAddress, // i.e.: aaa.bbb.ccc.ddd - standard dotted format
        ];

        $Request = new Request();
        $this->clientUri = $Request->getUri();
    }

    /**
     * SetSearchClass
     *
     * @param string $className ClassName
     *
     * @return void
     */
    public function setSearchClass($className = 'Summon')
    {
        $this->searchClass    = $className;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator ServiceLocator
     *
     * @return void
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * GetClientIp
     *
     * @return Array
     */
    public function getClientIp()
    {
        return $this->clientIp;
    }

    /**
     * GetClientIpV4
     *
     * @return String Client IP address in IPv4 notation (standard dotted format),
     *                i.e.: aaa.bbb.ccc.ddd
     */
    public function getClientIpV4()
    {
        return $this->clientIp['IPv4'];
    }

    /**
     * GetClientUrl
     *
     * @return \Zend\Uri\Http
     */
    public function getClientUrl()
    {
        return $this->clientUri;
    }

    /**
     * GetConfig
     *
     * @return \Zend\Config\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get target to be used for the client's IP range + sub domain
     *
     * @param String $overrideIP   Simulate request from given
     *                             instead of detecting real IP
     * @param String $overrideHost Simulate request from given
     *                             instead of detecting from real URL
     *
     * @return Boolean Target detected or not?
     */
    public function detectTarget($overrideIP = '', $overrideHost = '')
    {
        $this->targetKey = false;    // Key of detected target config
        $this->targetApiId = false;
        $this->targetApiKey = false;

        $targetKeys = explode(
            ',',
            $this->config->get('TargetsProxy')
                ->get('targetKeys' . $this->searchClass)
        );

        // Check whether the current IP address matches against any of the
        // configured targets' IP / sub domain patterns
        $ipAddress = !empty($overrideIP) ? $overrideIP : $this->getClientIpV4();

        if (empty($overrideHost)) {
            $url = $this->getClientUrl();
        } else {
            $url = new \Zend\Uri\Http();
            $url->setHost($overrideHost);
        }

        $IpMatcher = new IpMatcher();
        $UrlMatcher = new UrlMatcher();

        foreach ($targetKeys as $targetKey) {
            $isMatchingIP = false;
            $isMatchingUrl = false;

            /**
             * Config
             *
             * @var \Zend\Config\Config $targetConfig
             */
            $targetConfig = $this->config->get($targetKey);
            $patternsIP = '';
            $patternsURL = '';

                // Check match of IP address if any pattern configured.
                // If match is found, set corresponding keys and continue matching
            if ($targetConfig->offsetExists('patterns_ip')) {
                $patternsIP = $targetConfig->get('patterns_ip');
                if (!empty($patternsIP)) {
                    $targetPatternsIp = explode(',', $patternsIP);
                    $isMatchingIP = $IpMatcher->isMatching(
                        $ipAddress, $targetPatternsIp
                    );

                    if ($isMatchingIP === true) {
                        $this->_setConfigKeys($targetKey);
                    }
                }
            }

            // Check match of URL hostname if any pattern configured.
            // If match is found, set corresponding keys and exit immediately
            if ($targetConfig->offsetExists('patterns_url')) {
                $patternsURL = $targetConfig->get('patterns_url');
                if (!empty($patternsURL)) {
                    $targetPatternsUrl = explode(',', $patternsURL);
                    $isMatchingUrl = $UrlMatcher->isMatching(
                        $url->getHost(), $targetPatternsUrl
                    );
                    if ($isMatchingUrl === true) {
                        $this->_setConfigKeys($targetKey);
                        return true;
                    }
                }
            }
        }

        return ($this->targetKey != "" ? true : false);
    }

    /**
     * Set relevant keys from the target key section in config.ini
     *
     * @param String $targetKey TargetKey
     *
     * @return void
     */
    private function _setConfigKeys($targetKey)
    {
        $this->targetKey = $targetKey;
        $vfConfig = $this->serviceLocator->get('VuFind\Config')
            ->get('config')->toArray();
        $this->targetApiId = $vfConfig[$this->targetKey]['apiId'];
        $this->targetApiKey = $vfConfig[$this->targetKey]['apiKey'];
    }

    /**
     * Get key of detected target to be rerouted to
     *
     * @return bool|String
     */
    public function getTargetKey()
    {
        return $this->targetKey;
    }

    /**
     * GetTargetApiKey
     *
     * @return bool|String
     */
    public function getTargetApiKey()
    {
        return $this->targetApiKey;
    }

    /**
     * GetTargetApiId
     *
     * @return bool|String
     */
    public function getTargetApiId()
    {
        return $this->targetApiId;
    }
}
