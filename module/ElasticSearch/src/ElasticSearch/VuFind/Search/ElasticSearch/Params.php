<?php
/**
 * Params.php
 *
 * PHP Version 7
 *
 * Copyright (C) swissbib 2018
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  ElasticSearch\VuFind\Search\ElasticSearch
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace ElasticSearch\VuFind\Search\ElasticSearch;

use VuFind\Search\Base\Params as BaseParams;
use VuFindSearch\Query\Query;

/**
 * Class Params
 *
 * @category VuFind
 * @package  ElasticSearch\VuFind\Search\ElasticSearch
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class Params extends BaseParams
{
    /**
     * The index
     *
     * @var String $_index
     */
    private $_index;

    /**
     * The template
     *
     * @var String $template
     */
    private $_template;

    /**
     * Gets the index
     *
     * @return String
     */
    public function getIndex(): string
    {
        return $this->_index ?? "";
    }

    /**
     * Sets the index
     *
     * @param String $_index The index
     *
     * @return void
     */
    public function setIndex(String $_index)
    {
        $this->_index = $_index;
    }

    /**
     * Gets the template
     *
     * @return String
     */
    public function getTemplate(): String
    {
        return $this->_template;
    }

    /**
     * Sets the template
     *
     * @param String $template The template
     *
     * @return void
     */
    public function setTemplate(String $template)
    {
        $this->_template = $template;
    }

    /**
     * From Solr/Params
     *
     * Initialize the object's search settings from a request object.
     *
     * @param \Laminas\StdLib\Parameters $request Parameter object representing user
     *                                         request.
     *
     * @return void
     */
    protected function initSearch($request)
    {
        // TODO Get default values from config
        $this->setIndex($request->get('index', 'lsb'));
        $this->setTemplate($request->get('template', 'id'));
        // Special case -- did we get a list of IDs instead of a standard query?
        $ids = $request->get('overrideIds', null);
        if (is_array($ids)) {
            // TODO Remove or overwrite overrideIds behaviour
            //            $this->setQueryIDs($ids);
            $this->query->setHandler($request->get('type', null));
            $this->query->setString('[' . implode(",", $ids) . ']');
        } else {
            // Use standard initialization:
            parent::initSearch($request);
        }
    }

    /**
     * Override the normal search behavior with an explicit array of IDs that must
     * be retrieved.
     *
     * @param array $ids Record IDs to load
     *
     * @return void
     */
    public function setQueryIDs($ids)
    {
        // No need for spell checking or highlighting on an ID query!
        $this->getOptions()->spellcheckEnabled(false);
        $this->getOptions()->disableHighlighting();

        // Special case -- no IDs to set:
        if (empty($ids)) {
            $this->setOverrideQuery('NOT *:*');
        }

        $callback = function ($i) {
            return '"' . addcslashes($i, '"') . '"';
        };
        $ids = array_map($callback, $ids);
        $this->setOverrideQuery('id:(' . implode(' OR ', $ids) . ')');
    }

    /**
     * Return search query object.
     *
     * @return VuFindSearch\Query\AbstractQuery
     */
    public function getQuery(): Query
    {
        if ($this->overrideQuery) {
            return new Query($this->overrideQuery);
        }
        return $this->query;
    }
}
