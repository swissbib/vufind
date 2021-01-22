<?php
/**
 * AlphaBrowse view helper
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
use Laminas\View\Helper\Url;
use VuFind\View\Helper\Root\AlphaBrowse as VuFindAlphaBrowse;

/**
 * AlphaBrowse view helper
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\View\Helper\Root
 * @author   Christoph Böhm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class AlphaBrowse extends VuFindAlphaBrowse
{
    /**
     * Config
     *
     * @var Config
     */
    protected $config;

    /**
     * Constructor
     *
     * @param Config $config
     * @param Url $helper URL helper
     */
    public function __construct(Config $config, Url $helper)
    {
        parent::__construct($helper);
        $this->config = $config;
    }

    /**
     * Get link to browse results (or null if no valid URL available)
     *
     * @param string $source AlphaBrowse index currently being used
     * @param array $item Item to link to
     *
     * @return string
     */
    public function getUrl($source, $item)
    {
        if ($item['frequency'] <= 0) {
            return null;
        }

        // TODO Make configurable or $source should match required type
        if ($source === 'browseAddressee') {
            $source = 'Author';
        }

        $query = [
        'type' => $source,
        'lookfor' => $item['fieldvalue'],
        ];
        if ($item['frequency'] == 1) {
            $query['jumpto'] = 1;
        }
        return $this->url->__invoke('search-results', [], ['query' => $query]);
    }

    /**
     * Escape a string for inclusion in a Solr query.
     *
     * @param string $str String to escape
     *
     * @return string
     */
    protected function escapeForSolr($str)
    {
        return '"' . addcslashes($str, '"') . '"';
    }
}
