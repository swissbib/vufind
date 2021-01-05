<?php
/**
 * Created by IntelliJ IDEA.
 * User: ballmann
 * Date: 12/4/20
 * Time: 8:24 AM
 */

namespace SwissCollections\RenderConfig;

class FormatterConfig {
    /**
     * @var String
     */
    public $formatterNameDefault;

    /**
     * @var mixed|null
     */
    protected $config;

    /**
     * @var boolean
     */
    public $repeatedDefault;

    /**
     * @var String
     */
    public $listStartHml;

    /**
     * @var String
     */
    public $listEndHml;

    /**
     * @var String
     */
    public $separatorDefault;

    /**
     * FormatterConfig constructor.
     * @param String $formatterNameDefault
     * @param mixed|null $config
     */
    public function __construct($formatterNameDefault, $config) {
        $this->formatterNameDefault = $formatterNameDefault;
        $this->config = $config;
        $this->repeatedDefault = false;
        $this->separatorDefault = ", ";
    }

    /**
     * For $name: "inline".
     * @return String|null
     */
    public function getSeparator() {
        return $this->optionalConfigEntry("separator", $this->separatorDefault);
    }

    public function isRepeated(): bool {
        return $this->optionalConfigEntry("repeated", $this->repeatedDefault);
    }

    public function getFormatterName(): String {
        return $this->optionalConfigEntry("name", $this->formatterNameDefault);
    }

    public function setRepeatedDefault(bool $flag) {
        $this->repeatedDefault = $flag;
    }

    public function __toString() {
        return "FormatterConfig{" . $this->formatterNameDefault . "," . print_r($this->config, true) . "}";
    }

    public function setListHtml(String $start, String $end): void {
        $this->listStartHml = $start;
        $this->listEndHml = $end;
    }

    /**
     * @param String $defaultEntryFormatterName
     * @return FieldFormatterConfig[]
     */
    public function getEntryOrder($defaultEntryFormatterName) {
        $order = [];
        if (!empty($this->config["entries"])) {
            foreach ($this->config["entries"] as $entry) {
                $formatterName = $defaultEntryFormatterName;
                if (!empty($entry["formatter"]) && !empty($entry["formatter"]["name"])) {
                    $formatterName = $entry["formatter"]["name"];
                }
                $order[] = new FieldFormatterConfig($entry["name"], $formatterName, $entry["formatter"]);
            }
        }
        return $order;
    }

    /**
     * For $name: "sucheinstiege-person".
     * @return String[]
     */
    public function getRoleOrder() {
        return $this->optionalConfigEntry("roleOrder", []);
    }

    /**
     * For $name: "sucheinstiege-person".
     * @return String[]
     */
    public function getRoleMarcSubfields() {
        return $this->optionalConfigEntry("roleMarcSubfields", []);
    }

    /**
     * For $name: "sucheinstiege-person".
     * @return String[]
     */
    public function getHiddenRoleMarcSubfields() {
        return $this->optionalConfigEntry("hiddenMarcSubfields", []);
    }

    /**
     * @param String $key
     * @param mixed $defaultValue
     * @return mixed
     */
    protected function optionalConfigEntry($key, $defaultValue) {
        $value = $defaultValue;
        if (array_key_exists($key, $this->config)) {
            $value = $this->config[$key];
        }
        return $value;
    }
}

class FieldFormatterConfig extends FormatterConfig {
    /**
     * @var String
     */
    public $fieldName;

    /**
     * FormatterConfig constructor.
     * @param String $fieldName
     * @param String $formatterName
     * @param mixed|null $config
     */
    public function __construct($fieldName, $formatterName, $config) {
        parent::__construct($formatterName, $config);
        $this->fieldName = $fieldName;
    }

    public function __toString() {
        return "FieldFormatterConfig{" . $this->fieldName . "," . parent::__toString() . "}";
    }
}