<?php
/**
 * Created by IntelliJ IDEA.
 * User: ballmann
 * Date: 12/4/20
 * Time: 8:30 AM
 */

namespace SwissCollections\RenderConfig;

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

    public function addEntry(AbstractRenderConfigEntry &$entry) {
        if ($entry instanceof CompoundEntry) {
            $this->addCompound($entry);
        } else if ($entry instanceof SingleEntry) {
            $this->addSingle($entry);
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
     * @return null|AbstractRenderConfigEntry
     */
    public function getField(String $name) {
        foreach ($this->info as $key => $field) {
            if ($name === $field->labelKey) {
                return $field;
            }
        }
        return null;
    }

    /**
     * @param String[] $fieldOrder
     * @param String $groupName
     * @param Callable $subfieldOrderProvider with $groupName x $fieldName x $subfieldName
     */
    public function orderFields($fieldOrder, $subfieldOrderProvider) {
        $newFields = [];
        foreach ($fieldOrder as $key => $fieldName) {
            $gc = $this->getField($fieldName);
            if ($gc) {
                $newFields[$key] = $gc;
                $gc->orderEntries($subfieldOrderProvider($this->name, $fieldName));
            }
        }
        foreach ($this->info as $key => $field) {
            if (!in_array($field->labelKey, $fieldOrder)) {
                $newFields[$key] = $field;
                $field->orderEntries($subfieldOrderProvider($this->name, $field->labelKey));
            }
        }
        $this->info = $newFields;
    }
}