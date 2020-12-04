<?php
/**
 * Created by IntelliJ IDEA.
 * User: ballmann
 * Date: 12/4/20
 * Time: 8:29 AM
 */

namespace SwissCollections\RenderConfig;

class CompoundEntry extends AbstractRenderConfigEntry {

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

    public function get(String $name) {
        foreach ($this->elements as $element) {
            if ($name === $element->labelKey) {
                return $element;
            }
        }
        return null;
    }

    /**
     * @param String[] $entryOrder
     */
    public function orderEntries($entryOrder) {
        $newEntries = [];
        foreach ($entryOrder as $fieldName) {
            $e = $this->get($fieldName);
            if ($e) {
                $newEntries[] = $e;
            }
        }
        foreach ($this->elements as $element) {
            if (!in_array($element->labelKey, $entryOrder)) {
                $newEntries[] = $element;
            }
        }
        $this->elements = $newEntries;
    }
}