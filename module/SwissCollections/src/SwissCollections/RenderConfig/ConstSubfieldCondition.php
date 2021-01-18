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
 * Special field condition to compare a given marc subfield's value to a given
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
     * Expected value of indicator 1
     * ({@link IndicatorCondition::$UNKNOWN_INDICATOR} if unset)
     *
     * @var int
     */
    protected $expectedIndicator1;

    /**
     * Expected value of indicator 2
     * ({@link IndicatorCondition::$UNKNOWN_INDICATOR} if unset)
     *
     * @var int
     */
    protected $expectedIndicator2;

    /**
     * ConstSubfieldCondition constructor.
     *
     * @param string $subfieldName       the subfield's name to check
     * @param string $expectedValue      the expected value
     * @param int    $expectedIndicator1 the expected first indicator
     * @param int    $expectedIndicator2 the expected second indicator
     */
    public function __construct(
        string $subfieldName, string $expectedValue, $expectedIndicator1,
        $expectedIndicator2
    ) {
        $this->subfieldName = $subfieldName;
        $this->expectedValue = $expectedValue;
        $this->expectedIndicator1 = $expectedIndicator1;
        $this->expectedIndicator2 = $expectedIndicator2;
    }


    /**
     * Checks the given field. Returns true if the condition is fulfilled.
     *
     * @param \File_MARC_Data_Field|\File_MARC_Control_Field $field    the marc field
     * @param SolrMarc                                       $solrMarc the marc record
     *
     * @return bool
     */
    protected function check($field, $solrMarc): bool
    {
        // indicators are checked too, so no need to do it twice
        $subfieldMap = $solrMarc->getMarcFieldRawMap($field, null);
        $subfieldValue = $subfieldMap[$this->subfieldName];
        if (!empty($subfieldValue)) {
            $subfieldValue = trim($subfieldValue);
            if ($subfieldValue === $this->expectedValue) {
                return true;
            }
        }
        echo "<!-- " . $field->getTag()
            . " CONDITION FAILED: Constant $this, got " . $subfieldValue
            . " -->";
        return false;
    }

    /**
     * Creates a new instance from the given text.
     *
     * @param string $text               the text has the format: $SubfieldName=text
     * @param int    $expectedIndicator1 the expected first indicator
     * @param int    $expectedIndicator2 the expected second indicator
     *
     * @return ConstSubfieldCondition|null
     */
    public static function parse(
        string $text, int $expectedIndicator1, int $expectedIndicator2
    ) {
        $text = trim($text);
        if (preg_match("/[$]([^=]+)=(.+)/", $text, $matches) === 1) {
            $subfieldName = trim($matches[1]);
            $expectedValue = trim($matches[2]);
            if (strlen($subfieldName) > 0 && strlen($expectedValue) > 0) {
                // "???" is used in csv to mark unknown subfield in condition
                if (strpos($subfieldName, "?") === false) {
                    return new ConstSubfieldCondition(
                        $subfieldName, $expectedValue, $expectedIndicator1,
                        $expectedIndicator2
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