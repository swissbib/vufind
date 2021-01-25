<?php
/**
 * SwissCollections: RenderGroupConfig.php
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
use SwissCollections\RecordDriver\ViewFieldInfo;

/**
 * Class RenderGroupConfig. Represents all group configuration options of
 * "detail-fields.csv".
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RenderConfig
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class RenderGroupConfig
{
    /**
     * The group's members.
     *
     * @var AbstractRenderConfigEntry[]
     */
    protected $info = [];
    /**
     * The group's name.
     *
     * @var string
     */
    protected $name;

    public static $IGNORE_INDICATOR = -1;

    /**
     * RenderGroupConfig constructor.
     *
     * @param string $name the group's name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Helper method to create a lookup key for a group member.
     *
     * @param AbstractRenderConfigEntry $entry the entry to create the lookup
     *                                         key for
     *
     * @return string
     */
    protected function buildKey(AbstractRenderConfigEntry $entry)
    {
        return $entry->fieldName
            . "-" . $entry->marcIndex
            . "-" . $entry->indicator1
            . "-" . $entry->indicator2
            . "-" . $entry->subfieldCondition;
    }

    /**
     * Add a {@link CompoundEntry} instance as new group member.
     *
     * @param CompoundEntry $groupEntry the group entry to add
     *
     * @return void
     */
    public function addCompound(CompoundEntry &$groupEntry)
    {
        $key = $this->buildKey($groupEntry);
        $this->info[$key] = $groupEntry;
    }

    /**
     * Add a {@link SingleEntry} instance as new group member.
     *
     * @param SingleEntry $entry the group entry to add
     *
     * @return void
     */
    public function addSingle(SingleEntry &$entry)
    {
        $key = $this->buildKey($entry);
        $this->info[$key] = $entry;
    }

    /**
     * Add a {@link SequencesEntry} instance as new group member.
     *
     * @param SequencesEntry $entry the group entry to add
     *
     * @return void
     */
    public function addSequences(SequencesEntry &$entry)
    {
        $key = $this->buildKey($entry);
        $this->info[$key] = $entry;
    }

    /**
     * Add new entry as group member.
     *
     * @param AbstractRenderConfigEntry $entry the entry to add
     *
     * @return void
     */
    public function addEntry(AbstractRenderConfigEntry &$entry)
    {
        if ($entry instanceof CompoundEntry) {
            $this->addCompound($entry);
        } else {
            if ($entry instanceof SingleEntry) {
                $this->addSingle($entry);
            } else {
                if ($entry instanceof SequencesEntry) {
                    $this->addSequences($entry);
                }
            }
        }
    }

    /**
     * Returns all group members as a map of internal key to render config element.
     *
     * @return (SingleEntry|CompoundEntry)[]
     */
    public function entries()
    {
        return $this->info;
    }

    /**
     * Get the stored render elements as simple list.
     *
     * @return array the contained render config elements
     */
    public function entryList()
    {
        return array_values($this->entries());
    }

    /**
     * Returns the group's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns a string represenation.
     *
     * @return string
     */
    public function __toString()
    {
        $s = "RenderGroupConfig{" . $this->name . ",[\n";
        foreach ($this->info as $key => $e) {
            $s = $s . "\t\t" . $e . ",\n";
        }
        return $s . "]}";
    }

    /**
     * Searches the group members by a field's name.
     *
     * @param string $name the field's name
     *
     * @return AbstractRenderConfigEntry[]
     */
    protected function getField(String $name)
    {
        $fields = [];
        foreach ($this->info as $key => $field) {
            if ($name === $field->fieldName) {
                $fields[] = $field;
            }
        }
        return $fields;
    }

    /**
     * Sort the group's members by the order in
     * "detail-view-field-structure.yaml".
     *
     * @param ViewFieldInfo $viewFieldInfo the data from "detail-view-field-structure.yaml"
     *
     * @return void
     */
    public function orderFields($viewFieldInfo)
    {
        $newFields = [];
        $fieldOrder = $viewFieldInfo->fieldNames($this->name);
        foreach ($fieldOrder as $key => $fieldName) {
            $gcs = $this->getField($fieldName);
            foreach ($gcs as $gc) {
                $newFields[$this->buildKey($gc)] = $gc;
                $gc->orderEntries();
            }
        }
        foreach ($this->info as $key => $field) {
            if (!in_array($field->labelKey, $fieldOrder)) {
                $newFields[$key] = $field;
                $field->orderEntries();
            }
        }
        $this->info = $newFields;
    }

    /**
     * Are there values for this group to render to html?
     *
     * @param SolrMarc $solrMarc the marc record
     *
     * @return bool
     */
    public function isEmpty(SolrMarc $solrMarc, RenderConfig $renderConfig
    ): bool {
        $groupIsEmpty = true;
        foreach ($this->entries() as $renderElem) {
            if (!$renderElem->isEmpty($solrMarc, $renderConfig)) {
                $groupIsEmpty = false;
                break;
            }
        }
        return $groupIsEmpty;
    }
}