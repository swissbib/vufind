<?php
/**
 * Factory for controllers.
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
 * @package  RecordDriver_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @author   Oliver Schihin <oliver.schihin@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\RecordDriver\Helper;

use Swissbib\RecordDriver\Helper\Availability as AvailabilityHelper;
use Swissbib\RecordDriver\Helper\Holdings as HoldingsHelper;
use Laminas\ServiceManager\ServiceManager;

/**
 * Factory for helpers.
 *
 * @category Swissbib_VuFind
 * @package  RecordDriver_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Factory
{
    /**
     * Construct the RecordController.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return Holdings
     */
    public static function getHoldingsHelper(ServiceManager $sm)
    {
        $ilsConnection = $sm->get('VuFind\ILS\Connection');
        $hmac = $sm->get('VuFind\HMAC');
        $authManager = $sm->get('VuFind\Auth\Manager');
        $ilsAuth = $sm->get('VuFind\ILSAuthenticator');
        $config = $sm->get('VuFind\Config\PluginManager');
        $translator = $sm->get('Laminas\Mvc\I18n\Translator');
        $locationMap = $sm->get('Swissbib\LocationMap');
        $eBooksOnDemand = $sm->get('Swissbib\EbooksOnDemand');
        $availability = $sm->get('Swissbib\Availability');
        $bibCodeHelper = $sm->get('Swissbib\BibCodeHelper');
        $logger = $sm->get('Swissbib\Logger');

        return new HoldingsHelper(
            $ilsConnection,
            $hmac,
            $authManager,
            $ilsAuth,
            $config,
            $translator,
            $locationMap,
            $eBooksOnDemand,
            $availability,
            $bibCodeHelper,
            $logger
        );
    }

    /**
     * Creates LocationMap type
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return LocationMap
     */
    public static function getLocationMap(ServiceManager $sm)
    {
        $locationMapConfig = $sm->get('VuFind\Config\PluginManager')
            ->get('config')->locationMap;
        return new LocationMap($locationMapConfig);
    }

    /**
     * Creates EbooksOnDemand type Helper
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return EbooksOnDemand
     */
    public static function getEbooksOnDemand(ServiceManager $sm)
    {
        $eBooksOnDemandConfig = $sm->get('VuFind\Config\PluginManager')
            ->get('config')->eBooksOnDemand;
        $translator = $sm->get('Laminas\Mvc\I18n\Translator');

        return new EbooksOnDemand($eBooksOnDemandConfig, $translator);
    }

    /**
     * Creates Helper type for availabilty functionality
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return Availability
     */
    public static function getAvailabiltyHelper(ServiceManager $sm)
    {
        $bibCodeHelper = $sm->get('Swissbib\BibCodeHelper');
        $availabilityConfig = $sm->get('VuFind\Config\PluginManager')
            ->get('config')->Availability;
        $logger = $sm->get('VuFind\Log\Logger');

        return new AvailabilityHelper($bibCodeHelper, $availabilityConfig, $logger);
    }

    /**
     * Gets BibCodeHelper
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return BibCode
     *
     * @throws \Laminas\ServiceManager\Exception\ServiceNotFoundException
     */
    public static function getBibCodeHelper(ServiceManager $sm)
    {
        $alephNetworkConfig = $sm->get('VuFind\Config\PluginManager')
            ->get('Holdings')->AlephNetworks;

        return new BibCode($alephNetworkConfig);
    }
}
