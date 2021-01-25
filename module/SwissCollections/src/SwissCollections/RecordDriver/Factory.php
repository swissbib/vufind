<?php
/**
 * SwissCollections: Factory.php
 *
 * PHP version 7
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swisscollections.org  / http://www.swisscollections.ch / http://www.ub.unibas.ch
 *
 * Date: 1/12/20
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
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RecordDriver
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\RecordDriver;

use Laminas\ServiceManager\ServiceManager;
use Swissbib\RecordDriver\Factory as SwissbibFactory;
use ParseCsv;

/**
 * Record driver factory.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RecordDriver
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class Factory extends SwissbibFactory
{
    /**
     * Get SolrMarcRecordDriver
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return SolrMarc
     */
    public static function getSolrMarcRecordDriver(ServiceManager $sm)
    {
        $driver = new SolrMarc(
            $sm->get('VuFind\Config\PluginManager')->get('config'),
            null,
            $sm->get('VuFind\Config\PluginManager')->get('searches'),
            $sm->get('Swissbib\HoldingsHelper'),
            $sm->get('Swissbib\RecordDriver\SolrDefaultAdapter'),
            $sm->get('Swissbib\Availability'),
            $sm->get('VuFind\Config\PluginManager')->get('Holdings')->AlephNetworks
                ->toArray(),
            $logger = $sm->get('VuFind\Log\Logger')
        );
        $driver->attachILS(
            $sm->get('VuFind\ILS\Connection'),
            $sm->get('VuFind\ILS\Logic\Holds'),
            $sm->get('VuFind\ILS\Logic\TitleHolds')
        );
        return $driver;
    }
}
