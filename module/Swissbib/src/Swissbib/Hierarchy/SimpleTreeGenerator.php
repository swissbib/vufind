<?php
/**
 * Swissbib SimpleTreeGenerator
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 1/2/13
 * Time: 4:09 PM
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
 * @package  Hierarchy
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Hierarchy;

use Zend\Cache\Storage\Adapter\Filesystem as ObjectCache;

/**
 * Swissbib SimpleTreeGenerator
 *
 * @category Swissbib_VuFind2
 * @package  Hierarchy
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class SimpleTreeGenerator
{
    /**
     * Zend object cache
     *
     * @var ObjectCache
     */
    protected $objectCache;

    /**
     * Constructor
     *
     * @param ObjectCache $objectCache ObjectCache
     */
    public function __construct(ObjectCache $objectCache)
    {
        $this->objectCache = $objectCache;
    }

    /**
     * Generating tree
     *
     * @param array   $datas        Tree data as list
     * @param string  $currentNode  Current node being prcoessed
     * @param integer $nestingLevel Indicationg nesting level
     *
     * @return string
     */
    protected function generatePageTree(array &$datas, $currentNode = "",
        $nestingLevel = 0
    ) {
        $tree = [];

        $currentNodeHead = explode(".", $currentNode);
        $currentNodeHead = $currentNodeHead[0];

        foreach ($datas as $key => $data) {
            $datasParent = explode(".", $data['value']);
            $head = $datasParent[0];

            if (!empty($currentNodeHead) && $head > $currentNodeHead) {
                break;
            }

            array_pop($datasParent);
            $parent = implode(".", $datasParent);

            if ($parent === $currentNode) {
                $data['nestingLevel'] = $nestingLevel;
                unset($datas[$key]);
                $tree[] = [
                    "entry" => $data,
                    "children" => $this->generatePageTree(
                        $datas, $data['value'], $nestingLevel + 1
                    )
                ];
            }
        }

        return $tree;
    }

    /**
     * Orders Facets and removes wrong instances. For instance D 14.5, D 14.5 e
     * and D 14.5 CH get trunked to D 14.5
     *
     * @param array $arrayList list of items
     *
     * @return array
     */
    protected function orderAndFilter(array $arrayList = [])
    {
        $sorted = [];

        foreach ($arrayList as $classification) {
            preg_match_all(
                "/[0-9]/", $classification['value'], $out, PREG_OFFSET_CAPTURE
            );
            $lastMatch = end($out[0]);
            $key = substr($classification['value'], 0, $lastMatch[1] + 1);

            if (!isset($sorted[$key])) {
                $sorted[$key] = $classification;
                $sorted[$key]['queryValue'] = $key;
            } else {
                $sorted[$key]['count'] += $classification['count'];
            }
            $sorted[$key]['value'] = $key;
        }

        uksort($sorted, 'strnatcmp');

        return $sorted;
    }

    /**
     * Returns tree
     *
     * @param array  $facets  Facets
     * @param string $treeKey Facet tree key for cache
     *
     * @return array
     */
    public function getTree(array $facets = [], $treeKey = '')
    {
        $cacheTreeId    = 'simpleTree-' . $treeKey;
        $cachedTree     = $this->objectCache->getItem($cacheTreeId);

        if (is_array($cachedTree)) {
            return $cachedTree;
        }
        if ($treeKey === '') {
            return $this->generatePageTree($this->orderAndFilter($facets));
        }

        $tree = $this->generatePageTree($this->orderAndFilter($facets));
        $this->objectCache->setItem($cacheTreeId, $tree);

        return $tree;
    }
}
