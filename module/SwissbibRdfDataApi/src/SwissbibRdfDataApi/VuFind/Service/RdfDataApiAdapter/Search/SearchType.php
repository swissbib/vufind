<?php

/**
 * SearchType
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 02.05.19
 * Time: 18:08
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

use mysql_xdevapi\Exception;

/**
 * SearchType
 *
 * @category Swissbib_VuFind2
 * @package  SwissbibRdfDataApi_VuFind_Service_RdfDataApiAdapter_Search
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 * @link     http://www.swissbib.ch
 */
class SearchType
{

    /**
     * List of allowed enum types
     *
     * @var array
     */
    private $_allowedTypes = [1, 2];

    private $_currentSearchType;

    const GND = 1;

    const BIBRESOURCES = 2;



    /**
     * SearchType constructor.
     * @param int $type
     * @throws \Exception
     */
    public function __construct(int $type)
    {

        if (!in_array($type, $this->_allowedTypes)) {
            throw new \Exception("Search type $type not allowed");
        }

        $this->_currentSearchType = $type;
    }


}