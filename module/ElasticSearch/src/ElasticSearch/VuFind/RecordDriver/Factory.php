<?php
/**
 * Created by IntelliJ IDEA.
 * User: boehm
 * Date: 30.11.17
 * Time: 10:42
 */
namespace ElasticSearch\VuFind\RecordDriver;

use Zend\ServiceManager\ServiceManager;

class Factory
{
    public static function getElasticSearchRecordDriver(ServiceManager $sm)
    {
        return new PluginManager();

        $driver = new ElasticSearch(
            //          $sm->getServiceLocator()->get('VuFind\Config')->get('config'),
            //          null,
            //          $sm->getServiceLocator()->get('VuFind\Config')->get('searches')
        );
        //        $driver->attachSearchService($sm->getServiceLocator()->get('VuFind\Search'));
        return $driver;
    }
}
