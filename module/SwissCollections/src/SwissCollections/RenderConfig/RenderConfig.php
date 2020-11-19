<?php namespace SwissCollections\RenderConfig;

abstract class RenderConfigEntry {
    public $labelKey;
    public $marcIndex;
    public $indicator1;
    public $indicator2;

    /**
     * @var string $renderMode is either 'line' (default) or 'inline'
     */
    public $renderMode;
    public static $RENDER_MODE_LINE = "line";
    public static $RENDER_MODE_INLINE = "inline";

    public function __construct($labelKey, $marcIndex, $indicator1, $indicator2) {
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
    public function __construct($labelKey, $marcIndex, $subfieldName, $indicator1 = -1, $indicator2 = -1) {
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
    public function __construct($labelKey, $marcIndex, $indicator1 = -1, $indicator2 = -1) {
        parent::__construct($labelKey, $marcIndex, $indicator1, $indicator2);
        $this->setLineRenderMode();
    }

    public function addElement($labelKey, $subfieldName) {
        $singleEntry = new SingleEntry($labelKey, $this->marcIndex, $subfieldName, $this->indicator1, $this->indicator2);
        $singleEntry->renderMode = $this->renderMode;
        array_push($this->elements, $singleEntry);
    }
}

class RenderConfig {
    protected $info = [];

    public static $IGNORE_INDICATOR = -1;

    protected function buildKey($marcIndex, $indicator1, $indicator2) {
        return $marcIndex . "-" . $indicator1 . "-" . $indicator2;
    }

    /**
     * @param GroupEntry $groupEntry
     */
    public function addGroup($groupEntry) {
        $key = $this->buildKey($groupEntry->marcIndex, $groupEntry->indicator1, $groupEntry->indicator2);
        $this->info[$key] = $groupEntry;
    }

    /**
     * @param int $marcIndex
     * @param int $indicator1 set to -1 if not relevant
     * @param int $indicator2 set to -1 if not relevant
     * @return GroupEntry | SingleEntry
     */
    public function getEntry($marcIndex, $indicator1 = -1, $indicator2 = -1) {
        $key = $this->buildKey($marcIndex, $indicator1, $indicator2);
        return $this->info[$key];
    }
}