<?php
namespace ElasticSearch\VuFind\Search\Factory;

use ElasticSearch\VuFindSearch\Backend\ElasticSearch\Backend;
use ElasticSearch\VuFindSearch\Backend\ElasticSearch\Response\AdapterClientResult\RecordCollectionFactory;
use ElasticsearchAdapter\Adapter;
use ElasticsearchAdapter\Connector\ElasticsearchClientConnector;
use Zend\Config\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Created by IntelliJ IDEA.
 * User: boehm
 * Date: 23.11.17
 * Time: 11:45
 */
class ElasticSearchBackendFactory implements FactoryInterface
{
    private $serviceLocator;

    /**
     * @var Config
     */
    private $config;

    /**
     * ElasticSearchBackendFactory constructor.
     */
    public function __construct()
    {
        $this->searchConfig = 'searches';
        $this->searchYaml = 'elasticsearch_adapter.yml';
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Backend
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->config = $this->serviceLocator->get('VuFind\Config');
        if ($this->serviceLocator->has('VuFind\Logger')) {
            $this->logger = $this->serviceLocator->get('VuFind\Logger');
        }

        return $this->createBackend();
    }

    /**
     * @return Backend
     */
    private function createBackend()
    {
        $hosts = $this->config->get("config")->ElasticSearch->hosts->toArray();
        $connector = new ElasticsearchClientConnector($hosts);
        $adapter = new Adapter($connector);
        $backend = new Backend($adapter, $this->loadSpecs());
        if ($this->logger)
        {
            $backend->setLogger($this->logger);
        }

        $manager = $this->serviceLocator->get('ElasticSearch\RecordDriverPluginManager');
        $factory = new RecordCollectionFactory([$manager, 'getElasticSearchRecordDriver']);
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
        $specReader = $this->serviceLocator->get('VuFind\SearchSpecsReader');
        $yaml = $specReader->get($this->searchYaml);
        return $yaml['parameters']['elasticsearch_adapter.templates'];
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }
}
