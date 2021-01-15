<?php
/**
 * SwissCollections: SubfieldRenderData.php
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

/**
 * Represents a value to render.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RecordDriver
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class SubfieldRenderData
{
    /**
     * The text.
     *
     * @var String
     */
    public $value;

    /**
     * Output html escaped?
     *
     * @var bool
     */
    public $escHtml;

    /**
     * First indicator of the value.
     *
     * @var int
     */
    public $ind1;

    /**
     * Second indicator of the value.
     *
     * @var int
     */
    public $ind2;

    /**
     * SubfieldRenderData constructor.
     *
     * @param string|null $value   the text to render
     * @param bool        $escHtml should the text rendered escaped?
     * @param int         $ind1    the first indicator of the text
     * @param int         $ind2    the second indicator of the text
     */
    public function __construct($value, bool $escHtml, int $ind1, int $ind2)
    {
        $this->value = $value;
        $this->escHtml = $escHtml;
        $this->ind1 = $ind1;
        $this->ind2 = $ind2;
    }

    /**
     * Is something to render?
     *
     * @return bool
     */
    public function emptyValue(): bool
    {
        if (empty($this->value)) {
            return true;
        }
        return empty(trim("" . $this->value));
    }

    /**
     * Build a lookup key for this value.
     *
     * @return string
     */
    public function asLookupKey(): string
    {
        return "|" . $this->value . "|" . $this->ind1 . "|" . $this->ind2 . "|";
    }

    /**
     * Returns a string represenation.
     *
     * @return string
     */
    public function __toString()
    {
        return "SubfieldRenderData{" . $this->asLookupKey() . "|"
            . json_encode($this->escHtml) . "}";
    }
}