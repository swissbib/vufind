<?php
/**
 * SwissCollections: SubfieldFormatter.php
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
use SwissCollections\RecordDriver\SubfieldRenderData;
use SwissCollections\RenderConfig\SingleEntry;

/**
 * Abstract top class of all subfield formatters.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\Formatter
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
abstract class SubfieldFormatter
{
    /**
     * "vufind"'s renderer.
     *
     * @var PhpRenderer
     */
    protected $phpRenderer;

    /**
     * FieldFormatter constructor.
     *
     * @param PhpRenderer $phpRenderer "vufind"'s renderer.
     */
    public function __construct(PhpRenderer $phpRenderer)
    {
        $this->phpRenderer = $phpRenderer;
    }

    /**
     * Renders given values to html.
     *
     * @param String             $fieldName the field's name (not the name of the marc subfield!)
     * @param FieldFormatterData $fieldData the value to render
     * @param FieldRenderContext $context   the render context
     *
     * @return void
     */
    public abstract function render($fieldName, $fieldData, $context): void;

    /**
     * Returns the formatter's name.
     *
     * @return string
     */
    public abstract function getName(): String;
}

/**
 * Registry of all subfield formatters.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\Formatter
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class SubfieldFormatterRegistry
{
    /**
     * The map of subfield formatters.
     *
     * @var array<string,SubfieldFormatter>
     */
    protected $registry;

    /**
     * Register a given subfield formatter.
     *
     * @param SubfieldFormatter $ff the formatter to register
     *
     * @return void
     */
    public function register(SubfieldFormatter $ff)
    {
        $this->registry[$ff->getName()] = $ff;
    }

    /**
     * Get a subfield formatter by name.
     *
     * @param string $name the formatter's name
     *
     * @return null|SubfieldFormatter
     */
    public function get(string $name)
    {
        return $this->registry[$name];
    }

    /**
     * Apply a field formatter.
     *
     * @param string             $formatterKey the formatter to apply
     * @param string             $fieldName    the field's name (not the name of the marc subfield)
     * @param FieldFormatterData $fieldData    the value to render
     * @param FieldRenderContext $context      the render context
     *
     * @return void
     */
    public function applyFormatter(
        $formatterKey, $fieldName, $fieldData, &$context
    ) {
        $ff = $this->get($formatterKey);
        if (!empty($ff)) {
            $ff->render($fieldName, $fieldData, $context);
        } else {
            echo "<!-- ERROR: Unknown subfield formatter: '$formatterKey' -->\n";
        }
    }
}
