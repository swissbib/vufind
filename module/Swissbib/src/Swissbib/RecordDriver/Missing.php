<?php
/**
 * Factory for RecordDrivers.
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
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
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\RecordDriver;

use VuFind\RecordDriver\Missing as VFMissing;

/**
 * Missing
 *
 * @category Swissbib_VuFind2
 * @package  RecordDriver
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Missing extends VFMissing implements SwissbibRecordDriver
{
    /**
     * Get short title
     * Override base method to assure a string and not an array
     *
     * @return String
     */
    public function getTitle()
    {
        try {
            $title = parent::getTitle();
        } catch (\Exception $e) {
            $title = $this->translate('Title not available');
        }

        if (is_array($title)) {
            $title = reset($title);
        }

        return $title;
    }

    /**
     * Get short title
     * Override base method to assure a string and not an array
     *
     * @return String
     */
    public function getShortTitle()
    {
        $shortTitle = parent::getShortTitle();

        if (is_array($shortTitle)) {
            $shortTitle = reset($shortTitle);
        }

        return $shortTitle;
    }

    //GH
    //Missing Typ wird bei der Tag - Suche aus verschiedensten Kontexten
    // aufgerufen (vor allem Helper)
    //@Oliver
    //moegliche Varianten
    //a) gib sinnvollere Wert zurück wie die von mir schnell hingeshriebenen
    //b) Erweiterung zu a) baue z.B. eine Loesung mit Interfaces die fuer von
    // uns erstellten Treiber festlegen,
    //dass ein Minimum an Verhalten erforderlich ist
    //c) muss man mal nachdenken....

    /**
     * Get CorporationNames
     *
     * @param bool|true $asString AsString
     *
     * @return string
     */
    public function getCorporationNames($asString = true)
    {
        return "";

    }

    /**
     * Get SecondaryAuthors
     *
     * @param bool|true $asString AsString
     *
     * @return string
     */
    public function getSecondaryAuthors($asString = true)
    {
        return "";

    }

    /**
     * Get PrimaryAuthors
     *
     * @param bool|true $asString AsString
     *
     * @return string
     */
    public function getPrimaryAuthor($asString = true)
    {
        return "";

    }

    /**
     * Get HostItemEntry
     *
     * @return array
     */
    public function getHostItemEntry()
    {
        return [];
    }

    /**
     * GetGroup
     *
     * @return string
     */
    public function getGroup()
    {
        return "";
    }

    /**
     * GetOnlineStatus
     *
     * @return bool
     */
    public function getOnlineStatus()
    {
        return false;
    }

    /**
     * GetUnions
     *
     * @return array
     */
    public function getUnions()
    {
        return [];
    }

    /**
     * GetFormatsTranslated
     *
     * @return string
     */
    public function getFormatsTranslated()
    {
        return "";
    }

    /**
     * GetFormatsRaw
     *
     * @return array
     */
    public function getFormatsRaw()
    {
        return parent::getFormats();
    }

    /**
     * Get alternative title
     *
     * @return array
     */
    public function getAltTitle()
    {
        // TODO: Implement getAltTitle() method.
    }

    /**
     * Get Cartographic Mathematical Data
     *
     * @return string
     */
    public function getCartMathData()
    {
        // TODO: Implement getCartMathData() method.
    }

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
    public function getAllSubjectVocabularies($ignoreControlFields = false)
    {
        // TODO: Implement getAllSubjectVocabularies() method.
    }

    /**
     * DisplayHoldings
     *
     * @return boolean
     */
    public function displayHoldings()
    {
        // TODO: Implement displayHoldings() method.
    }

    /**
     * GetUniqueID
     *
     * @return string
     */
    public function getUniqueID()
    {
        $uniqueID = parent::getUniqueID();

        return empty($uniqueID) ? '' : $uniqueID;
    }

    /**
     * Get Hierarchical level of record
     * 
     * @return String
     */
    public function getHierachicalLevel()
    {
        return [];
    }
}
