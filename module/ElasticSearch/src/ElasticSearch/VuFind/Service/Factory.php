<?php
/**
 * Factory.php
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
 * @package  ElasticSearch\VuFind\Service
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace ElasticSearch\VuFind\Service;

use ElasticSearch\VuFind\RecordDriver\PluginManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class Factory
 *
 * @category VuFind
 * @package  ElasticSearch\VuFind\Service
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
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
    public static function getGenericPluginManager(ContainerInterface $sm, $ns)
    {
        $className = 'ElasticSearch\VuFind\\' . $ns . '\PluginManager';
        $configKey = strtolower(str_replace('\\', '_', $ns));
        $config = $sm->get('Config');
        return new $className(
            $sm,
            $config['elasticsearch']['plugin_managers'][$configKey]
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
     * @return PluginManager
     */
    public static function getRecordDriverPluginManager(ContainerInterface $sm)
    {
        return static::getGenericPluginManager($sm, 'RecordDriver');
    }
}
