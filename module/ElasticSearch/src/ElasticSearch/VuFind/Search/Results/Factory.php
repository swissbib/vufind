<?php

namespace ElasticSearch\VuFind\Search\Results;
use Zend\ServiceManager\ServiceManager;

/**
 * Search Results Object Factory Class
 *
 * @category VuFind
 * @package  Search
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:hierarchy_components Wiki
 *
 * @codeCoverageIgnore
 */
class Factory
{
    /**
     * Factory for ElasticSearch results object.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return ElasticSearch\VuFind\Search\ElasticSearch\Results
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
