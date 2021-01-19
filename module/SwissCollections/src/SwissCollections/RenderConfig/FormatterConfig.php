<?php
/**
 * SwissCollections: FormatterConfig.php
 *
 * PHP version 7
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swisscollections.org  / http://www.swisscollections.ch / http://www.ub.unibas.ch
 *
 * Date: 1/12/20
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RenderConfig
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\RenderConfig;

/**
 * Class FormatterConfig.
 *
 * This class represents all configuration options of "formatter:" entries
 * in "detail-view-field-structure.yaml".
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RenderConfig
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class FormatterConfig
{
    /**
     * The formatter's name if none is specified.
     *
     * @var String
     */
    public $formatterNameDefault;

    /**
     * The whole formatter's configuration ("formatter:").
     *
     * @var mixed|null
     */
    protected $config;

    /**
     * The default value for "repeated:" if not specified
     *
     * @var boolean
     */
    public $repeatedDefault;

    /**
     * The html code to output before "repeated" field values.
     *
     * @var String
     */
    public $listStartHml;

    /**
     * The html code to output after "repeated" field values.
     *
     * @var String
     */
    public $listEndHml;

    /**
     * The default "separator" to use if none is specified
     *
     * @var String
     */
    public $separatorDefault;

    /**
     * FormatterConfig constructor.
     *
     * @param String     $formatterNameDefault the default formatter's name
     * @param mixed|null $config               the formatter's config from "formatter:"
     */
    public function __construct($formatterNameDefault, $config)
    {
        $this->formatterNameDefault = $formatterNameDefault;
        $this->config = $config;
        $this->repeatedDefault = false;
        $this->separatorDefault = ", ";
    }

    /**
     * For $name: "inline".
     *
     * @return String|null
     */
    public function getSeparator()
    {
        return $this->optionalConfigEntry("separator", $this->separatorDefault);
    }

    /**
     * Shall the field value be displayed in a list style? Uses
     * {@link FormatterConfig::repeatedDefault} if no "repeated:" was specified.
     *
     * @return bool
     */
    public function isRepeated(): bool
    {
        return $this->optionalConfigEntry("repeated", $this->repeatedDefault);
    }

    /**
     * Get the formatter's name. Uses
     * {@link FormatterConfig::formatterNameDefault} if no "name:" was specified.
     *
     * @return string
     */
    public function getFormatterName(): string
    {
        return $this->optionalConfigEntry("name", $this->formatterNameDefault);
    }

    /**
     * Set the default "repeated:" mode.
     *
     * @param bool $flag the value
     *
     * @return void
     */
    public function setRepeatedDefault(bool $flag)
    {
        $this->repeatedDefault = $flag;
    }

    /**
     * Returns a string represenation.
     *
     * @return string
     */
    public function __toString()
    {
        return "FormatterConfig{" . $this->formatterNameDefault
            . ",'" . $this->separatorDefault . "'"
            . "," . json_encode($this->repeatedDefault)
            . "," . print_r($this->config, true) . "}";
    }

    /**
     * Set the html code to output around all list items.
     *
     * @param string $start contains the html to render before all list items
     * @param string $end   contains the html to render after all list items
     *
     * @return void
     */
    public function setListHtml(string $start, string $end): void
    {
        $this->listStartHml = $start;
        $this->listEndHml = $end;
    }

    /**
     * Returns the "entries:" as objects.
     *
     * @param string $defaultEntryFormatterName the default field formatter's
     *                                          name to use none is specified
     *
     * @return FieldFormatterConfig[]
     */
    public function getEntryOrder($defaultEntryFormatterName)
    {
        $order = [];
        if (!empty($this->config["entries"])) {
            foreach ($this->config["entries"] as $entry) {
                $formatterName = $defaultEntryFormatterName;
                if (!empty($entry["formatter"])
                    && !empty($entry["formatter"]["name"])
                ) {
                    $formatterName = $entry["formatter"]["name"];
                }
                $order[] = new FieldFormatterConfig(
                    $entry["name"], $formatterName, $entry["formatter"]
                );
            }
        }
        return $order;
    }

    /**
     * Retrieves formatter for a given marc subfield. Returns
     * $defaultFormatterName if not specified. Inherits default separator and
     * repeated flag from $defaultFormatterConfig.
     *
     * @param string          $entryName              should match a "name:" in "entries:"
     * @param string          $defaultFormatterName   the default formatter name
     * @param FormatterConfig $defaultFormatterConfig the default formatter
     *
     * @return FormatterConfig
     */
    public function singleFormatter(
        $entryName, $defaultFormatterName, $defaultFormatterConfig
    ) {
        $config = [];
        if (!empty($this->config["entries"])) {
            foreach ($this->config["entries"] as $entry) {
                if ($entryName === $entry["name"]
                    && !empty($entry["formatter"])
                ) {
                    $config = $entry["formatter"];
                    break;
                }
            }
        }
        $r = new FormatterConfig($defaultFormatterName, $config);
        if (!array_key_exists("separator", $config)) {
            $r->separatorDefault = $defaultFormatterConfig->getSeparator();
        }
        if (!array_key_exists("repeated", $config)) {
            $r->repeatedDefault = $defaultFormatterConfig->isRepeated();
        }
        return $r;
    }

    /**
     * For $name: "sucheinstiege-person".
     *
     * @return String[]
     */
    public function getRoleOrder()
    {
        return $this->optionalConfigEntry("roleOrder", []);
    }

    /**
     * For $name: "sucheinstiege-person".
     *
     * @return String[]
     */
    public function getRoleMarcSubfields()
    {
        return $this->optionalConfigEntry("roleMarcSubfields", []);
    }

    /**
     * For $name: "sucheinstiege-person".
     *
     * @return String[]
     */
    public function getHiddenRoleMarcSubfields()
    {
        return $this->optionalConfigEntry("hiddenMarcSubfields", []);
    }

    /**
     * Helper method to return the default value if no specific option was set.
     *
     * @param string $key          the option's name
     * @param mixed  $defaultValue the default value
     *
     * @return mixed
     */
    public function optionalConfigEntry($key, $defaultValue)
    {
        $value = $defaultValue;
        if (array_key_exists($key, $this->config)) {
            $value = $this->config[$key];
        }
        return $value;
    }
}

/**
 * Class FieldFormatterConfig.
 *
 * This class represents all configuration options of "formatter:" entries
 * in "detail-view-field-structure.yaml". This class stores the name of the
 * field too.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RenderConfig
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class FieldFormatterConfig extends FormatterConfig
{
    /**
     * The field's name.
     *
     * @var string
     */
    public $fieldName;

    /**
     * FormatterConfig constructor.
     *
     * @param string     $fieldName     the field's name
     * @param string     $formatterName the field's formatter name
     * @param mixed|null $config        the formatter's configuration
     */
    public function __construct($fieldName, $formatterName, $config)
    {
        parent::__construct($formatterName, $config);
        $this->fieldName = $fieldName;
    }

    /**
     * Returns a string represenation.
     *
     * @return string
     */
    public function __toString()
    {
        return "FieldFormatterConfig{" . $this->fieldName . ","
            . parent::__toString() . "}";
    }
}