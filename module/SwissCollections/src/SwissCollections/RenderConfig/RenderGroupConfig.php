<?php
/**
 * Created by IntelliJ IDEA.
 * User: ballmann
 * Date: 12/4/20
 * Time: 8:30 AM
 */

namespace SwissCollections\RenderConfig;

use SwissCollections\RecordDriver\SolrMarc;
use SwissCollections\RecordDriver\ViewFieldInfo;

class RenderGroupConfig {
    /**
     * @var AbstractRenderConfigEntry[]
     */
    protected $info = [];
    protected $name;

    public static $IGNORE_INDICATOR = -1;

    public function __construct(String $name) {
        $this->name = $name;
    }

    protected function buildKey(AbstractRenderConfigEntry $entry) {
        return $entry->fieldName
            . "-" . $entry->marcIndex
            . "-" . $entry->indicator1
            . "-" . $entry->indicator2
            . "-" . $entry->subfieldCondition;
    }

    public function addCompound(CompoundEntry &$groupEntry) {
        $key = $this->buildKey($groupEntry);
        $this->info[$key] = $groupEntry;
    }

    public function addSingle(SingleEntry &$entry) {
        $key = $this->buildKey($entry);
        $this->info[$key] = $entry;
    }

    public function addSequences(SequencesEntry &$entry) {
        $key = $this->buildKey($entry);
        $this->info[$key] = $entry;
    }

    public function addEntry(AbstractRenderConfigEntry &$entry) {
        if ($entry instanceof CompoundEntry) {
            $this->addCompound($entry);
        } else if ($entry instanceof SingleEntry) {
            $this->addSingle($entry);
        } else if ($entry instanceof SequencesEntry) {
            $this->addSequences($entry);
        }
    }

    /**
     * @return (SingleEntry|CompoundEntry)[]
     */
    public function entries() {
        return $this->info;
    }

    public function getName() {
        return $this->name;
    }

    public function __toString() {
        $s = "RenderGroupConfig{" . $this->name . ",[\n";
        foreach ($this->info as $key => $e) {
            $s = $s . "\t\t" . $e . ",\n";
        }
        return $s . "]}";
    }

    /**
     * @param String $name
     * @return AbstractRenderConfigEntry[]
     */
    protected function getField(String $name) {
        $fields = [];
        foreach ($this->info as $key => $field) {
            if ($name === $field->fieldName) {
                $fields[] = $field;
            }
        }
        return $fields;
    }

    /**
     * @param ViewFieldInfo $viewFieldInfo
     */
    public function orderFields($viewFieldInfo) {
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

    public function isEmpty(SolrMarc $solrMarc): bool {
        $groupIsEmpty = true;
        foreach ($this->entries() as $renderElem) {
            if (!$renderElem->isEmpty($solrMarc)) {
                $groupIsEmpty = false;
                break;
            }
        }
        return $groupIsEmpty;
    }
}