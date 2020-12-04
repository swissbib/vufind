<?php
/**
 * Created by IntelliJ IDEA.
 * User: ballmann
 * Date: 12/4/20
 * Time: 8:28 AM
 */

namespace SwissCollections\RenderConfig;

class SingleEntry extends AbstractRenderConfigEntry {
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