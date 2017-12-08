<?php

namespace ElasticSearch\VuFind\Search\Options;
use Zend\ServiceManager\ServiceManager;

class Factory
{
    /**
     * Factory for ElasticSearch results object.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return ElasticSearch\VuFind\Search\ElasticSearch\Options
     */
    public static function getElasticSearch(ServiceManager $sm)
    {
        $factory = new PluginFactory();
        $es = $factory->createServiceWithName($sm, 'elasticsearch', 'ElasticSearch');
        $config = $sm->getServiceLocator()
            ->get('VuFind\Config')->get('config');
        return $es;
    }
}
