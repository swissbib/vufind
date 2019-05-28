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
    private $_allowedTypes = ["ID_SEARCH_PERSON",
            "ID_SEARCH_ORGANISATION",
            "ID_SEARCH_BIB_RESOURCE",
            "ID_SEARCH_DOCUMENT",
            "ID_SEARCH_SUBJECT",
            "ID_SEARCH_GND",
            "IDS_SEARCH_GND",
            "COLLECTION_FIELDS",
            "COLLECTION_DOCUMENT",
            "COLLECTION_BIB_RESOURCE",
            "COLLECTION_ITEM",
            "COLLECTION_PERSON",
            "PERSON",
            "SUB_SUBJECTS",
            "BIB_RESOURCES_BY_AUTHOR",
            "BIB_RESOURCES_BY_SUBJECT",
            "PERSON_BY_GENRE",
            "PERSON_BY_MOVEMENT",
            "IDS_SEARCH_PERSON"];

    private $_currentSearchType;

    const ID_SEARCH_PERSON = "ID_SEARCH_PERSON";

    const IDS_SEARCH_PERSON = "IDS_SEARCH_PERSON";

    const ID_SEARCH_ORGANISATION = "ID_SEARCH_ORGANISATION";

    const ID_SEARCH_BIB_RESOURCE = "ID_SEARCH_BIB_RESOURCE";

    const ID_SEARCH_DOCUMENT = "ID_SEARCH_DOCUMENT";

    const ID_SEARCH_SUBJECT = "ID_SEARCH_SUBJECT";

    const ID_SEARCH_GND = "ID_SEARCH_GND";

    const IDS_SEARCH_GND = "IDS_SEARCH_GND";

    const COLLECTION_FIELDS = "COLLECTION_FIELDS";

    const COLLECTION_DOCUMENT = "COLLECTION_DOCUMENT";

    const COLLECTION_BIB_RESOURCE = "COLLECTION_BIB_RESOURCE";

    const COLLECTION_ITEM = "COLLECTION_ITEM";

    const COLLECTION_PERSON = "COLLECTION_PERSON";

    //const CollectionWork = 2;

    //do we need this??
    const PERSON = "PERSON";

    const SUB_SUBJECTS = "SUB_SUBJECTS";

    const BIB_RESOURCES_BY_AUTHOR = "BIB_RESOURCES_BY_AUTHOR";

    const BIB_RESOURCES_BY_SUBJECT = "BIB_RESOURCES_BY_SUBJECT";

    const PERSON_BY_GENRE = "PERSON_BY_GENRE";

    const PERSON_BY_MOVEMENT = "PERSON_BY_MOVEMENT";

    /**
     * SearchTypeEnum constructor.
     * @param string $type
     * @throws \Exception
     */
    public function __construct(string $type)
    {

        if (!in_array($type, $this->_allowedTypes)) {
            throw new \Exception("Search type $type not allowed");
        }

        $this->_currentSearchType = $type;
    }

    public function getCurrectSearchType(): string {
        return $this->_currentSearchType;
    }


}