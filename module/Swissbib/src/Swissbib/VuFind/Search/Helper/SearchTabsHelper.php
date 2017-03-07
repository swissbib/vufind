<?php
/**
 * "Search tabs" helper
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
 * Copyright (C) The National Library of Finland 2015-2016.
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
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\VuFind\Search\Helper;
use VuFind\Search\Results\PluginManager, Zend\View\Helper\Url, Zend\Http\Request;
use VuFind\Search\SearchTabsHelper as VFSearchTabsHelper;

/**
 * "Search tabs" helper
 *
 * @category Swissbib_VuFind2
 * @package  View_Helpers
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class SearchTabsHelper extends VFSearchTabsHelper
{
    /**
     * Set the tab configuration
     *
     * @param array $config tab config
     *
     * @return SearchTabsHelper
     */
    public function setTabConfig($config)
    {
        $this->tabConfig = $config;
        return $this;
    }

    /**
     * Set the tab filter configuration
     *
     * @param array $config tab filter config
     *
     * @return SearchTabsHelper
     */
    public function setTabFilterConfig($config)
    {
        $this->filterConfig = $config;
        return $this;
    }

}
