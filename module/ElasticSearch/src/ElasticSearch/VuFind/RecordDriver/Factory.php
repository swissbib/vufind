<?php
/**
 * Factory for RecordDriver
 *
 * @category ElasticSearch
 * @package  RecordDriver
 * @author   Christoph Böhm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://www.swissbib.ch/
 */
namespace ElasticSearch\VuFind\RecordDriver;

use Zend\ServiceManager\ServiceManager;

/**
 * Class Factory
 *
 * @package ElasticSearch\VuFind\RecordDriver
 */
class Factory
{
    public static function getElasticSearchRecord(ServiceManager $sm)
    {
        $driver = new ElasticSearch(
            //          $sm->getServiceLocator()->get('VuFind\Config')->get('config'),
            //          null,
            //          $sm->getServiceLocator()->get('VuFind\Config')->get('searches')
        );
        //        $driver->attachSearchService($sm->getServiceLocator()->get('VuFind\Search'));
        return $driver;
    }

    public static function getESPersonRecord(ServiceManager $sm)
    {
        $driver = new ESPerson(
            //          $sm->getServiceLocator()->get('VuFind\Config')->get('config'),
            //          null,
            //          $sm->getServiceLocator()->get('VuFind\Config')->get('searches')
        );
        //        $driver->attachSearchService($sm->getServiceLocator()->get('VuFind\Search'));
        return $driver;
    }

    public static function getESBibliographicResourceRecord(ServiceManager $sm)
    {
        return new ESBibliographicResource();
    }

    public static function getESSubjectRecord(ServiceManager $sm)
    {
        return new ESSubject();
    }
}
