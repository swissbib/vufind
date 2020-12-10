<?php

namespace SwissCollections\RecordDriver;

class SubfieldRenderData {
    /**
     * @var String
     */
    public $value;

    /**
     * @var bool
     */
    public $escHtml;

    /**
     * @var int
     */
    public $ind1;

    /**
     * @var int
     */
    public $ind2;

    /**
     * SubfieldRenderData constructor.
     * @param String|null $value
     * @param bool $escHtml
     * @param int $ind1
     * @param int $ind2
     */
    public function __construct($value, bool $escHtml, int $ind1, int $ind2) {
        $this->value = $value;
        $this->escHtml = $escHtml;
        $this->ind1 = $ind1;
        $this->ind2 = $ind2;
    }

    public function emptyValue(): bool {
        if (empty($this->value)) {
            return true;
        }
        return empty(trim("" . $this->value));
    }

    public function asLookupKey(): String {
        return "|" . $this->value . "|" . $this->ind1 . "|" . $this->ind2 . "|";
    }
}