<?php
/**
 * Hierarchy Driver Factory Class *
 *
 * PHP version 7
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
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
 * @package  VuFind_Hierarchy_TreeDataSource
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\VuFind\Hierarchy;

use Laminas\ServiceManager\ServiceManager;
use Swissbib\VuFind\Hierarchy\TreeRenderer\JSTree as SwissbibJsTree;

/**
 * Hierarchy Data Source Factory Class
 * This is a factory class to build objects for managing hierarchies.
 *
 * @category Swissbib_VuFind
 * @package  VuFind_Hierarchy_TreeDataSource
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Factory
{
    /**
     * GetJsTree
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return \Swissbib\VuFind\Hierarchy\TreeRenderer\JSTree
     */
    public static function getJSTree(ServiceManager $sm)
    {
        $searchService = $sm->get('VuFindSearch\Service');
        $config = $sm->get('VuFind\Config\PluginManager')->get('config');
        $swissbibJSTree = new SwissbibJsTree(
            $sm->get('ControllerPluginManager')->get('Url'),
            $searchService,
            !empty($config->Collections->collections)
        );
        return $swissbibJSTree;
    }
}
