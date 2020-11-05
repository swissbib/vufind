<?php
/**
 * TargetsProxy
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  TargetsProxy
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\TargetsProxy;

use Laminas\Config\Config;
use Laminas\Di\ServiceLocator;
use Laminas\Http\PhpEnvironment\RemoteAddress;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Log\Logger as LaminasLogger;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Targets proxy
 * Analyze connection parameters (IP address + requested hostname)
 * and switch target config respectively
 *
 * @category Swissbib_VuFind
 * @package  TargetsProxy
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class TargetsProxy
{
    /**
     * SearchClass
     *
     * @var string
     */
    protected $searchClass = 'Summon';

    /**
     * ClientIP
     *
     * @var String
     */
    protected $clientIp;

    /**
     * ClientUri
     *
     * @var \Laminas\Uri\Http
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
     * Config
     *
     * @var Config
     */
    protected $config;

    /**
     * TargetsProxyConfig
     *
     * @var Config
     */
    protected $targetsProxyConfig;

    /**
     * Initialize proxy with config
     *
     * @param Config        $config             Config
     * @param Config        $targetsProxyConfig Config
     * @param LaminasLogger $logger             LaminasLogger
     * @param Request       $request            Request
     */
    public function __construct($config, $targetsProxyConfig,
        LaminasLogger $logger, Request $request
    ) {
        $this->config = $config;
        $this->targetsProxyConfig = $targetsProxyConfig;
        $this->logger = $logger;
        $trustedProxies = explode(
            ',',
            $this->targetsProxyConfig->get('TrustedProxy')->get('loadbalancer')
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
     * @return \Laminas\Uri\Http
     */
    public function getClientUrl()
    {
        return $this->clientUri;
    }

    /**
     * GetConfig
     *
     * @return \Laminas\Config\Config
     */
    public function getConfig()
    {
        return $this->targetsProxyConfig;
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
            $this->targetsProxyConfig->get('TargetsProxy')
                ->get('targetKeys' . $this->searchClass)
        );

        // Check whether the current IP address matches against any of the
        // configured targets' IP / sub domain patterns
        $ipAddress = !empty($overrideIP) ? $overrideIP : $this->getClientIpV4();

        if (empty($overrideHost)) {
            $url = $this->getClientUrl();
        } else {
            $url = new \Laminas\Uri\Http();
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
             * @var \Laminas\Config\Config $targetConfig
             */
            $targetConfig = $this->targetsProxyConfig->get($targetKey);
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

        return $this->targetKey != "" ? true : false;
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
        $vfConfig = $this->config->toArray();
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
