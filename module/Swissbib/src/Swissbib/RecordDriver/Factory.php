<?php
/**
 * Factory for RecordDrivers.
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
 * @package  RecordDriver
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\RecordDriver;

use Zend\ServiceManager\ServiceManager;

/**
 * Factory
 *
 * @category Swissbib_VuFind
 * @package  RecordDriver
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Factory
{
    /**
     * Get SolrDefaultAdapter
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return SolrDefaultAdapter
     */
    public static function getSolrDefaultAdapter(ServiceManager $sm)
    {
        $config = $sm->get('VuFind\Config\PluginManager')->get('config');
        return new SolrDefaultAdapter($config);
    }

    /**
     * Get SolrMarcRecordDriver
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return SolrMarc
     */
    public static function getSolrMarcRecordDriver(ServiceManager $sm)
    {
        $driver = new \Swissbib\RecordDriver\SolrMarc(
            $sm->get('VuFind\Config\PluginManager')->get('config'),
            null,
            $sm->get('VuFind\Config\PluginManager')->get('searches'),
            $sm->get('Swissbib\HoldingsHelper'),
            $sm->get('Swissbib\RecordDriver\SolrDefaultAdapter'),
            $sm->get('Swissbib\Availability'),
            $sm->get('VuFind\Config\PluginManager')->get('Holdings')->AlephNetworks->toArray(),
            $logger = $sm->get('VuFind\Log\Logger')
        );
        $driver->attachILS(
            $sm->get('VuFind\ILS\Connection'),
            $sm->get('VuFind\ILS\Logic\Holds'),
            $sm->get('VuFind\ILS\Logic\TitleHolds')
        );

        return $driver;
    }

    /**
     * Get WorldCatRecordDriver
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return WorldCat
     */
    public static function getWorldCatRecordDriver(ServiceManager $sm)
    {
        $baseConfig = $sm->get('VuFind\Config\PluginManager')->get('config');
        $worldcatConfig = $sm->get('VuFind\Config\PluginManager')
            ->get('WorldCat');

        return new WorldCat(
            $baseConfig, // main config
            $worldcatConfig // record config
        );
    }

    /**
     * Get RecordDriverMissing
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return Missing
     */
    public static function getRecordDriverMissing(ServiceManager $sm)
    {
        $baseConfig = $sm->get('VuFind\Config\PluginManager')->get('config');

        return new Missing($baseConfig);
    }
}
