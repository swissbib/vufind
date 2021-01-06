<?php
/**
 * Created by IntelliJ IDEA.
 * User: ballmann
 * Date: 12/4/20
 * Time: 8:30 AM
 */

namespace SwissCollections\RenderConfig;

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

    protected function buildKey(int $marcIndex, int $indicator1, int $indicator2) {
        return $marcIndex . "-" . $indicator1 . "-" . $indicator2;
    }

    public function addCompound(CompoundEntry &$groupEntry) {
        $key = $this->buildKey($groupEntry->marcIndex, $groupEntry->indicator1, $groupEntry->indicator2);
        $this->info[$key] = $groupEntry;
    }

    public function addSingle(SingleEntry &$entry) {
        $key = $this->buildKey($entry->marcIndex, $entry->indicator1, $entry->indicator2);
        $this->info[$key] = $entry;
    }

    public function addSequences(SequencesEntry &$entry) {
        $key = $this->buildKey($entry->marcIndex, $entry->indicator1, $entry->indicator2);
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
     * @param int $marcIndex
     * @param int $indicator1 set to -1 if not relevant
     * @param int $indicator2 set to -1 if not relevant
     * @return AbstractRenderConfigEntry
     */
    public function getEntry(int $marcIndex, int $indicator1 = -1, int $indicator2 = -1) {
        $key = $this->buildKey($marcIndex, $indicator1, $indicator2);
        return $this->info[$key];
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
                $newFields[$this->buildKey($gc->marcIndex, $gc->indicator1, $gc->indicator2)] = $gc;
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
}