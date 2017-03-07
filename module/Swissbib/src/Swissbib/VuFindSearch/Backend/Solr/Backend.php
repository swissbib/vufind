<?php
 /**
 * Extended version of the VuFind Solr Backend Factory
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 8/19/13
 * Time: 10:21 PM
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
 * @package  VuFindSearch_Backend_Solr
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\VuFindSearch\Backend\Solr;

use VuFindSearch\Backend\Solr\Backend as VuFindSearchBackend;
use VuFindSearch\Query\AbstractQuery;
use VuFindSearch\ParamBag;
use VuFindSearch\Backend\Solr\Connector;
use VuFindSearch\Response\RecordCollectionInterface;

/**
 * Backend
 *
 * @category Swissbib_VuFind2
 * @package  VuFindSearch_Backend_Solr
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
class Backend extends VuFindSearchBackend
{
    /**
     * Constructor
     *
     * @param Connector $connector SOLR connector
     */
    public function __construct(Connector $connector)
    {
        parent::__construct($connector);
    }

    /**
     * Perform a search and return record collection.
     *
     * @param AbstractQuery $query  Search query
     * @param integer       $offset Search offset
     * @param integer       $limit  Search limit
     * @param ParamBag      $params Search backend parameters
     *
     * @return RecordCollectionInterface
     */
    public function search(AbstractQuery $query, $offset, $limit,
        ParamBag $params = null
    ) {
        //it was necessary to overwrite this function
        // before Refactoring of Spellchecking
        //I will leave it because it might be possible
        // that we need our own backend later
        return parent::search($query, $offset, $limit, $params);
    }
}
