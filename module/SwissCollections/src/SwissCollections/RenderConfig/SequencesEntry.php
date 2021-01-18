<?php
/**
 * SwissCollections: SequencesEntry.php
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

use SwissCollections\Formatter\FieldFormatterData;
use SwissCollections\RecordDriver\FieldRenderContext;
use SwissCollections\RecordDriver\SolrMarc;

/**
 * Class SequencesEntry.
 *
 * Represents a stream of repeating marc subfield sequences of one marc record.
 * A sequence is a defined order of marc subfields.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RenderConfig
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class SequencesEntry extends CompoundEntry
{
    /**
     * All allowed subfield name sequences.
     *
     * @var String[][]
     */
    protected $sequences;

    /**
     * SequencesEntry constructor.
     *
     * @param string                      $groupName       the group's name from detail-fields.csv, column "Gruppierungsname / Oberbegriff"
     * @param string                      $fieldName       the field's name from detail-fields.csv, column "Bezeichnung"
     * @param string                      $subfieldName    the subfield's name from detail-fields.csv, column "Unterbezeichnung"
     * @param int                         $marcIndex       the marc index from from detail-fields.csv, column "datafield tag"
     * @param FormatterConfig             $formatterConfig the formatter to apply
     * @param int                         $indicator1      the first indicator from from detail-fields.csv, column "datafield ind1"
     * @param int                         $indicator2      the second indicator from from detail-fields.csv, column "datafield ind2"
     * @param AbstractFieldCondition|null $condition       the condition  from from detail-fields.csv, column "subfield match condition"
     */
    public function __construct(
        $groupName, $fieldName, $subfieldName, $marcIndex,
        $formatterConfig, $indicator1 = -1, $indicator2 = -1, $condition = ""
    ) {
        parent::__construct(
            $groupName, $fieldName, $subfieldName, $marcIndex, $formatterConfig,
            $indicator1,
            $indicator2, $condition
        );
        $this->formatterConfig->setRepeatedDefault(true);
        if (empty($this->formatterConfig->formatterNameDefault)) {
            $this->formatterConfig->formatterNameDefault = "inline";
        }
    }

    /**
     * Returns a string represenation.
     *
     * @return string
     */
    public function __toString()
    {
        $seqStr = "[";
        foreach ($this->sequences as $index => $seq) {
            if ($index > 0) {
                $seqStr .= ",";
            }
            $seqStr .= "[" . implode(",", $seq) . "]";
        }
        $seqStr .= "]";
        return "SequencesEntry{" . parent::__toString() . "," . $seqStr . "}";
    }

    /**
     * Calls addElement() for every not already added subfield.
     *
     * @param String[][] $sequences the values from detail-view-field-structure.yaml at "sequences:"
     *
     * @return void
     */
    public function setSequences($sequences)
    {
        $this->sequences = $sequences;
    }

    /**
     * Build a lookup key.
     *
     * @param string $fieldSubfieldName the fields subfield name (column "Unterbezeichnung" in detail-fields.csv)
     * @param string $marcSubfieldName  the marc subfield name (e.g. "a")
     *
     * @return string
     */
    protected function buildSubfieldName($fieldSubfieldName, $marcSubfieldName
    ): string {
        return $fieldSubfieldName . "-" . $marcSubfieldName;
    }

    /**
     * Searches a given subfield name in the given values and returns the containing value or null.
     *
     * @param String               $subfieldName the subfield's name to find
     * @param FieldFormatterData[] $values       search subfield's name in these values
     *
     * @return null | FieldFormatterData
     */
    protected function inValues($subfieldName, $values)
    {
        foreach ($values as $ffd) {
            $sn = $this->buildSubfieldName(
                $ffd->renderConfig->subfieldName,
                $ffd->renderConfig->getMarcSubfieldName()
            );
            if ($sn === $subfieldName) {
                return $ffd;
            }
        }
        return null;
    }

    /**
     * Similiar to {@link AbstractRenderConfigEntry::orderEntries}, but works on FieldFormatterData[] instead of SingleEntry[].
     *
     * @param FieldFormatterData[] $values sort values by the values returned by {@link SequencesEntry::getEntryOrder}
     *
     * @return FieldFormatterData[]
     */
    protected function orderValues($values)
    {
        $newEntries = [];
        $entryOrder = $this->getEntryOrder();
        if (empty($entryOrder)) {
            $newEntries = $values;
        } else {
            $fieldNames = [];
            foreach ($entryOrder as $fieldFormatter) {
                $fieldName = $fieldFormatter->fieldName;
                $fieldNames[] = $fieldName;
                $ffd = $this->inValues($fieldName, $values);
                if ($ffd) {
                    $newEntries[] = $ffd;
                }
            }
            foreach ($values as $v) {
                $sn = $this->buildSubfieldName(
                    $v->renderConfig->subfieldName,
                    $v->renderConfig->getMarcSubfieldName()
                );
                if (!in_array($sn, $fieldNames)) {
                    $newEntries[] = $v;
                }
            }
        }
        return $newEntries;
    }

    /**
     * Checks whether a subfield sequence matches the current field values.
     *
     * @param array    $rawData  maps marc subfield names to their value
     * @param SolrMarc $solrMarc the marc record
     *
     * @return FieldFormatterData[]
     */
    public function matchesSubfieldSequence(&$rawData, &$solrMarc)
    {
        $rawDataSubfieldNames = array_keys($rawData);
        $rawDataSubfieldNamesLen = count($rawDataSubfieldNames);
        $values = [];
        foreach ($this->sequences as $seq) {
            $pos = 0;
            $values = [];
            foreach ($seq as $subfieldName) {
                if ($pos >= $rawDataSubfieldNamesLen) {
                    continue 2;
                }
                if ($rawDataSubfieldNames[$pos] !== $subfieldName) {
                    continue 2;
                }
                $text = $rawData[$rawDataSubfieldNames[$pos]];
                $fieldFormatterData = $this->buildFieldFormatterData(
                    $subfieldName, $text, $solrMarc
                );
                $values[] = $fieldFormatterData;
                $pos++;
            }
            // echo "<!-- MATSEQ: " . implode("-", $seq) . " -->";
            break;
        }
        // $this->elements are already ordered, but the $values are built from "sequences:" which
        // has to use its own sorting! so, re-sort $values too ...
        return $this->orderValues($values);
    }

    /**
     * Add not specified subfields from sequences.
     *
     * @return void
     */
    public function addSubfieldsFromSequences(): void
    {
        foreach ($this->sequences as $seq) {
            foreach ($seq as $marcSubfieldName) {
                if (!$this->knowsSubfield($marcSubfieldName)) {
                    $this->addElement(
                        $this->buildSubfieldName(
                            $this->subfieldName, $marcSubfieldName
                        ), $marcSubfieldName
                    );
                }
            }
        }
    }

    /**
     * Render one given marc field to html. The subfields are filtered by
     * the given {@link SequencesEntry::sequences}.
     *
     * @param \File_MARC_Control_Field|\File_MARC $field   the current field to render
     * @param FieldRenderContext                  $context the render context
     *
     * @return void
     */
    public function render(&$field, &$context)
    {
        $rawData = $context->solrMarc->getMarcFieldRawMap(
            $field, $this->indicator1, $this->indicator2
        );
        $matchedValues = $this->matchesSubfieldSequence(
            $rawData, $context->solrMarc
        );
        if (!empty($matchedValues)) {
            $this->renderImpl($matchedValues, $context);
        }
    }

}
