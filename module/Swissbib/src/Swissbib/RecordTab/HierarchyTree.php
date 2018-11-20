<?php
/**
 * HierarchyTree
 *
 * PHP version 7
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 27/08/15
 * Time: 14:24
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
 * @category Swissbib_VuFind
 * @package  RecordTab
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\RecordTab;

use VuFind\RecordTab\HierarchyTree as VuFindHierarchyTree;

/**
 * HierarchyTree
 *
 * @category Swissbib_VuFind
 * @package  RecordTab
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class HierarchyTree extends VuFindHierarchyTree
{
    /**
     * IsActive
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function isActive()
    {
        $trees = $this->getTreeList();

        return !empty($trees) && in_array(
            $this->getRecordDriver()->getHierarchyType(),
            ['Default', 'series']
        );
    }

    /**
     * RenderTree
     *
     * @param string $baseUrl VuFind base URL
     * @param string $id      id if the record
     * @param string $context context (record or collenction)
     *
     * @return string
     */
    public function renderTree($baseUrl, $id = null, $context = 'Record')
    {
        $id = (null === $id) ? $this->getActiveTree() : $id;
        $recordDriver = $this->getRecordDriver();
        $hierarchyDriver = $recordDriver->tryMethod('getHierarchyDriverArchival');
        if (is_object($hierarchyDriver)) {
            $tree = $hierarchyDriver->render($recordDriver, $context, 'List', $id);
            return str_replace(
                '%%%%VUFIND-BASE-URL%%%%', rtrim($baseUrl, '/'), $tree
            );
        }
        return '';
    }
}
