<?php
/**
 * Created by IntelliJ IDEA.
 * User: ballmann
 * Date: 12/4/20
 * Time: 8:24 AM
 */

namespace SwissCollections\RenderConfig;

abstract class AbstractRenderConfigEntry {
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
     * @var bool
     */
    public $repeated;

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
        $this->repeated = false;
    }

    public function setLineRenderMode() {
        $this->renderMode = AbstractRenderConfigEntry::$RENDER_MODE_LINE;
    }

    public function setInlineRenderMode() {
        $this->renderMode = AbstractRenderConfigEntry::$RENDER_MODE_INLINE;
    }

    /**
     * Render each group element in its own html container.
     * @return bool
     */
    public function isLineRenderMode() {
        return $this->renderMode == AbstractRenderConfigEntry::$RENDER_MODE_LINE;
    }

    /**
     * Render all group elements in one line.
     * @return bool
     */
    public function isInlineRenderMode() {
        return $this->renderMode == AbstractRenderConfigEntry::$RENDER_MODE_INLINE;
    }

    public function __toString() {
        return "RenderConfigEntry{" . $this->labelKey . ","
            . $this->marcIndex . "," . $this->indicator1 . "," . $this->indicator2
            . "," . $this->repeated . "}";
    }

    public function orderEntries() {
        // NOP
    }
}