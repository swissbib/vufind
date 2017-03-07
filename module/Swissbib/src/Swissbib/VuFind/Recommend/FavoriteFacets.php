<?php
/**
 * Swissbib / VuFind swissbib enhancements for Summon records
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 04/07/14
 * Time: 3:20 PM
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
 * @package  VuFind_Recommend
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\VuFind\Recommend;

use VuFind\Recommend\FavoriteFacets as VFFavoriteFacets;

/**
 * FavoriteFacets Recommendations Module
 *
 * This class provides special facets for the Favorites area (tags/lists)
 * The VuFind class was extended because we need the former functionality where
 * lists are only
 * shown in case the menu item favorite lists is chosen by users
 * Newer version of VF2 (2.2.1) seems to display favorite lists on every
 * "My Research" page
 * we have to evaluate this later (project swissbib responsive design)
 *
 * @category VuFind2
 * @package  VuFind_Recommend
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:recommendation_modules Wiki
 */
class FavoriteFacets extends VFFavoriteFacets
{
    /**
     * SetConfig
     *
     * Store the configuration of the recommendation module.
     *
     * @param string $settings Settings from searches.ini.
     *
     * @return void
     */
    public function setConfig($settings)
    {
        $this->mainFacets = ['lists' => 'Your Lists', 'tags' => 'Your Tags'];
    }
}
