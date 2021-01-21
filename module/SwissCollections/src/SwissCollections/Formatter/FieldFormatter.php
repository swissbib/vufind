<?php
/**
 * SwissCollections: FieldFormatter.php
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

use Laminas\View\Renderer\PhpRenderer;
use SwissCollections\RecordDriver\FieldRenderContext;
use SwissCollections\RenderConfig\FormatterConfig;

/**
 * Abstract top class of all field formatters.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\Formatter
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
abstract class FieldFormatter
{
    /**
     * Renders given values to html.
     *
     * @param string               $fieldName       the field's name
     * @param FieldFormatterData[] $fieldDataList   the field's values
     * @param FormatterConfig      $formatterConfig the field formatter's config
     * @param FieldRenderContext   $context         the render context
     *
     * @return void
     */
    public abstract function render(
        $fieldName, $fieldDataList, $formatterConfig, $context
    ): void;

    /**
     * Helper method to render one subfield to html.
     *
     * @param FieldFormatterData $fd      the information to render
     * @param FieldRenderContext $context the render context
     *
     * @return void
     */
    public function outputValue(
        FieldFormatterData $fd, FieldRenderContext $context
    ): void {
        $formatterConfig = $fd->renderConfig->getFormatterConfig();
        // "null" for lookupKey should be OK, because non-sequence fields (see SequencesEntry) should not contain duplicates
        $context->applySubfieldFormatter(
            null, $fd, $formatterConfig, $fd->renderConfig->labelKey
        );
    }

    /**
     * Returns a formatter's name.
     *
     * @return string
     */
    public function __toString()
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}

