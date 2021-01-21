<?php
/**
 * SwissCollections: FieldGroupFormatter.php
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
use SwissCollections\RecordDriver\FieldGroupRenderContext;
use SwissCollections\RecordDriver\FieldRenderContext;
use SwissCollections\RenderConfig\AbstractRenderConfigEntry;

/**
 * Abstract top class of all field group formatters.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\Formatter
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
abstract class FieldGroupFormatter
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
     * @param PhpRenderer $phpRenderer vufind's renderer
     */
    public function __construct(PhpRenderer $phpRenderer)
    {
        $this->phpRenderer = $phpRenderer;
    }

    /**
     * Renders given values to html.
     *
     * @param AbstractRenderConfigEntry[] $fieldDataList the values to render
     * @param FieldGroupRenderContext     $context       the render context
     *
     * @return void
     */
    public abstract function render(&$fieldDataList, &$context): void;

    /**
     * Returns the formatter's name.
     *
     * @return string
     */
    public abstract function getName(): string;

    /**
     * Helper method to render one field to html.
     *
     * @param AbstractRenderConfigEntry $renderElem      the field's render configuration
     * @param FieldGroupRenderContext   $context         the render context
     * @param String                    $repeatStartHtml html to be output before list items
     * @param String                    $repeatEndHtml   html to be output after list items
     *
     * @return void
     */
    public function outputField(
        &$renderElem, &$context, $repeatStartHtml, $repeatEndHtml
    ): void {
        $fields = $context->solrMarc->getFieldValues($renderElem);
        if (!empty($fields)) {
            if ($renderElem->getFormatterConfig()->isRepeated()) {
                echo $repeatStartHtml;
            }
            $fieldContext = new FieldRenderContext(
                $context->fieldFormatterRegistry, $context->solrMarc,
                $context->subfieldFormatterRegistry
            );
            foreach ($fields as $field) {
                $renderElem->render($field, $fieldContext);
            }
            if ($renderElem->getFormatterConfig()->isRepeated()) {
                echo $repeatEndHtml;
            }
        }
    }

    /**
     * Converts a translate key to a well formed css class.
     *
     * @param String $labelKey the key to convert
     *
     * @return string
     */
    public static function labelKeyAsCssClass(String $labelKey): string
    {
        return preg_replace('/[. \/"§$%&()!=?+*~#\':,;]/', "_", $labelKey);
    }

    /**
     * Returns translated text for a given key. If no translation exists, the
     * key is returned, where all "." are replaced with "-" (allows html to
     * wrap).
     *
     * @param String $labelKey the key to translate
     *
     * @return string
     */
    public function translateLabelKey(String $labelKey): string
    {
        $label = $this->phpRenderer->translate(
            'page.detail.field.label.' . $labelKey
        );
        if (strpos($label, "page.detail.") !== false) {
            $label = preg_replace("/[.]/", "-", $label);
        }
        return $label;
    }
}

