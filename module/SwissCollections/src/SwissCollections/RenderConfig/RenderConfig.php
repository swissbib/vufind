<?php namespace SwissCollections\RenderConfig;

abstract class RenderConfigEntry {
    /**
     * @var String
     */
    public $labelKey;
    /**
     * @var int
     */
    public $marcIndex;
    /**
     * @var int
     */
    public $indicator1;
    /**
     * @var int
     */
    public $indicator2;

    /**
     * @var String $renderMode is either 'line' (default) or 'inline'
     */
    public $renderMode;
    public static $RENDER_MODE_LINE = "line";
    public static $RENDER_MODE_INLINE = "inline";

    public static $UNKNOWN_INDICATOR = -1;

    public function __construct(String $labelKey, int $marcIndex, int $indicator1, int $indicator2) {
        $this->labelKey = $labelKey;
        $this->marcIndex = $marcIndex;
        $this->indicator1 = $indicator1;
        $this->indicator2 = $indicator2;
        $this->setLineRenderMode();
    }

    public function info() {
        return $this->labelKey . "[" . $this->marcIndex . "," . $this->indicator1 . "," . $this->indicator2 . "]";
    }

    public function setLineRenderMode() {
        $this->renderMode = CompoundEntry::$RENDER_MODE_LINE;
    }

    public function setInlineRenderMode() {
        $this->renderMode = CompoundEntry::$RENDER_MODE_INLINE;
    }

    /**
     * Render each group element in its own html container.
     * @return bool
     */
    public function isLineRenderMode() {
        return $this->renderMode == CompoundEntry::$RENDER_MODE_LINE;
    }

    /**
     * Render all group elements in one line.
     * @return bool
     */
    public function isInlineRenderMode() {
        return $this->renderMode == CompoundEntry::$RENDER_MODE_INLINE;
    }

    public function __toString() {
        return "RenderConfigEntry{" . $this->labelKey . ","
            . $this->marcIndex . "," . $this->indicator1 . "," . $this->indicator2 . "}";
    }
}

class SingleEntry extends RenderConfigEntry {
    protected $subfieldName;

    /**
     * SingleEntry constructor.
     * @param String $labelKey
     * @param int $marcIndex
     * @param String $subfieldName
     * @param int $indicator1 set to -1 if not relevant
     * @param int $indicator2 set to -1 if not relevant
     */
    public function __construct(String $labelKey, int $marcIndex, String $subfieldName = null,
                                int $indicator1 = -1, int $indicator2 = -1) {
        parent::__construct($labelKey, $marcIndex, $indicator1, $indicator2);
        $this->subfieldName = $subfieldName;
    }

    public function buildMap() {
        $result = [];
        $result[$this->subfieldName] = "value";
        return $result;
    }

    public function info() {
        return $this->labelKey . "[" . $this->marcIndex
            . "," . $this->indicator1 . "," . $this->indicator2 . "," . $this->subfieldName . "]";
    }

    public function __toString() {
        return "SingleEntry{" . parent::__toString() . "," . $this->subfieldName . "}";
    }
}

class CompoundEntry extends RenderConfigEntry {

    /**
     * @var SingleEntry[]
     */
    public $elements = [];

    /**
     * GroupEntry constructor.
     * @param String $labelKey
     * @param int $marcIndex
     * @param int $indicator1 set to -1 if not relevant
     * @param int $indicator2 set to -1 if not relevant
     */
    public function __construct(String $labelKey, int $marcIndex, int $indicator1 = -1, int $indicator2 = -1) {
        parent::__construct($labelKey, $marcIndex, $indicator1, $indicator2);
        $this->setLineRenderMode();
    }

    public function addElement(String $labelKey, String $subfieldName) {
        $singleEntry = new SingleEntry($labelKey, $this->marcIndex, $subfieldName, $this->indicator1, $this->indicator2);
        $singleEntry->renderMode = $this->renderMode;
        array_push($this->elements, $singleEntry);
    }

    public function __toString() {
        $s = "CompoundEntry{[\n";
        foreach ($this->elements as $e) {
            $s = $s . "\t\t\t" . $e . ",\n";
        }
        return $s . "]}";
    }
}

class RenderGroupConfig {
    /**
     * @var RenderConfigEntry[]
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

    public function addEntry(RenderConfigEntry &$entry) {
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
     * @return RenderConfigEntry
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
     * @return null|RenderConfigEntry
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
     */
    public function orderFields($fieldOrder) {
        $newFields = [];
        foreach ($fieldOrder as $key => $fieldName) {
            $gc = $this->getField($fieldName);
            if ($gc) {
                $newFields[$key] = $gc;
            }
        }
        foreach ($this->info as $key => $field) {
            if (!in_array($field->labelKey, $fieldOrder)) {
                $newFields[$key] = $field;
            }
        }
        $this->info = $newFields;
    }
}

class RenderConfig {
    /**
     * @var RenderGroupConfig[]
     */
    protected $info = [];

    public function add(RenderGroupConfig $entry) {
        $this->info[] = $entry;
    }

    /**
     * @return RenderGroupConfig[]
     */
    public function entries() {
        return $this->info;
    }

    public function __toString() {
        $s = "RenderConfig{[\n";
        foreach ($this->info as $e) {
            $s = $s . "\t" . $e . ",\n";
        }
        return $s . "]}";
    }

    /**
     * @param String $groupName
     * @return null|RenderGroupConfig
     */
    public function get(String $groupName) {
        foreach ($this->info as $renderGroupConfig) {
            if ($groupName === $renderGroupConfig->getName()) {
                return $renderGroupConfig;
            }
        }
        return null;
    }

    /**
     * @param String[] $groupOrder
     * @param Callable $fieldOrderProvider with $groupName
     */
    public function orderGroups($groupOrder, $fieldOrderProvider) {
        $newGroups = [];
        foreach ($groupOrder as $groupName) {
            $gc = $this->get($groupName);
            if ($gc) {
                $newGroups[] = $gc;
                $gc->orderFields($fieldOrderProvider($groupName));
            }
        }
        foreach ($this->info as $renderGroupConfig) {
            if (!in_array($renderGroupConfig->getName(), $groupOrder)) {
                $newGroups[] = $renderGroupConfig;
                $renderGroupConfig->orderFields($fieldOrderProvider($groupName));
            }
        }
        $this->info = $newGroups;
    }
}