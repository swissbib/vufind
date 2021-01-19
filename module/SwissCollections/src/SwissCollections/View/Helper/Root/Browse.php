<?php
/**
 * SwissCollections: Browse.php
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
 * @package  SwissCollections\View\Helper\Root
 * @author   Christoph Böhm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\View\Helper\Root;

use Laminas\Config\Config;
use VuFind\View\Helper\Root\Browse as VuFindBrowse;

/**
 * Browse controller view helper
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\View\Helper\Root
 * @author   Christoph Böhm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Browse extends VuFindBrowse
{
    /**
     * The browse actions
     *
     * @var Config
     */
    protected $actions;

    /**
     * Browse constructor.
     *
     * @param Config $config Config
     */
    public function __construct(Config $config)
    {
        $this->actions = $config->Browse->actions;
    }

    /**
     * Get the Solr field associated with a particular browse action.
     *
     * @param string $category Browse category
     * @param string $backup   Backup browse action if no match is found for $action
     *
     * @return string
     */
    public function getSolrField($category, $backup = null)
    {
        $facet = $this->actions->get($category);
        if ($facet !== null) {
            return $facet;
        }

        if ($backup == null) {
            return $category;
        }
        return $this->getSolrField($backup);
    }
}