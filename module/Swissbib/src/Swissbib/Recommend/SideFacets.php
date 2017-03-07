<?php
/**
 * SideFacets Recommendations Module
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
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
 * @category VuFind2
 * @package  Recommendations
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:recommendation_modules Wiki
 */
namespace Swissbib\Recommend;

use VuFind\Recommend\SideFacets as VFSideFacets;

/**
 * SideFacets Recommendations Module
 *
 * This class provides recommendations displaying facets beside search results
 *
 * @category VuFind2
 * @package  Recommendations
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:recommendation_modules Wiki
 */
class SideFacets extends VFSideFacets
{
    /**
     * Result_Settings
     *
     * @var array
     */
    protected $resultSettings = [];

    /**
     * Stores QueryFacets from config
     * which is later needed to create facet entries
     * actually QueryFacets are not supported by VuFind
     *
     * @var array
     */
    protected $queryFacets = [];

    /**
     * Returns libraries
     *
     * @return mixed
     */
    public function getMyLibraries()
    {
        return $this->results->getMyLibrariesFacets();
    }

    /**
     * Store the configuration of the recommendation module.
     *
     * @param string $settings Settings from searches.ini.
     *
     * @return void
     */
    public function setConfig($settings)
    {
        parent::setConfig($settings);

        $settings = explode(':', $settings);
        $iniName = isset($settings[2]) ? $settings[2] : 'facets';

        // Load the desired facet information...
        $config = $this->configLoader->get($iniName);
        if (isset($config->Results_Settings)) {
            $this->resultSettings = $config->Results_Settings->toArray();
        }

        if (isset($config->QueryFacets_Settings->orFacets)) {
            if (isset($this->orFacets)) {
                $this->orFacets = array_merge(
                    array_map(
                        'trim', explode(
                            ',', $config->QueryFacets_Settings->orFacets
                        )
                    ),
                    $this->orFacets
                );
            } else {
                $this->orFacets = array_map(
                    'trim', explode(
                        ',',
                        $config->QueryFacets_Settings->orFacets
                    )
                );
            }
        }

        if (isset($config->QueryFacets_Settings->exclude)) {
            if (isset($this->excludableFacets)) {
                $this->excludableFacets = array_merge(
                    array_map(
                        'trim',
                        explode(',', $config->QueryFacets_Settings->exclude)
                    ),
                    $this->excludableFacets
                );
            } else {
                $this->excludableFacets = array_map(
                    'trim', explode(
                        ',',
                        $config->QueryFacets_Settings->exclude
                    )
                );
            }
        }

        if (!empty($config->QueryFacets)) {
            foreach ($config->QueryFacets as $facetKey => $facetValue) {
                $this->queryFacets[$facetKey] = $facetValue;
            }
        }

    }

}
