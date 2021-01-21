<?php
/**
 * SwissCollections: SucheinstiegePerson.php
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

use SwissCollections\Formatter\FieldFormatterData;
use SwissCollections\Formatter\FieldGroupFormatter;
use SwissCollections\RecordDriver\FieldGroupRenderContext;
use SwissCollections\RecordDriver\FieldRenderContext;
use SwissCollections\RenderConfig\AbstractRenderConfigEntry;
use SwissCollections\RenderConfig\CompoundEntry;

/**
 * A special formatter to render "Person" of "Sucheinstiege". The "Person"s are
 * collected from all marc field values and grouped by their roles.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\templates\RecordDriver\SolrMarc\FieldGroupFormatter
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class SucheinstiegePerson extends FieldGroupFormatter
{
    public static $GROUP_NAME = "Sucheinstiege";
    public static $RENDER_CONFIG_PERSON_KEY = "Person";

    /**
     * Die Personen nach ihren Funktionen gruppiert ausgeben.
     * 1xx/7xx$4: Urheber (aut, cmp, Rollen zu definieren, vgl. Primo, e-manuscripta)
     * 7xx$4: Beteiligte (inkl. Rollen)
     * 7xx$4rcp: AdressatIn
     * z.B. http://127.0.0.1/Record/990115273800205508
     *
     * @param AbstractRenderConfigEntry[] $fieldDataList the field's render configuration
     * @param FieldGroupRenderContext     $context       the render context
     *
     * @return void
     */
    public function render(&$fieldDataList, &$context): void
    {
        // bucket sort by role ("unknown" if no role is assigned)
        $personsWithRole = $this->fillRoleBuckets($fieldDataList, $context);
        $partialRoleOrder = $fieldDataList[0]->getFieldGroupFormatter()
            ->getRoleOrder();
        $roles = $this->sortRoles(
            array_keys($personsWithRole), $partialRoleOrder
        );
        $personLabelKey = SucheinstiegePerson::$GROUP_NAME . "."
            . SucheinstiegePerson::$RENDER_CONFIG_PERSON_KEY;
        $unknownRoleLabelKey = $personLabelKey . ".unknown-role";
        $fieldContext = new FieldRenderContext(
            $context->fieldFormatterRegistry, $context->solrMarc,
            $context->subfieldFormatterRegistry,
            $context->phpRenderer
        );

        echo $context->phpRenderer->render(
            '/RecordDriver/SolrMarc/FieldGroupFormatter/SucheinstiegePerson',
            [
                'fieldDataList' => &$fieldDataList,
                'formatter' => $this,
                'context' => $context,
                'fieldContext' => $fieldContext,
                'personLabelKey' => $personLabelKey,
                'unknownRoleLabelKey' => $unknownRoleLabelKey,
                'roles' => $roles,
                'personsWithRole' => $personsWithRole,
            ]
        );
    }

    /**
     * Sort the roles by the given configuration.
     *
     * @param string[] $roles        the list of roles to sort
     * @param string[] $partialOrder a partial list of roles defining the order
     *
     * @return string[]
     */
    protected function sortRoles($roles, $partialOrder)
    {
        $newRoles = $partialOrder;
        // add all missing roles too ...
        foreach ($roles as $r) {
            if (!in_array($r, $newRoles)) {
                $newRoles[] = $r;
            }
        }
        return $newRoles;
    }

    /**
     * Sort persons into buckets of roles. If no role is present "unknown" is used.
     *
     * @param AbstractRenderConfigEntry[] $renderConfigEntries the field's render configuration
     * @param FieldGroupRenderContext     $context             the render context
     *
     * @return array array with role keys and a list of tuples. Each tuple consists of an CompoundEntry and
     *          FieldFormatterData[] (= values)
     */
    protected function fillRoleBuckets(&$renderConfigEntries, &$context): array
    {
        $roleMarcSubfields = $renderConfigEntries[0]->getFieldGroupFormatter()
            ->getRoleMarcSubfields();
        $hiddenMarcSubfields = $renderConfigEntries[0]->getFieldGroupFormatter()
            ->getHiddenRoleMarcSubfields();

        $personsWithRole = [];
        /**
         * A single render configuration.
         *
         * @var CompoundEntry $renderElem
         */
        foreach ($renderConfigEntries as $renderElem) {
            $marcIndex = $renderElem->marcIndex;
            $subfieldValues = $context->solrMarc->getMarcFieldsRawMap(
                $marcIndex, $renderElem->subfieldCondition,
                $renderElem->getHiddenMarcSubfields()
            );
            if (!empty($subfieldValues)) {
                foreach ($subfieldValues as $sfv) {
                    $role = "unknown";
                    foreach ($roleMarcSubfields as $roleSubfieldName) {
                        if (!empty($sfv[$roleSubfieldName])) {
                            $role = $sfv[$roleSubfieldName][0];
                            break;
                        }
                    }
                    $compoundEntry = $renderElem->flatCloneEntry();
                    foreach ($sfv as $marcSubfieldNameStr => $value) {
                        if (in_array(
                            $marcSubfieldNameStr, $hiddenMarcSubfields
                        )
                        ) {
                            continue;
                        }
                        $compoundEntry->addElement(
                            SucheinstiegePerson::$RENDER_CONFIG_PERSON_KEY,
                            $marcSubfieldNameStr
                        );
                    }
                    $compoundEntry->orderEntries();
                    $ffdList = [];
                    foreach ($compoundEntry->elements as $singleEntry) {
                        $marcSubfieldNameStr
                            = $singleEntry->getMarcSubfieldName();
                        $ffdList[] = $compoundEntry->buildFieldFormatterData(
                            $marcSubfieldNameStr,
                            $sfv[$marcSubfieldNameStr][0], $context->solrMarc
                        );
                    }
                    if (!array_key_exists($role, $personsWithRole)) {
                        $personsWithRole[$role] = [[$compoundEntry, $ffdList]];
                    } else {
                        $personsWithRole[$role][] = [$compoundEntry, $ffdList];
                    }
                }
            }
        }
        return $personsWithRole;
    }
}