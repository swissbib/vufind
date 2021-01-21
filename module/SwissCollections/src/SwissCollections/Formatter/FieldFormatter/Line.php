<?php
/**
 * SwissCollections: Line.php
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
 * @package  SwissCollections\templates\RecordDriver\SolrMarc\FieldFormatter
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\Formatter\FieldFormatter;

use SwissCollections\Formatter\FieldFormatter;
use SwissCollections\Formatter\FieldFormatterData;
use SwissCollections\RecordDriver\FieldRenderContext;
use SwissCollections\RenderConfig\FormatterConfig;

/**
 * Render subfield values line by line to html.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\templates\RecordDriver\SolrMarc\FieldFormatter
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class Line extends FieldFormatter
{
    /**
     * Render subfield values to html.
     *
     * @param string               $fieldName       the field's name
     * @param FieldFormatterData[] $fieldDataList   the field's values
     * @param FormatterConfig      $formatterConfig the field formatter's config
     * @param FieldRenderContext   $context         the render context
     *
     * @return void
     */
    public function render(
        $fieldName, $fieldDataList, $formatterConfig, $context
    ): void {
        echo $this->phpRenderer->render(
            '/RecordDriver/SolrMarc/FieldFormatter/Line',
            [
                'fieldDataList' => &$fieldDataList,
                'fieldName' => $fieldName,
                'formatter' => $this,
                'context' => $context,
                'formatterConfig' => $formatterConfig,
            ]
        );
    }
}
