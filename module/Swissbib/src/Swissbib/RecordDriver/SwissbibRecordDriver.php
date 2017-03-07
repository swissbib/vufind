<?php
/**
 * Swissbib / VuFind swissbib enhancements for Summon records
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 7/23/14
 * Time: 10:00 AM
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
 * @package  RecordDriver
 * @author   Nicolas Karrer <nkarrer@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\RecordDriver;

/**
 * Class SwissbibRecordDriver
 *
 * @category Swissbib_VuFind2
 * @package  Swissbib\RecordDriver
 * @author   Nicolas Karrer <nkarrer@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
interface SwissbibRecordDriver
{
    /**
     * Get raw formats as provided by the basic driver
     * Wrap for getFormats() because it's overwritten in this driver
     *
     * @return string[]
     */
    public function getFormatsRaw();

    /**
     * Get alternative title
     *
     * @return array
     */
    public function getAltTitle();

    /**
     * Get Cartographic Mathematical Data
     *
     * @return string
     */
    public function getCartMathData();

    /**
     * Get group-id from solr-field to display FRBR-Button
     *
     * @return string|number
     */
    public function getGroup();

    /**
     * Get structured subject vocabularies from predefined fields
     * Extended version of getAllSubjectHeadings()
     *
     * $fieldIndexes contains keys of fields to check
     * $vocabConfigs contains checks for vocabulary detection
     *
     * $vocabConfigs:
     * - ind: Value for indicator 2 in tag
     * - field: sub field 2 in tag
     * - fieldsOnly: Only check for given field indexes
     * - detect: The vocabulary key is defined in sub field 2.
     *      Don't use the key in the config (only used for local)
     *
     * Expected result:
     * [
     *        gnd => [
     *            600 => [{},{},{},...]
     *            610 => [{},{},{},...]
     *            620 => [{},{},{},...]
     *        ],
     *    rero => [
     *            600 => [{},{},{},...]
     *            610 => [{},{},{},...]
     *            620 => [{},{},{},...]
     *        ]
     * ]
     * {} is an assoc array which contains the field data
     *
     * @param boolean $ignoreControlFields Ignore control fields 0 and 2
     *
     * @return array
     */
    public function getAllSubjectVocabularies($ignoreControlFields = false);

    /**
     * Get host item entry
     *
     * @return array
     */
    public function getHostItemEntry();

    /**
     * Get unions
     *
     * @return string
     */
    public function getUnions();

    /**
     * Get online status
     *
     * @return boolean
     */
    public function getOnlineStatus();

    /**
     * Returns the corporation names
     *
     * @param boolean $asString define return type
     *
     * @return array|string
     */
    public function getCorporationNames($asString = true);

    /**
     * DisplayHoldings
     *
     * @return boolean
     */
    public function displayHoldings();

    /**
     * Get Hierarchical level of record
     * 
     * @return String
     */
    public function getHierachicalLevel();
}
