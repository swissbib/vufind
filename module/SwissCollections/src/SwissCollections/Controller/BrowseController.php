<?php
/**
 * SwissCollections: BrowseController.php
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
 * @package  SwissCollections\Controller
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\Controller;

use Laminas\Config\Config;
use Laminas\ServiceManager\ServiceLocatorInterface;
use VuFind\Controller\BrowseController as VuFindBrowseController;

/**
 * Class BrowseController.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\Controller
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class BrowseController extends VuFindBrowseController
{
    /**
     * The browse actions
     *
     * @var Config
     */
    protected $actions;
    /**
     * The browse categories.
     *
     * @var array
     */
    protected $categories;

    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $sm     Service manager
     * @param Config                  $config VuFind configuration
     */
    public function __construct(ServiceLocatorInterface $sm, Config $config)
    {
        parent::__construct($sm, $config);
        $this->actions = $config->Browse->actions;
        foreach (
            $config->Browse->categories->toArray() as $action => $categoryList
        ) {
            $categories = explode(",", $categoryList);
            $entry = [];
            foreach ($categories as $category) {
                $facet = $this->actions->get($category, $category);
                $entry[$facet] = $category;
            }
            $this->categories[$action] = $entry;
        }
    }

    /**
     * Action for Browse
     *
     * @return \Laminas\View\Model\ViewModel
     */
    public function browseAction()
    {
        $action = $this->params()->fromQuery('action');

        $facet = $this->actions->get($action);
        if ($facet !== null) {
            $categoryList = $this->categories[$action];
            return $this->performBrowse($action, $categoryList, true);
        }
    }

    /**
     * Given a list of active options, format them into details for the view.
     *
     * @return array
     */
    protected function buildBrowseOptions()
    {
        // Initialize the array of top-level browse options.
        $browseOptions = [];

        $activeOptions = $this->actions->toArray();
        foreach ($activeOptions as $action => $facet) {
            $browseOptions[] = $this->buildBrowseOption($action, $action);
        }
        return $browseOptions;
    }

    /**
     * Get the facet search term for an action
     *
     * @param string $action action to be translated
     *
     * @return string
     */
    protected function getCategory($action = null)
    {
        if ($action == null || $this->actions[$action] == null) {
            $action = $this->getCurrentAction();
        }

        return $this->actions[$action];
    }

    /**
     * Get array with two values: a filter name and a secondary list based on facets
     *
     * @param string $action the action to process
     *
     * @return array
     */
    protected function getSecondaryList($action)
    {
        // eg Genre
        $category = $this->getCategory();
        if ($action === 'alphabetical') {
            return ['', $this->getAlphabetList()];
        }
        $facet = $this->actions->get($action, $action);
        return [
            $facet,
            $this->quoteValues($this->getFacetList($facet, $category))
        ];
    }
}
