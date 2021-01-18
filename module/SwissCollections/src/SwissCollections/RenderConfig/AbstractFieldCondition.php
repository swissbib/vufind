<?php
/**
 * SwissCollections: FieldCondition.php
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
 * @package  SwissCollections\RenderConfig
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\RenderConfig;

use SwissCollections\RecordDriver\SolrMarc;

/**
 * Abstract top class of field conditions.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RenderConfig
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
abstract class AbstractFieldCondition
{
    /**
     * Another condition.
     *
     * @var AbstractFieldCondition | null
     */
    protected $andCondition;

    /**
     * Checks the given field. Returns true if the condition is fulfilled.
     *
     * @param \File_MARC_Data_Field|\File_MARC_Control_Field $field    the marc field
     * @param SolrMarc                                       $solrMarc the marc record
     *
     * @return bool
     */
    protected abstract function check($field, $solrMarc): bool;

    /**
     * Checks the given field and all and'ed conditions. Returns true if all
     * conditions are fulfilled.
     *
     * @param \File_MARC_Data_Field|\File_MARC_Control_Field $field    the marc field
     * @param SolrMarc                                       $solrMarc the marc record
     *
     * @return bool
     */
    public function assertTrue($field, $solrMarc): bool
    {
        if (!$this->check($field, $solrMarc)) {
            return false;
        }
        if (!empty($this->andCondition)) {
            if (!$this->andCondition->assertTrue($field, $solrMarc)) {
                return false;
            }
        }
        // echo "<!-- " . $field->getTag() . " CONDITION OK: " . $this->allConditionsToString() . " -->";
        return true;
    }

    /**
     * Returns a string representation.
     *
     * @return string
     */
    public abstract function __toString();

    /**
     * Returns all conditions as string.
     *
     * @return string
     */
    public function allConditionsToString(): string
    {
        $allConditions = "" . $this;
        $c = $this;
        while ($c->andCondition) {
            $allConditions = $allConditions . "&&" . $c->andCondition;
            $c = $c->andCondition;
        }
        return $allConditions;
    }

    /**
     * Set another condition to test.
     *
     * @param AbstractFieldCondition $condition the condition to "and"
     *
     * @return void
     */
    public function setAndCondition(AbstractFieldCondition $condition)
    {
        if (empty($this->andCondition)) {
            $this->andCondition = $condition;
        } else {
            if (empty($condition->andCondition)) {
                $oldCondition = $this->andCondition;
                $this->andCondition = $condition;
                $condition->andCondition = $oldCondition;
            } else {
                $this->andCondition->setAndCondition($condition);
            }
        }
    }

    /**
     * Connect conditions with "and".
     *
     * @param AbstractFieldCondition|null $condition1 first condition
     * @param AbstractFieldCondition      $condition2 second condition
     *
     * @return AbstractFieldCondition|null
     */
    public static function buildAndCondition($condition1, $condition2)
    {
        if (empty($condition1)) {
            return $condition2;
        }
        if (!empty($condition2)) {
            $condition1->setAndCondition($condition2);
        }
        return $condition1;
    }
}
