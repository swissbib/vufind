<?php
/**
 * ElasticSearchBackendFactory.php
 *
 * PHP Version 7
 *
 * Copyright (C) swissbib 2018
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  ElasticSearch\VuFind\Search\Factory
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace SwissbibRdfDataApi\VuFind\Search\Factory;

use ElasticSearch\VuFindSearch\Backend\ElasticSearch\Backend;
// @codingStandardsIgnoreLineuse
use ElasticSearch\VuFindSearch\Backend\ElasticSearch\Response\AdapterClientResult\RecordCollectionFactory;
use ElasticsearchAdapter\Adapter;
use ElasticsearchAdapter\Connector\ElasticsearchClientConnector;
use Interop\Container\ContainerInterface;
use Zend\Config\Config;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class RdfDataApiSearchBackendFactory
 *
 * @category VuFind
 * @package  SwissbibRdfDataApi\VuFind\Search\Factory
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class RdfDataApiBackendFactory implements FactoryInterface
{
    /**
     * The Service Locator
     *
     * @var
     */
    private $_serviceLocator;

    /**
     * The config
     *
     * @var
     */
    private $_config;

    /**
     * RdfDataApiBackendFactory constructor.
     */
    public function __construct()
    {
        $this->searchConfig = 'searches';
        $this->searchYaml = 'elasticsearch_adapter.yml';
    }

    /**
     * Create service
     *
     * @param ContainerInterface $sm      serviceManager
     * @param string             $name    name of the service
     * @param array|null         $options options
     *
     * @return Backend|object
     */
    public function __invoke(ContainerInterface $sm, $name, array $options = null)
    {
        $this->_serviceLocator = $sm;
        $this->_config = $this->_serviceLocator->get('VuFind\Config');
        if ($this->_serviceLocator->has('VuFind\Logger')) {
            $this->logger = $this->_serviceLocator->get('VuFind\Logger');
        }

        return $this->createBackend();
    }

    /**
     * Creates the Backend
     *
     * @return \ElasticSearch\VuFindSearch\Backend\ElasticSearch\Backend
     */
    protected function createBackend()
    {
        $hosts = $this->_config->get("config")->ElasticSearch->hosts->toArray();
        $connector = new ElasticsearchClientConnector($hosts);
        $adapter = new Adapter($connector);
        $backend = new Backend($adapter, $this->loadSpecs());
        if ($this->logger) {
            $backend->setLogger($this->logger);
        }

        $manager = $this->_serviceLocator->get(
            'ElasticSearch\VuFind\RecordDriver\PluginManager'
        );
        $factory = new RecordCollectionFactory(
            [$manager, 'getElasticSearchRecord']
        );
        $backend->setRecordCollectionFactory($factory);

        return $backend;
    }

    /**
     * Load the search specs.
     *
     * @return array
     */
    public function loadSpecs()
    {
        $specReader = $this->_serviceLocator->get('VuFind\SearchSpecsReader');
        $yaml = $specReader->get($this->searchYaml);
        return $yaml['parameters']['elasticsearch_adapter.templates'];
    }

    /**
     * Sets the Config
     *
     * @param \Zend\Config\Config $_config The config
     *
     * @return void
     */
    public function setConfig(Config $_config)
    {
        $this->_config = $_config;
    }
}
