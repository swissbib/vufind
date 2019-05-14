<?php

/**
 * SearchTypeEnum
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


/**
 * SearchTypeEnum
 *
 * @category Swissbib_VuFind2
 * @package  SwissbibRdfDataApi_VuFind_Service_RdfDataApiAdapter_Search
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 * @link     http://www.swissbib.ch
 */
class SearchTypeEnum
{

    /**
     * List of allowed enum types
     *
     * @var array
     */
    private $_allowedTypes = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15,
        16, 17];

    private $_currentSearchType;

    const ID_SEARCH_PERSON = 1;

    const ID_SEARCH_ORGANISATION = 2;

    const ID_SEARCH_BIB_RESOURCE = 3;

    const ID_SEARCH_DOCUMENT = 4;

    const ID_SEARCH_SUBJECT = 5;

    const ID_SEARCH_GND = 6;

    const COLLECTION_FIELDS = 7;

    const COLLECTION_DOCUMENT = 8;

    const COLLECTION_BIB_RESOURCE = 9;

    const COLLECTION_ITEM = 10;

    const COLLECTION_PERSON = 11;

    //const CollectionWork = 2;

    //do we need this??
    const PERSON = 12;

    const SUB_SUBJECTS = 13;

    const BIB_RESOURCES_BY_AUTHOR = 14;

    const BIB_RESOURCES_BY_SUBJECT = 15;

    const PERSON_BY_GENRE = 16;

    const PERSON_BY_MOVEMENT = 17;

    /**
     * SearchTypeEnum constructor.
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