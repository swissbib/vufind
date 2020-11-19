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
        $this->renderMode = GroupEntry::$RENDER_MODE_LINE;
    }

    public function setInlineRenderMode() {
        $this->renderMode = GroupEntry::$RENDER_MODE_INLINE;
    }

    /**
     * Render each group element in its own html container.
     * @return bool
     */
    public function isLineRenderMode() {
        return $this->renderMode == GroupEntry::$RENDER_MODE_LINE;
    }

    /**
     * Render all group elements in one line.
     * @return bool
     */
    public function isInlineRenderMode() {
        return $this->renderMode == GroupEntry::$RENDER_MODE_INLINE;
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
}

class GroupEntry extends RenderConfigEntry {

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
}

class RenderConfig {
    protected $info = [];

    public static $IGNORE_INDICATOR = -1;

    protected function buildKey(int $marcIndex, int $indicator1, int $indicator2) {
        return $marcIndex . "-" . $indicator1 . "-" . $indicator2;
    }

    public function addGroup(GroupEntry $groupEntry) {
        $key = $this->buildKey($groupEntry->marcIndex, $groupEntry->indicator1, $groupEntry->indicator2);
        $this->info[$key] = $groupEntry;
    }

    public function addSingle(SingleEntry $entry) {
        $key = $this->buildKey($entry->marcIndex, $entry->indicator1, $entry->indicator2);
        $this->info[$key] = $entry;
    }

    /**
     * @param int $marcIndex
     * @param int $indicator1 set to -1 if not relevant
     * @param int $indicator2 set to -1 if not relevant
     * @return GroupEntry | SingleEntry
     */
    public function getEntry(int $marcIndex, int $indicator1 = -1, int $indicator2 = -1) {
        $key = $this->buildKey($marcIndex, $indicator1, $indicator2);
        return $this->info[$key];
    }
}
