<?php

/**
 * RdfApiSearch
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 06.05.19
 * Time: 14:31
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
 * @package  SwissbibRdfDataApi_VuFind_Service_RdfDataApiAdapter_Search
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */

namespace SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Search;

use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Query\Query;

/**
 * RdfApiSearch
 *
 * @category Swissbib_VuFind2
 * @package  SwissbibRdfDataApi_VuFind_Service_RdfDataApiAdapter_Search
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 * @link     http://www.swissbib.ch
 */
class RdfApiSearch implements Search
{

    public function setSize(int $size)
    {
        // TODO: Implement setSize() method.
    }

    public function getSize(): int
    {
        // TODO: Implement getSize() method.
    }

    public function setFrom(int $from)
    {
        // TODO: Implement setFrom() method.
    }

    public function getFrom(): int
    {
        // TODO: Implement getFrom() method.
    }

    public function setQuery(Query $query)
    {
        // TODO: Implement setQuery() method.
    }

    public function getQuery(): Query
    {
        // TODO: Implement getQuery() method.
    }

    public function getSearchType(): SearchType
    {
        // TODO: Implement getSearchType() method.
    }

    public function setSearchType(SearchType $type)
    {
        // TODO: Implement setSearchType() method.
    }
}