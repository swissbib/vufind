<?php
/**
 * SummonBackendFactory
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
 * @package  VuFind_Search_Factory
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\VuFind\Search\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Config\Config;
use Zend\Http\Client as HttpClient;

use VuFind\Search\Factory\SummonBackendFactory as SummonBackendFactoryBase;
use SerialsSolutions\Summon\Zend2 as Connector;

/**
 * Factory for Summon backends.
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Search_Factory
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class SummonBackendFactory extends SummonBackendFactoryBase
{
    /**
     * Superior service manager.
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * VuFind configuration
     *
     * @var Config
     */
    protected $config;

    /**
     * Summon configuration
     *
     * @var Config
     */
    protected $summonConfig;

    /**
     * Create the Summon connector.
     * Detects clients relevant for target switching by IP and hostname
     *
     * @return Connector
     */
    protected function createConnector()
    {
        // Load credentials:
        $id  = isset($this->config->Summon->apiId) ?
            $this->config->Summon->apiId : null;
        $key = isset($this->config->Summon->apiKey) ?
            $this->config->Summon->apiKey : null;

        $overrideCredentials = $this->getOverrideApiCredentialsFromProxy();
        if ($overrideCredentials !== false) {
            if (isset($overrideCredentials['apiId'])
                && !empty($overrideCredentials['apiId'])
            ) {
                $id = $overrideCredentials['apiId'];
            }
            if (isset($overrideCredentials['apiKey'])
                && !empty($overrideCredentials['apiKey'])
            ) {
                $key = $overrideCredentials['apiKey'];
            }
        }

        /**
         * HttpClient
         *
         * @var HttpClient $client
         */
        $client  = $this->serviceLocator->get('VuFind\Http')->createClient();
        $timeout = isset($this->summonConfig->General->timeout) ?
            $this->summonConfig->General->timeout : 30;
        $client->setOptions(['timeout' => $timeout]);

        $connector = new Connector($id, $key, [], $client);
        $connector->setLogger($this->logger);

        return $connector;
    }

    /**
     * Detect client to possibly switch API key from proxy configuration
     *
     * @return Boolean|String        false or the API key to switch to
     */
    protected function getOverrideApiCredentialsFromProxy()
    {
        $targetsProxy = $this->serviceLocator->get(
            'Swissbib\TargetsProxy\TargetsProxy'
        );
        $targetsProxy->setSearchClass('Summon');
        $proxyDetected = $targetsProxy->detectTarget();
        if ($proxyDetected !== false) {
            return [
                'apiId'  => $targetsProxy->getTargetApiId(),
                'apiKey' => $targetsProxy->getTargetApiKey()
            ];
        }
        return false;
    }
}
