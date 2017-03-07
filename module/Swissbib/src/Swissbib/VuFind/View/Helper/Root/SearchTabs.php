<?php
/**
 * SearchTabs
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 9/12/13
 * Time: 11:46 AM
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
 * @package  VuFind_View_Helper_Root
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\VuFind\View\Helper\Root;

use VuFind\Search\Base\Results,
    VuFind\Search\Results\PluginManager,
    Swissbib\VuFind\Search\Helper\SearchTabsHelper,
    Zend\View\Helper\Url,
    Zend\Http\Request;
use VuFind\View\Helper\Root\SearchTabs as VuFindSearchTabs;
//use Swissbib\VuFind\Search\SearchTabsHelper;
//use Swissbib\VuFind\Search\Results\PluginManager as PluginManager;

/**
 * SearchTabs
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_View_Helper_Root
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
class SearchTabs extends VuFindSearchTabs
{
    /**
     * Constructor
     *
     * @param PluginManager    $results Search results plugin manager
     * @param Url              $url     URL helper
     * @param SearchTabsHelper $helper  Search tabs helper
     */
    public function __construct(PluginManager $results, Url $url,
        SearchTabsHelper $helper
    ) {
        $this->results = $results;
        $this->url = $url;
        $this->helper = $helper;
    }

    /**
     * Determine information about search tabs
     *
     * @param string $activeSearchClass The search class ID of the active search
     * @param string $query             The current search query
     * @param string $handler           The current search handler
     * @param string $type              The current search type (basic/advanced)
     * @param array  $hiddenFilters     The current hidden filters
     * @param string $view              variable to determine which tab config
     *                                  should be used
     *
     * @return array
     */
    public function getTabConfig($activeSearchClass, $query, $handler,
        $type = 'basic', $hiddenFilters = [],
        $view = 'default'
    ) {
        $backupConfig = $this->helper->getTabConfig();
        $this->helper->setTabConfig($this->injectViewDependentConfig($view));

        $tabs = parent::getTabConfig($activeSearchClass, $query, $handler, $type);

        $this->helper->setTabConfig($backupConfig);
        return $tabs;
    }

    /**
     * This function is used to distinguish between the two configs [SearchTabs]
     * and [AdvancedSearchTabs] depending on the view parameter
     *
     * @param string $view View mode
     *
     * @return array $config
     */
    public function injectViewDependentConfig($view)
    {
        switch ($view) {
        case 'advanced':
            return array_key_exists(
                'AdvancedSearchTabs',
                $this->helper->getTabConfig()
            ) ?
                $this->helper->getTabConfig()['AdvancedSearchTabs'] : [];
        default:
            return array_key_exists(
                'SearchTabs',
                $this->helper->getTabConfig()
            ) ?
                $this->helper->getTabConfig()['SearchTabs'] : [];
        }
    }
}
