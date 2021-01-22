<?php
/**
 * SwissCollections: ResultListViewFieldInfo.php
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
 * @package  SwissCollections\RecordDriver
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\RecordDriver;

use Laminas\View\Helper\AbstractHelper;
use SwissCollections\RenderConfig\CompoundEntry;
use SwissCollections\RenderConfig\FormatterConfig;
use SwissCollections\RenderConfig\RenderGroupConfig;

/**
 * Represents all information read from "result-list-entry-fields.yaml"
 * which is used in the result list view for every search hit.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RecordDriver
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class ResultListViewFieldInfo extends AbstractHelper
{
    /**
     * Raw content of "result-list-entry-fields.yaml".
     *
     * @var mixed
     */
    protected $resultListViewFieldInfo;

    /**
     * The parsed info from {@link ResultListViewFieldInfo::$resultListViewFieldInfo}
     *
     * @var array array of doc type string to {@link RenderGroupConfig}[]
     */
    protected $renderInfo;

    public static $RENDER_INFO_MARC = "marc";
    public static $RENDER_INFO_MARC_INDEX = "index";
    public static $RENDER_INFO_MARC_LABEL = "labelKey";
    public static $RENDER_INFO_MARC_FORMATTER = "formatter";
    public static $RENDER_INFO_MARC_FORMATTER_NAME = "name";
    public static $RENDER_INFO_MARC_FORMATTER_SEPARATOR = "separator";
    public static $RENDER_INFO_MARC_FORMATTER_REPEATED = "repeated";
    public static $RENDER_INFO_MARC_FORMATTER_SUBFIELDS = "entries";

    public static $FIELD_TITEL = "Titel";
    public static $FIELD_PERSONS = "Personen";
    public static $FIELD_TIME = "Zeiten";
    public static $FIELD_IMPORTANT = "Wichtig";
    public static $FIELD_SIGNATURE = "Signatur";

    /**
     * ViewFieldInfo constructor.
     *
     * @param mixed $detailViewFieldInfo the read in information
     */
    public function __construct($resultListViewFieldInfo)
    {
        $this->resultListViewFieldInfo = $resultListViewFieldInfo;
        $this->renderInfo = $this->parse();
        // not needed anymore ...
        $this->resultListViewFieldInfo = null;
    }

    /**
     * Get a group's configuration.
     *
     * @param string $name the document type's name
     *
     * @return mixed | null
     */
    protected function getDocTypeInfo(string $name)
    {
        return $this->getDocumentTypesInfo()[$name];
    }

    /**
     * Get the field info from a doc type info.
     * Read from {@link ViewFieldInfo::$RENDER_INFO_FIELDS}.
     *
     * @param array  $docTypeInfo data returned by getDocTypeInfo()
     * @param string $fieldName   the field's name is either {@link ResultListViewFieldInfo::FIELD_TITEL},
     *                            {@link ResultListViewFieldInfo::FIELD_PERSONS},
     *                            {@link ResultListViewFieldInfo::FIELD_TIME},
     *                            {@link ResultListViewFieldInfo::FIELD_IMPORTANT} or
     *                            {@link ResultListViewFieldInfo::FIELD_SIGNATURE}
     *
     * @return array an array of arrays with "index" etc.
     */
    protected function getMarcFieldInfo($docTypeInfo, $fieldName)
    {
        $marcInfo = [];
        if (!empty($docTypeInfo)) {
            $fieldViewInfo = $docTypeInfo[$fieldName];
            if (!empty($fieldViewInfo)) {
                $marcInfo = $fieldViewInfo[self::$RENDER_INFO_MARC];
            }
        }
        return $marcInfo;
    }

    /**
     * Returns a field formatter's config (from
     * {@link ResultListViewFieldInfo::$$RENDER_INFO_MARC_FORMATTER}).
     *
     * @param array|null $marcFieldInfo one element returned by getMarcFieldInfo()
     *
     * @return FormatterConfig
     */
    protected function getMarcFieldFormatter($marcFieldInfo)
    {
        $config = null;
        if (!empty($marcFieldInfo)) {
            $config = $marcFieldInfo[self::$RENDER_INFO_MARC_FORMATTER];
        }
        if (empty($config)) {
            $config = [];
        }
        $fc = new FormatterConfig('inline', $config);
        return $fc;
    }

    /**
     * Returns a field marc's index (from
     * {@link ResultListViewFieldInfo::$$RENDER_INFO_MARC_INDEX}).
     *
     * @param array|null $marcFieldInfo one element returned by getMarcFieldInfo()
     *
     * @return int
     */
    protected function getMarcFieldIndex($marcFieldInfo)
    {
        return $marcFieldInfo[self::$RENDER_INFO_MARC_INDEX];
    }

    /**
     * Get string representation of this instance.
     *
     * @return string
     */
    public function __toString()
    {
        return "ResultListViewFieldInfo{" . print_r(
                $this->renderInfo, true
            ) . "}";
    }

    /**
     * Parse the yaml file to groups of render config entries suitable for a
     * call to {@link FieldGroupFormatterRegistry::applyFormatter}
     *
     * @return array array of doc type to {@link RenderGroupConfig}[]
     */
    protected function parse()
    {
        $docTypeRenderGroupMap = [];
        foreach ($this->getDocumentTypesInfo() as $docType => $dtInfo) {
            $renderGroups = [];
            foreach ($dtInfo as $fieldName => $fieldConfig) {
                $renderGroupConfig = new RenderGroupConfig($fieldName);
                $marcFieldsInfo = $fieldConfig[self::$RENDER_INFO_MARC];
                if (!empty($marcFieldsInfo)) {
                    foreach ($marcFieldsInfo as $marcInfo) {
                        $fieldFormatter = $this->getMarcFieldFormatter(
                            $marcInfo
                        );
                        // echo "<!-- SFCL1: " . print_r($marcInfo, true) . " -->";
                        $marcIndex = $this->getMarcFieldIndex($marcInfo);
                        $renderConfig = new CompoundEntry(
                            "ResultList",
                            $fieldName,
                            $fieldName . "1",
                            $marcIndex,
                            $fieldFormatter
                        );
                        $subfieldFormatterConfigList
                            = $fieldFormatter->getEntryOrder("simple");
                        // echo "<!-- SFCL2: " . print_r($subfieldFormatterConfigList, true) . " -->";
                        foreach ($subfieldFormatterConfigList as $sc) {
                            $renderConfig->addElement(
                                $fieldName, $sc->fieldName
                            );
                        }
                        $renderGroupConfig->addCompound($renderConfig);
                    }
                }
                $renderGroups[] = $renderGroupConfig;
            }
            $docTypeRenderGroupMap[$docType] = $renderGroups;
        }
        return $docTypeRenderGroupMap;
    }

    /**
     * Get the configuration of all document types.
     *
     * @return mixed
     */
    protected function getDocumentTypesInfo()
    {
        return $this->resultListViewFieldInfo['document-types'];
    }

    /**
     * Get the parsed render info.
     *
     * @return array array array of doc type string to grouped render elements {@link AbstractRenderConfigEntry}[][]
     */
    public function getRenderInfo(): array
    {
        return $this->renderInfo;
    }
}