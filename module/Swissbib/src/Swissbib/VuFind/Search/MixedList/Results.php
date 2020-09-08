<?php
/**
 * Mixed List aspect of the Search Multi-class (Results)
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  Search_MixedList
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org Main Site
 */
namespace Swissbib\VuFind\Search\MixedList;

use VuFind\Search\MixedList\Results as VuFindMixedListResults;

/**
 * Search Mixed List Results
 *
 * @category Swissbib_VuFind
 * @package  Search_MixedList
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org Main Site
 */
class Results extends VuFindMixedListResults
{
    /**
     * Target
     *
     * @var String
     */
    protected $target = 'swissbib';

    /**
     * Facets Configuration
     *
     * @param \Laminas\Config\Config $facetsConfig the facet config
     *
     * @return void
     */
    public function setFacetsConfig(\Laminas\Config\Config $facetsConfig)
    {
        $this->facetsConfig = $facetsConfig;
    }
}
