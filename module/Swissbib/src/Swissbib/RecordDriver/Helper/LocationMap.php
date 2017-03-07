<?php
/**
 * LocationMap
 *
 * PHP version 5
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  RecordDriver_Helper
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @author   Oliver Schihin <oliver.schihin@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\RecordDriver\Helper;

use Zend\Config\Config;

use Swissbib\RecordDriver\Helper\Holdings as HoldingsHelper;

/**
 * Generate location map link depending on item data and configuration
 * This class allows you to implement custom behaviour per institution.
 * Add the institution code as postfix to the called methods.
 * Possible method names are:
 * - isItemValidForLocationMap
 * - buildLocationMapLink
 *
 * Example: isItemValidForLocationMapA100
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class LocationMap extends LocationMapBase
{
    /**
     * Check whether item should have a map link
     * Customized for A100
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isItemValidForLocationMapA100(array $item,
        HoldingsHelper $holdingsHelper
    ) {
        $isItemAvailable = true; //Implement availability check with holdings helper
        $hasSignature = isset($item['signature']) && !empty($item['signature'])
            && $item['signature'] !== '-';
        $accessibleConfigKey = $item['institution'] . '_codes';
        $isAccessible = isset($item['location_code'])
            && $this->isValueInConfigList(
                $accessibleConfigKey, $item['location_code']
            );
        $circulatingConfigKey = $item['institution'] . '_status';
        $isCirculating          = true;

        // Compare holding/item status if set
        if (isset($item['holding_status'])) {
            $isCirculating = $this->isValueInConfigList(
                $circulatingConfigKey, $item['holding_status']
            );
        }

        return $isItemAvailable && $hasSignature && $isAccessible && $isCirculating;
    }

    /**
     * Build location map link for A100
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return String
     */
    protected function buildLocationMapLinkA100(array $item,
        HoldingsHelper $holdingsHelper
    ) {
        $mapLinkPattern  = $this->config->get('A100');
        $signature = preg_replace('/^UBH /', '', $item['signature']);

        return $this->buildSimpleLocationMapLink(
            $mapLinkPattern, $signature
        );
    }

    /**
     * Custom validation check for B500
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isItemValidForLocationMapB500(array $item,
        HoldingsHelper $holdingsHelper
    ) {
        //Implement availability check with holdings helper
        $isItemAvailable = true;
        $hasSignature = isset($item['signature']) && !empty($item['signature'])
            && $item['signature'] !== '-';
        $accessibleConfigKey = $item['institution'] . '_codes';
        $isAccessible = isset($item['location_code'])
            && $this->isValueInConfigList(
                $accessibleConfigKey, $item['location_code']
            );
        $circulatingConfigKey = $item['institution'] . '_status';
        $isCirculating = true;

        // Compare holding/item status if set
        if (isset($item['holding_status'])) {
            $isCirculating = $this->isValueInConfigList(
                $circulatingConfigKey, $item['holding_status']
            );
        }

        return $isItemAvailable && $hasSignature && $isAccessible && $isCirculating;
    }

    /**
     * Build custom link for B500
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function buildLocationMapLinkB500(array $item,
        HoldingsHelper $holdingsHelper
    ) {
        $mapLinkPattern  = $this->config->get('B500');
        if (preg_match(
            '/Spiele|Klassensatz|Permanentapparat/', $item['location_expanded']
        )
        ) {
            $b500_param = $item['location_expanded'] . '_' . $item['signature'];
        } else {
            $b500_param = $item['signature'];
        }

        return $this->buildSimpleLocationMapLink($mapLinkPattern, $b500_param);
    }

    /**
     * Check if map link is possible
     * Make sure signature is present
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isItemValidForLocationMapHSG(array $item,
        HoldingsHelper $holdingsHelper
    ) {
        $hasSignature = isset($item['signature']) && !empty($item['signature'])
            && $item['signature'] !== '-';

        return $hasSignature;
    }

    /**
     * Build custom link for HSG
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function buildLocationMapLinkHSG(array $item,
        HoldingsHelper $holdingsHelper
    ) {
        $mapLinkPattern  = $this->config->get('HSG');
        $hsg_param = $item['location_code'] . ' ' . $item['signature'];

        return $this->buildSimpleLocationMapLink($mapLinkPattern, $hsg_param);
    }
}
