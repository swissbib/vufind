<?php
/**
 * SwissCollections: DocTypeCategories.php
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
 * @package  SwissCollections\RecordDriver
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\RecordDriver;

use Laminas\View\Helper\AbstractHelper;

/**
 * Represents all information read from "doc-categories.csv"
 * which assigns document type categories to document types.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RecordDriver
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class DocTypeCategories extends AbstractHelper
{
    /**
     * The parsed info from {@link DocTypeCategories::$docTypeCategoryInfo}
     *
     * @var array<string,string> array of internal doc type to doc type category
     */
    protected $docTypeCategories;

    /**
     * DocTypeCategories constructor.
     *
     * @param mixed $docTypeCategoryInfo the read in information
     */
    public function __construct($csvMapping)
    {
        $this->docTypeCategories = $this->parse($csvMapping);
    }

    /**
     * Get string representation of this instance.
     *
     * @return string
     */
    public function __toString()
    {
        return "ResultListViewFieldInfo{" . print_r(
                $this->docTypeCategories, true
            ) . "}";
    }

    /**
     * Parse the csv file to an internal representation.
     *
     * @param string[][] $csvMapping the raw csv input from "doc-categories.csv".
     *
     * @return array array of doc type (string) to doc type category (string).
     */
    protected function parse($csvMapping)
    {
        $mapping = [];
        $catName = "";
        foreach ($csvMapping as $row) {
            $newCatName = trim($row['Trefferliste-Dokumenttyp']);
            if (strlen($newCatName) > 0) {
                $catName = $newCatName;
            }
            $docTypeName = trim($row['Kürzel interner Dokumenttyp']);
            $mapping[$docTypeName] = $catName;
        }
        return $mapping;
    }

    /**
     * Get the document type category from a given document type.
     *
     * @param string[] $docTypes an internal document type
     * @param string   $defaultCategory the default document type category to use if no mapping exists
     *
     * @return mixed
     */
    public function categoryOfDocType($docTypes, $defaultCategory)
    {
        $categories = [];
        foreach ($docTypes as $docType) {
            $cat = $this->docTypeCategories[$docType];
            if (!empty($cat) && !in_array($cat, $categories)) {
                $categories[] = $cat;
            }
        }
        if (empty($categories)) {
            $cat = $defaultCategory;
        } else {
            // first one wins
            $cat = $categories[0];
            if (count($categories) > 1) {
                echo "<!-- WARN Using first doc category $cat from: " . implode(
                        ",", $categories
                    ) . " -->";
            }
        }
        return $cat;
    }
}