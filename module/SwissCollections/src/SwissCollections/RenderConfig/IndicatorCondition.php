<?php
/**
 * SwissCollections: IndicatorCondition.php
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
 * Special field condition to compare a given marc indicator's value to a given
 * int.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RenderConfig
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class IndicatorCondition extends AbstractFieldCondition
{
    public static $UNKNOWN_INDICATOR = -1;
    /**
     * The indicator to check. Either 1 or 2.
     *
     * @var int
     */
    protected $indicatorId;

    /**
     * The expected value.
     *
     * @var int
     */
    public $expectedValue;

    /**
     * IndicatorCondition constructor.
     *
     * @param int $indicatorId   the indicator to check
     * @param int $expectedValue the expected value
     */
    protected function __construct($indicatorId, $expectedValue)
    {
        $this->indicatorId = $indicatorId;
        $this->expectedValue = $expectedValue;
    }

    /**
     * Creates an {@link IndicatorCondition} from the given text if the text
     * is valid. Otherwise null is returned.
     *
     * @param int    $indicatorId either 1 or 2
     * @param string $text        the indicator's expected value
     *
     * @return IndicatorCondition|null
     */
    protected static function buildIndicatorCondition($indicatorId, $text)
    {
        $ind = IndicatorCondition::parse($text);
        if ($ind === self::$UNKNOWN_INDICATOR) {
            return null;
        }
        return new IndicatorCondition($indicatorId, $ind);
    }

    /**
     * Creates an {@link IndicatorCondition} from the given text for the first
     * indicator if the text is valid. Otherwise null is returned.
     *
     * @param string $text the indicator's expected value
     *
     * @return IndicatorCondition|null
     */
    public static function buildIndicator1Condition($text)
    {
        return IndicatorCondition::buildIndicatorCondition(1, $text);
    }

    /**
     * Creates an {@link IndicatorCondition} from the given text for the second
     * indicator if the text is valid. Otherwise null is returned.
     *
     * @param string $text the indicator's expected value
     *
     * @return IndicatorCondition|null
     */
    public static function buildIndicator2Condition($text)
    {
        return IndicatorCondition::buildIndicatorCondition(2, $text);
    }

    /**
     * Parses indicator from text. Returns
     * {@link IndicatorCondition::$UNKNOWN_INDICATOR} for unknown/bad
     * indicator value.
     *
     * @param string|null $text a positive int or empty string or null
     *
     * @return int
     */
    public static function parse(string $text): int
    {
        if ($text === null) {
            return self::$UNKNOWN_INDICATOR;
        }
        $text = trim($text);
        // note: "0" is a valid indicator value, so don't use empty()!
        if (strlen($text) === 0) {
            return self::$UNKNOWN_INDICATOR;
        }
        if (!ctype_digit($text)) {
            return self::$UNKNOWN_INDICATOR;
        }
        return intval($text);
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
        try {
            if ($field instanceof \File_MARC_Data_Field) {
                $ind = IndicatorCondition::parse(
                    $field->getIndicator($this->indicatorId)
                );
                if ($ind !== $this->expectedValue) {
                    echo "<!-- " . $field->getTag()
                        . " CONDITION FAILED: Indicator $this, got "
                        . $ind . " -->";
                    return false;
                }
            } else {
                if ($field instanceof \File_MARC_Control_Field) {
                    // this conditions represents an indicator requirement, but
                    // a control field has no indicator; if no indicator
                    // requirement would exist, no IndicatorCondition would be
                    // created; so this condition is always false
                    echo "<!-- CONDITION FAILED: Indicator $this for "
                        . " File_MARC_Control_Field -->";
                    return false;
                } else {
                    echo "<!-- WARN (IndicatorCondition::check): Can't handle field type: "
                        . get_class($field) . " -->\n";
                    // return true in order not to hide a field's value
                }
            }
        } catch (\Throwable $exception) {
            echo "<!-- ERROR: Exception " . $exception->getMessage() . "\n"
                . $exception->getTraceAsString() . " -->\n";
            // return true in order not to hide a field's value
        }
        return true;
    }

    /**
     * Returns a string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return "|" . $this->indicatorId . "|=" . $this->expectedValue;
    }
}