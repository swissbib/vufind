<?php
/**
 * Swissbib / VuFind swissbib enhancements for Summon records
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 1/2/13
 * Time: 4:09 PM
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
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\RecordDriver;

use VuFind\RecordDriver\Summon as VuFindSummon;

/**
 * Enhancement for swissbib Summon records
 *
 * @category Swissbib_VuFind2
 * @package  RecordDriver
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
class Summon extends VuFindSummon implements SwissbibRecordDriver
{
    /**
     * Get Author
     *
     * @return String Author name(s)
     */
    public function getAuthor()
    {
        $author = $this->_getField('Author', '-');

        return is_array($author) ? implode('; ', $author) : $author;
    }

    /**
     * Get URI
     *
     * @return Array
     */
    public function getURI()
    {
        return $this->_getField('URI');
    }

    /**
     * Get Link
     *
     * @return String ?? return 360-summon-link (field 'link')
     */
    public function getLink()
    {
        return $this->_getField('link');
    }

    /**
     * Has DirectLink
     *
     * @return Boolean
     */
    public function hasDirectLink()
    {
        return in_array('DirectLink', $this->_getLinkModel());
    }

    /**
     * Has Fulltext
     *
     * @return Boolean
     */
    public function hasFulltext()
    {
        return 1 === intval($this->_getField('hasFullText'));
    }

    /**
     * Get AllSubjectHeadingsAsString
     *
     * @return string
     */
    public function getAllSubjectHeadingsAsString()
    {
        $ret = [];
        $subj = $this->getAllSubjectHeadings();
        if (is_array($subj) and count($subj) > 0) {
            foreach ($subj as $sub) {
                $ret = array_merge($ret, $sub);
            }
            $ret = trim(implode('; ', $ret));
        }

        return $ret;
    }

    /**
     * Get DatabaseTitle
     *
     * @return String
     */
    public function getDatabaseTitle()
    {
        $ret = '';
        $db = $this->_getField('DatabaseTitle');
        if (is_array($db)) {
            $ret = implode('; ', $db);
        }

        return $ret;
    }

    /**
     * Get AltTitle
     *
     * @return String
     */
    public function getAltTitle()
    {
        return '';
    }

    /**
     * Get CitationFormats
     *
     * @override
     *
     * @return array Strings representing citation formats.
     */
    public function getCitationFormats()
    {
        $solrDefaultAdapter = $this->hierarchyDriverManager->getServiceLocator()
            ->get('Swissbib\RecordDriver\SolrDefaultAdapter');

        return $solrDefaultAdapter->getCitationFormats();
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
        return [];
    }

    /**
     * Get raw formats as provided by the basic driver
     * Wrap for getFormats() because it's overwritten in this driver
     *
     * @return string[]
     */
    public function getFormatsRaw()
    {
        // TODO: Implement getFormatsRaw() method.
    }

    /**
     * Get Cartographic Mathematical Data
     *
     * @return string
     */
    public function getCartMathData()
    {
        return null;
    }

    /**
     * Get highlighted fulltext
     *
     * @return String
     */
    public function getHighlightedFulltext()
    {
        return null;
    }

    /**
     * Get group-id from solr-field to display FRBR-Button
     *
     * @return string|number
     */
    public function getGroup()
    {
        // TODO: Implement getGroup() method.
    }

    /**
     * Get host item entry
     *
     * @return array
     */
    public function getHostItemEntry()
    {
        // TODO: Implement getHostItemEntry() method.
    }

    /**
     * Get unions
     *
     * @return string
     */
    public function getUnions()
    {
        // TODO: Implement getUnions() method.
    }

    /**
     * Get online status
     *
     * @return boolean
     */
    public function getOnlineStatus()
    {
        // TODO: Implement getOnlineStatus() method.
    }

    /**
     * Returns the corporation names
     *
     * @param Boolean $asString AsString
     *
     * @return array|string
     */
    public function getCorporationNames($asString = true)
    {
        return $asString ? '' : [];
    }

    /**
     * DisplayHoldings
     *
     * @return Boolean
     */
    public function displayHoldings()
    {
        return false;
    }

    /**
     * DisplayLinks
     *
     * @return boolean
     */
    public function displayLinks()
    {
        return true;
    }

    /**
     * GetThumbnail
     *
     * @param string $size Size
     *
     * @return array|bool|string
     */
    public function getThumbnail($size = 'small')
    {
        return parent::getThumbnail('small');
    }

    /**
     * Get Field
     *
     * @param String $fieldName     FieldName
     * @param String $fallbackValue FallBackValue
     *
     * @return String
     */
    private function _getField($fieldName, $fallbackValue = '')
    {
        return array_key_exists($fieldName, $this->fields) ?
            $this->fields[$fieldName] : $fallbackValue;
    }

    /**
     * Get LinkModel
     *
     * @return Array
     */
    private function _getLinkModel()
    {
        return $this->_getField('LinkModel');
    }

    /**
     * Get related Entries
     *
     * @return array
     */
    public function getRelatedEntries()
    {
        return [];
    }

    /**
     * Checks the current record if it's supported for generating OpenURLs.
     *
     * @return bool
     */
    public function supportsOpenUrl()
    {
        // Summon never uses OpenURLs for anything other than COinS:
        return true;
    }

    /**
     * Get Hierarchical level of record
     *
     * @return String
     */
    public function getHierachicalLevel()
    {

    }
}
