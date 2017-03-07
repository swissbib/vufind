<?php
/**
 * Factory
 *
 * PHP version 5
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Hierarchy_TreeRenderer
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\VuFind\Hierarchy\TreeRenderer;

use VuFind\Hierarchy\TreeRenderer\JSTree as VfJsTree;
use VuFindSearch\Query\Query;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Temporary override to fix problem with invalid solr data
 * (count of top ids does not match top titles)
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Hierarchy_TreeRenderer
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class JSTree extends VfJsTree implements ServiceLocatorAwareInterface
{
    /**
     * ServiceLocator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Search Serivice
     *
     * @var VuFindSearch\Service
     */
    protected $searchService;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator ServiceLocator
     *
     * @return void
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->searchService = $serviceLocator->getServiceLocator()
            ->get('VuFind\Search');
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Prevent error from missing hierarchy title data
     *
     * @param bool|false $hierarchyID HierarchyId
     *
     * @return array|bool
     */
    public function getTreeList($hierarchyID = false)
    {
        $record             = $this->getRecordDriver();
        $id                 = $record->getUniqueID();
        $inHierarchies      = $record->getHierarchyTopID();
        $inHierarchiesTitle = $record->getHierarchyTopTitle();

        if ($hierarchyID) {
            // Specific Hierarchy Supplied
            if (in_array($hierarchyID, $inHierarchies)
                && $this->getDataSource()->supports($hierarchyID)
            ) {
                return [
                    $hierarchyID => $this->getHierarchyName(
                        $hierarchyID, $inHierarchies, $inHierarchiesTitle
                    )
                ];
            }
        } else {
            // Return All Hierarchies
            $i           = 0;
            $hierarchies = [];
            foreach ($inHierarchies as $hierarchyTopID) {
                if ($this->getDataSource()->supports($hierarchyTopID)) {
                    $hierarchies[$hierarchyTopID] = isset($inHierarchiesTitle[$i]) ?
                        $inHierarchiesTitle[$i] : '';
                }
                $i++;
            }
            if (!empty($hierarchies)) {
                return $hierarchies;
            }
        }

            // Return dummy tree list (for top most records)
        if ($id && $this->hasChildren($id)) {
            return [
                $id => 'Unknown hierarchie title'
            ];
        }

        // If we got this far, we couldn't find valid match(es).
        return false;
    }

    /**
     * Check whether item has children in hierarchy
     *
     * @param String $id Id
     *
     * @return Boolean
     */
    protected function hasChildren($id)
    {
        $query = new Query(
            'hierarchy_parent_id:"' . addcslashes($id, '"') . '"'
        );
        $results    = $this->searchService->search('Solr', $query, 0, 1);

        return $results->getTotal() > 0;
    }

    /**
     * Prevent error on empty xml file
     * Transforms Collection XML to Desired Format
     *
     * @param string $context     The Context in which the tree is being displayed
     * @param string $mode        The Mode in which the tree is being displayed
     * @param string $hierarchyID The hierarchy to get the tree for
     * @param string $recordID    The currently selected Record (false for none)
     *
     * @return string A HTML List
     */
    protected function transformCollectionXML($context, $mode,
        $hierarchyID, $recordID
    ) {
        $jsonFile = $this->getDataSource()->getJSON($hierarchyID);

        if (empty($jsonFile)) {
            return 'Missing data for tree rendering';
        }

        return parent::transformCollectionXML(
            $context, $mode, $hierarchyID, $recordID
        );
    }

    /**
     * Prevent error from missing title
     *
     * @param string $hierarchyID        The hierarchy ID to find the title for
     * @param string $inHierarchies      An array of hierarchy IDs
     * @param string $inHierarchiesTitle An array of hierarchy Titles
     *
     * @return string A hierarchy title
     */
    public function getHierarchyName($hierarchyID, $inHierarchies,
        $inHierarchiesTitle
    ) {
        if (in_array($hierarchyID, $inHierarchies)) {
            $keys = array_flip($inHierarchies);
            $key = $keys[$hierarchyID];

            if (isset($inHierarchiesTitle[$key])) {
                return $inHierarchiesTitle[$key];
            }
        }

        return 'No title found';
    }

    /**
     * Recursive function to convert the json to the right format
     *
     * @param object  $node        JSON object of a node/top node
     * @param string  $context     Record or Collection
     * @param string  $hierarchyID Collection ID
     * @param integer $idPath      Indicating a path of IDs to the
     *                             root element in order to make the
     *                             HTML ID unique
     *
     * @return array
     */
    protected function buildNodeArray($node, $context, $hierarchyID, $idPath = 0)
    {
        $escaper = new \Zend\Escaper\Escaper('utf-8');
        $htmlID = $idPath . '_' . preg_replace('/\W/', '-', $node->id);
        $ret = [
            'id' => $htmlID,
            'text' => $escaper->escapeHtml($node->title),
            'li_attr' => [
                'recordid' => $node->id
            ],
            'a_attr' => [
                'href' => $this->getContextualUrl(
                    $node, $context, $hierarchyID, $htmlID
                ),
                'title' => $node->title
            ],
            'type' => $node->type
        ];
        if (isset($node->children)) {
            $ret['children'] = [];
            for ($i = 0;$i < count($node->children);$i++) {
                $ret['children'][$i] = $this->buildNodeArray(
                    $node->children[$i], $context, $hierarchyID, $htmlID
                );
            }
        }
        return $ret;
    }

    /**
     * Use the router to build the appropriate URL based on context
     *
     * @param object $node         JSON object of a node/top node
     * @param string $context      Record or Collection
     * @param string $collectionID Collection ID
     * @param string $htmlID       ID used on html tag, must be unique
     *
     * @return string
     */
    protected function getContextualUrl($node, $context, $collectionID = '',
        $htmlID = ''
    ) {
        $params = [
            'id' => $node->id,
            'tab' => 'HierarchyTree'
        ];
        $options = [
            'query' => [
                'recordID' => $node->id,
                'htmlID' => $htmlID
            ]
        ];
        if ($context == 'Collection') {
            return $this->router->fromRoute('collection', $params, $options)
            . '#tabnav';
        } else {
            $options['query']['hierarchy'] = $collectionID;
            $url = $this->router->fromRoute($node->type, $params, $options);
            return $node->type == 'collection'
                ? $url . '#tabnav'
                : $url . '#tree-' . preg_replace('/\W/', '-', $node->id);
        }
    }
}
