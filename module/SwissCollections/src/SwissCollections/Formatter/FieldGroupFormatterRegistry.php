<?php
/**
 * SwissCollections: FieldGroupFormatterRegistry.php
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

use SwissCollections\RecordDriver\FieldGroupRenderContext;
use SwissCollections\RenderConfig\AbstractRenderConfigEntry;
use SwissCollections\RenderConfig\FormatterConfig;

/**
 * Registry of all field group formatters.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\Formatter
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class FieldGroupFormatterRegistry
{
    /**
     * The map of field group formatters.
     *
     * @var array<string,FieldGroupFormatter>
     */
    protected $registry;

    /**
     * Register a given field group formatter.
     *
     * @param string              $name the formatter's name
     * @param FieldGroupFormatter $ff   the formatter to register
     *
     * @return void
     */
    public function register(string $name, FieldGroupFormatter $ff)
    {
        $this->registry[$name] = $ff;
    }

    /**
     * Get a field group formatter by name.
     *
     * @param string $name the formatter's name
     *
     * @return null|FieldGroupFormatter
     */
    public function get(String $name)
    {
        return $this->registry[$name];
    }

    /**
     * Apply a field group formatter.
     *
     * @param FormatterConfig             $groupFormatter the formatter to apply
     * @param AbstractRenderConfigEntry[] $data           the values to render
     * @param FieldGroupRenderContext     $context        the render context
     *
     * @return void
     */
    public function applyFormatter($groupFormatter, &$data, &$context)
    {
        $context->formatterConfig = null;
        $ff = $this->get($groupFormatter->getFormatterName());
        if (!empty($ff)) {
            $context->formatterConfig = $groupFormatter;
            $ff->render($data, $context);
        } else {
            echo "<!-- ERROR: Unknown field group formatter: '"
                . $groupFormatter->getFormatterName() . "' -->\n";
        }
    }
}
