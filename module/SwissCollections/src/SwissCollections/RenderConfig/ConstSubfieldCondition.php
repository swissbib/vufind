<?php
/**
 * SwissCollections: FieldCondition.php
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
 * @package  SwissCollections\RenderConfig
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\RenderConfig;

use SwissCollections\RecordDriver\SolrMarc;

/**
 * Special field condition to compare a given marc subfield' value to a given
 * string.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RenderConfig
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class ConstSubfieldCondition extends AbstractFieldCondition
{
    /**
     * This subfield contains the expected value.
     *
     * @var string
     */
    protected $subfieldName;

    /**
     * The expected value.
     *
     * @var string
     */
    protected $expectedValue;

    /**
     * SubfieldCondition constructor.
     *
     * @param string $subfieldName
     * @param string $expectedValue
     */
    public function __construct(string $subfieldName, string $expectedValue)
    {
        $this->subfieldName = $subfieldName;
        $this->expectedValue = $expectedValue;
    }


    /**
     * Checks the given field. Returns true if the condition is fulfilled.
     *
     * @param \File_MARC_Data_Field|\File_MARC_Control_Field $field    the marc field
     * @param SolrMarc                                       $solrMarc the marc record
     *
     * @return bool
     */
    public function check($field, $solrMarc): bool
    {
        $anyInd = AbstractRenderConfigEntry::$UNKNOWN_INDICATOR;
        $subfieldMap = $solrMarc->getMarcFieldRawMap($field, $anyInd, $anyInd);
        $subfieldValue = $subfieldMap[$this->subfieldName];
        if (!empty($subfieldValue)) {
            $subfieldValue = trim($subfieldValue);
            if ($subfieldValue === $this->expectedValue) {
                return true;
            }
        }
        return false;
    }

    /**
     * Creates a new instance from the given text.
     *
     * @param string $text the text has the format: $SubfieldName=text
     *
     * @return ConstSubfieldCondition|null
     */
    public static function parse(string $text)
    {
        $text = trim($text);
        if (preg_match("/[$]([^=]+)=(.+)/", $text, $matches) === 1) {
            $subfieldName = trim($matches[1]);
            $expectedValue = trim($matches[2]);
            if (strlen($subfieldName) > 0 && strlen($expectedValue) > 0) {
                // "???" is used in csv to mark unknown subfield in condition
                if (strpos($subfieldName, "?") === false) {
                    return new ConstSubfieldCondition(
                        $subfieldName, $expectedValue
                    );
                }
            }
        }
        if (strlen($text) > 0) {
            echo "<!-- ERROR: BAD CONDITION: $text -->";
        }
        return null;
    }

    /**
     * Returns a string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return "$" . $this->subfieldName . "=" . $this->expectedValue;
    }
}