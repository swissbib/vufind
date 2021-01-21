<?php
/**
 * SwissCollections: DefaultFormatter.php
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
 * @package  SwissCollections\templates\RecordDriver\SolrMarc\FieldGroupFormatter
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\Formatter\FieldGroupFormatter;

use SwissCollections\Formatter\FieldGroupFormatter;
use SwissCollections\RecordDriver\FieldGroupRenderContext;
use SwissCollections\RenderConfig\AbstractRenderConfigEntry;

/**
 * The default formatter to render a group of fields.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\templates\RecordDriver\SolrMarc\FieldGroupFormatter
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class DefaultFormatter extends FieldGroupFormatter
{
    public static $NAME = "default";

    /**
     * Render all fields to html.
     *
     * @param AbstractRenderConfigEntry[] $fieldDataList the field's render configuration
     * @param FieldGroupRenderContext     $context       the render context
     *
     * @return void
     */
    public function render(&$fieldDataList, &$context): void
    {
        echo $context->phpRenderer->render(
            '/RecordDriver/SolrMarc/FieldGroupFormatter/Default',
            [
                'fieldDataList' => &$fieldDataList,
                'formatter' => $this,
                'context' => $context,
            ]
        );
    }
}