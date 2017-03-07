<?php
/**
 * Swissbib HierarchyCacheController
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
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;

use VuFind\Search\Solr\Results as SolrResults;

use Swissbib\VuFind\Hierarchy\TreeDataSource\Solr as TreeDataSourceSolr;

/**
 * Swissbib HierarchyCacheController
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class HierarchyCacheController extends AbstractActionController
{
    /**
     * Build cache files
     *
     * @return String
     */
    public function buildCacheAction()
    {
        $counter = 1;
        /**
         * Console request
         *
         * @var ConsoleRequest $request
         */
        $request = $this->getRequest();
        $verbose = $request->getParam('verbose', false) ||
            $request->getParam('v', false);
        $limit   = $request->getParam('limit');

        echo "Start building hierarchy tree cache in local/cache/hierarchy\n";

        if ($limit) {
            echo "Limit for child records is set to $limit\n";
        }

        echo "\n";

        $recordLoader = $this->getServiceLocator()->get('VuFind\RecordLoader');
        /**
         * SolrResults
         *
         * @var SolrResults $solrResults
         */
        $solrResults = $this->getServiceLocator()
            ->get('VuFind\SearchResultsPluginManager')->get('Solr');
        $hierarchies = $solrResults->getFullFieldFacets(['hierarchy_top_id']);

        foreach ($hierarchies['hierarchy_top_id']['data']['list'] as $hierarchy) {
            if ($verbose) {
                echo "Building tree for {$hierarchy['value']} (" .
                    ($counter++) . ")\n";
            }

            $driver = $recordLoader->load($hierarchy['value']);
                // Only do this if the record is actually a hierarchy type record
            if ($driver->getHierarchyType()) {
                /**
                 * TreeDataSourceSolr
                 *
                 * @var TreeDataSourceSolr $treeDataSource
                 */
                $treeDataSource = $driver->getHierarchyDriver()->getTreeSource();

                if ($limit) {
                    $treeDataSource->setTreeChildLimit(1000);
                }

                $treeDataSource->getXML(
                    $hierarchy['value'],
                    ['refresh' => true]
                );
            }
        }

        return "Building of hierarchy cache finished. Created " .
            ($counter - 1) . " cache files\n";
    }
}
