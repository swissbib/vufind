<?php
/**
 * LocationMap
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
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @author   Oliver Schihin <oliver.schihin@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\RecordDriver\Helper;

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
 * @category Swissbib_VuFind
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
     * Custom validation check for B410
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isItemValidForLocationMapB410(array $item,
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
     * Build location map link for B410
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return String
     */
    protected function buildLocationMapLinkB410(array $item,
        HoldingsHelper $holdingsHelper
    ) {
        $mapLinkPattern  = $this->config->get('B410');
        $signature = $item['signature'];

        return $this->buildSimpleLocationMapLink(
            $mapLinkPattern, $signature
        );
    }
    /**
     * Custom validation check for B410
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isItemValidForLocationMapB410(array $item,
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
     * Build location map link for B410
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return String
     */
    protected function buildLocationMapLinkB410(array $item,
        HoldingsHelper $holdingsHelper
    ) {
        $mapLinkPattern  = $this->config->get('B410');
        $signature = $item['signature'];

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

    /**
     * Check if map link is possible for LUUHL (Part of ZHB UPG Lucerne
     * University Library)
     * Make sure signature is present
     * Make sure item is in accesible stacks
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isItemValidForLocationMapLUUHL(array $item,
        HoldingsHelper $holdingsHelper
    ) {
        $hasSignature = isset($item['signature']) && !empty($item['signature'])
            && $item['signature'] !== '-';

        $accessibleConfigKey = $item['institution'] . '_codes';
        $isAccessible = isset($item['location_code'])
            && $this->isValueInConfigList(
                $accessibleConfigKey, $item['location_code']
            );

        return $hasSignature && $isAccessible;
    }

    /**
     * Build custom link for LUUHL (Part of ZHB UPG Lucerne University Library)
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function buildLocationMapLinkLUUHL(array $item,
        HoldingsHelper $holdingsHelper
    ) {
        $mapLinkPattern  = $this->config->get('LUUHL');
        $luuhl_param = $item['signature'];

        return $this->buildSimpleLocationMapLink($mapLinkPattern, $luuhl_param);
    }

    /**
     * Check if map link is possible for LUNI3 (Part of ZHB UPG Lucerne
     * University Library)
     * Make sure signature is present
     * Make sure item is in accesible stacks
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isItemValidForLocationMapLUNI3(array $item,
        HoldingsHelper $holdingsHelper
    ) {
        $hasSignature = isset($item['signature']) && !empty($item['signature'])
            && $item['signature'] !== '-';

        $accessibleConfigKey = $item['institution'] . '_codes';
        $isAccessible = isset($item['location_code'])
            && $this->isValueInConfigList(
                $accessibleConfigKey, $item['location_code']
            );

        return $hasSignature && $isAccessible;
    }

    /**
     * Build custom link for LUNI3 (Part of ZHB UPG Lucerne University Library)
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function buildLocationMapLinkLUNI3(array $item,
        HoldingsHelper $holdingsHelper
    ) {
        $mapLinkPattern  = $this->config->get('LUNI3');
        $luni3_param = $item['signature'];

        return $this->buildSimpleLocationMapLink($mapLinkPattern, $luni3_param);
    }

    /**
     * Check if map link is possible for LUKIL (Part of ZHB UPG Lucerne
     * University Library)
     * Make sure signature is present
     * Make sure item is in accesible stacks
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isItemValidForLocationMapLUKIL(array $item,
        HoldingsHelper $holdingsHelper
    ) {
        $hasSignature = isset($item['signature']) && !empty($item['signature'])
            && $item['signature'] !== '-';

        $accessibleConfigKey = $item['institution'] . '_codes';
        $isAccessible = isset($item['location_code'])
            && $this->isValueInConfigList(
                $accessibleConfigKey, $item['location_code']
            );

        return $hasSignature && $isAccessible;
    }

    /**
     * Build custom link for LUKIL (Part of ZHB UPG Lucerne University Library)
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function buildLocationMapLinkLUKIL(array $item,
        HoldingsHelper $holdingsHelper
    ) {
        $mapLinkPattern  = $this->config->get('LUKIL');
        $lukil_param = $item['signature'];

        return $this->buildSimpleLocationMapLink($mapLinkPattern, $lukil_param);
    }

    /**
     * Check if map link is possible for LUPHL (Part of ZHB UPG Lucerne
     * University Library)
     * Make sure signature is present
     * Make sure item is in accesible stacks
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isItemValidForLocationMapLUPHL(array $item,
        HoldingsHelper $holdingsHelper
    ) {
        $hasSignature = isset($item['signature']) && !empty($item['signature'])
            && $item['signature'] !== '-';

        $accessibleConfigKey = $item['institution'] . '_codes';
        $isAccessible = isset($item['location_code'])
            && $this->isValueInConfigList(
                $accessibleConfigKey, $item['location_code']
            );

        return $hasSignature && $isAccessible;
    }

    /**
     * Build custom link for LUPHL (Part of ZHB UPG Lucerne University Library)
     *
     * @param Array    $item           Item
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function buildLocationMapLinkLUPHL(array $item,
        HoldingsHelper $holdingsHelper
    ) {
        $mapLinkPattern  = $this->config->get('LUPHL');
        $luphl_param = $item['signature'];

        return $this->buildSimpleLocationMapLink($mapLinkPattern, $luphl_param);
    }
}
