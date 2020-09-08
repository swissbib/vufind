<?php
/**
 * Swissbib MultiTreeGenerator
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  Hierarchy
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Hierarchy;

use Laminas\Config\Config;

/**
 * Swissbib MultiTreeGenerator
 *
 * @category Swissbib_VuFind
 * @package  Hierarchy
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class MultiTreeGenerator
{
    /**
     * Facet keys that are used for trees
     *
     * @var array
     */
    protected $treeConfig = [];

    /**
     * SimpleTreeGenerator
     *
     * @var SimpleTreeGenerator
     */
    protected $simpleTreeGenerator;

    /**
     * Constructor
     *
     * @param Config              $config              Tree configuration
     * @param SimpleTreeGenerator $simpleTreeGenerator SimpleTreeGenerator
     */
    public function __construct(
        Config $config, SimpleTreeGenerator $simpleTreeGenerator
    ) {
        $this->setTreeConfig($config);
        $this->simpleTreeGenerator = $simpleTreeGenerator;
    }

    /**
     * Returns multi generated trees
     *
     * @param array $facetList List of facets
     *
     * @return array
     */
    public function getTrees(array $facetList)
    {
        $treesToGenerate = array_intersect(
            array_keys($facetList), $this->treeConfig
        );
        $generatedTrees = [];

        foreach ($treesToGenerate as $tree) {
            $generatedTrees[$tree] = $this->simpleTreeGenerator->getTree(
                $facetList[$tree]['list'], $tree
            );
        }

        return $generatedTrees;
    }

    /**
     * Set Tree Config
     *
     * @param Config $config Config
     *
     * @return void
     */
    protected function setTreeConfig(Config $config)
    {
        if ($config->Site->classificationTrees instanceof Config) {
            $this->treeConfig = $config->Site->classificationTrees->toArray();
        } else {
            $this->treeConfig = [];
        }
    }
}
