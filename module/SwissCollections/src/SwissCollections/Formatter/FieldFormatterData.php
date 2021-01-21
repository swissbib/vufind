<?php
/**
 * SwissCollections: FieldFormatterData.php
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
 * @package  SwissCollections\Formatter
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\Formatter;

use SwissCollections\RecordDriver\SubfieldRenderData;
use SwissCollections\RenderConfig\SingleEntry;

/**
 * This class contains all information to render one marc subfield value.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\Formatter
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class FieldFormatterData
{
    /**
     * The render configuration.
     *
     * @var SingleEntry
     */
    public $renderConfig;

    /**
     * The value to render.
     *
     * @var SubfieldRenderData
     */
    public $subfieldRenderData;

    /**
     * FieldFormatterData constructor.
     *
     * @param SingleEntry        $renderConfig       the marc subfield's render configuration
     * @param SubfieldRenderData $subfieldRenderData the marc subfield value
     */
    public function __construct(
        SingleEntry $renderConfig, SubfieldRenderData $subfieldRenderData
    ) {
        $this->renderConfig = $renderConfig;
        $this->subfieldRenderData = $subfieldRenderData;
    }

    /**
     * Returns a string represenation.
     *
     * @return string
     */
    public function __toString()
    {
        return "FieldFormatterData{" . $this->renderConfig . ","
            . $this->subfieldRenderData . "}";
    }
}
