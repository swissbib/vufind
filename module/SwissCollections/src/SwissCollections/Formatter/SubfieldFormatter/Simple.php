<?php
/**
 * SwissCollections: Simple.php
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
 * @package  SwissCollections\templates\RecordDriver\SolrMarc\SubfieldFormatter
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\Formatter\SubfieldFormatter;

use SwissCollections\Formatter\FieldFormatterData;
use SwissCollections\Formatter\SubfieldFormatter;
use SwissCollections\RecordDriver\FieldRenderContext;
use SwissCollections\RenderConfig\FormatterConfig;

/**
 * Render subfield value as inline html.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\templates\RecordDriver\SolrMarc\SubfieldFormatter
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class Simple extends SubfieldFormatter
{
    /**
     * Html tag to render.
     *
     * @string
     */
    protected $tag = "span";

    /**
     * Render a subfield value to html.
     *
     * @param string             $fieldName       the field's name
     * @param FieldFormatterData $data            the subfield's value
     * @param FormatterConfig    $formatterConfig the formatter config to apply
     * @param FieldRenderContext $context         the render context
     *
     * @return void
     */
    public function render($fieldName, $data, $formatterConfig, $context): void
    {
        echo $context->phpRenderer->render(
            '/RecordDriver/SolrMarc/SubfieldFormatter/Simple',
            [
                'fieldData' => &$data,
                'fieldName' => $fieldName,
                'formatter' => $this,
                'context' => $context,
                'formatterConfig' => $formatterConfig,
            ]
        );
    }

    /**
     * Get text html escaped.
     *
     * @param string             $text            the plain text for html output
     * @param FormatterConfig    $formatterConfig the formatter config to apply
     * @param FieldRenderContext $context         the render context
     *
     * @return string
     */
    public function getHtml($text, $formatterConfig, $context): string
    {
        return $context->phpRenderer->escapeHtml($text);
    }

    /**
     * Get the html tag's name.
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }
}