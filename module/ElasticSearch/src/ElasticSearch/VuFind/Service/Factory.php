<?php

/**
 *
 * @category linked-swissbib
 * @package  Search_Service
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace ElasticSearch\VuFind\Service;

use Zend\ServiceManager\ServiceManager;


class Factory
{

    /**
     * Generic plugin manager factory (support method).
     *
     * @param ServiceManager $sm Service manager.
     * @param string         $ns LinkedSwissbib namespace containing plugin manager
     *
     * @return object
     */
    public static function getGenericPluginManager(ServiceManager $sm, $ns)
    {
        $className = 'ElasticSearch\VuFind\\' . $ns . '\PluginManager';
        $configKey = strtolower(str_replace('\\', '_', $ns));
        $config = $sm->get('Config');
        return new $className(
          $sm,
            new \Zend\ServiceManager\Config(
                $config['elasticsearch']['plugin_managers'][$configKey]
            )
        );
    }

    /**
     * Construct the Search\Options Plugin Manager.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return \VuFind\Search\Options\PluginManager
     */
    public static function getSearchOptionsPluginManager(ServiceManager $sm)
    {
        return static::getGenericPluginManager($sm, 'Search\Options');
    }

    /**
     * Construct the Search\Params Plugin Manager.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return \VuFind\Search\Params\PluginManager
     */
    public static function getSearchParamsPluginManager(ServiceManager $sm)
    {
        return static::getGenericPluginManager($sm, 'Search\Params');
    }


    /**
     * Construct the Search\Results Plugin Manager.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return \VuFind\Search\Results\PluginManager
     */
    public static function getSearchResultsPluginManager(ServiceManager $sm)
    {
        return static::getGenericPluginManager($sm, 'Search\Results');
    }
    /**
     * Construct the RecordDriver Plugin Manager.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return \ElasticSearch\VuFind\RecordDriver\PluginManager
     */
    public static function getRecordDriverPluginManager(ServiceManager $sm)
    {
        return static::getGenericPluginManager($sm, 'RecordDriver');
    }


}