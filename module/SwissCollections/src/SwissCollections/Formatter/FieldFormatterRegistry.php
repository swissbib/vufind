<?php
/**
 * SwissCollections: FieldFormatterRegistry.php
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

use SwissCollections\RecordDriver\FieldRenderContext;
use SwissCollections\RenderConfig\FormatterConfig;

/**
 * Registry of all field formatters.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\Formatter
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class FieldFormatterRegistry
{
    /**
     * The map of field formatters.
     *
     * @var array<string,FieldFormatter>
     */
    protected $registry;

    /**
     * Register a given field formatter.
     *
     * @param FieldFormatter $ff the formatter to register
     *
     * @return void
     */
    public function register(FieldFormatter $ff)
    {
        $this->registry[$ff->getName()] = $ff;
    }

    /**
     * Get a field formatter by name.
     *
     * @param string $name a formatter's name
     *
     * @return null|FieldFormatter
     */
    public function get(string $name)
    {
        return $this->registry[$name];
    }

    /**
     * Apply a field formatter.
     *
     * @param FormatterConfig      $formatterConfig the formatter's config
     * @param string               $fieldName       the field's name to render
     * @param FieldFormatterData[] $data            the field's values
     * @param FieldRenderContext   $context         the render context
     *
     * @return void
     */
    public function applyFormatter(
        $formatterConfig, $fieldName, $data, &$context
    ) {
        $formatterKey = $formatterConfig->getFormatterName();
        $ff = $this->get($formatterKey);
        if (!empty($ff)) {
            $ff->render($fieldName, $data, $formatterConfig, $context);
        } else {
            echo "<!-- ERROR: Unknown field formatter: '$formatterKey' -->\n";
        }
    }
}
