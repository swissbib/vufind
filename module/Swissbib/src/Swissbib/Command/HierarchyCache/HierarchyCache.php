<?php
/**
 * A console command or factory
 *
 * PHP Version 7
 *
 * Copyright (C) swissbib 2020
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  Swissbib\Command
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Command\HierarchyCache;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VuFind\Record\Loader;
use VuFind\Search\Solr\Results as SolrResults;

/**
 * Class HierarchyCache
 *
 * Generate Cache for hierarchy trees
 *
 * @category Swissbib_VuFind
 * @package  Swissbib\Command\HierarchyCache
 * @author   Lionel Walter  <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class HierarchyCache extends Command
{
    /**
     * The name of the command (the part after "public/index.php")
     *
     * @var string
     */
    protected static $defaultName = 'hierarchy-build-cache';

    /**
     * SolrResults
     *
     * @var SolrResults solrResults
     */
    protected $solrResults;

    /**
     * RecordLoader
     *
     * @var Loader recordLoader
     */
    protected $recordLoader;

    /**
     * HierarchyCache constructor.
     *
     * @param SolrResults $solrResults  Solr results
     * @param Loader      $recordLoader Record loader
     */
    public function __construct(SolrResults $solrResults, Loader $recordLoader)
    {
        $this->solrResults=$solrResults;
        $this->recordLoader=$recordLoader;
        parent::__construct();
    }

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->addArgument(
            'limit',
            InputArgument::OPTIONAL,
            'maximum number of child element to consider'
        );
    }

    /**
     * Build cache for hierarchies
     *
     * @param InputInterface  $input  Input object
     * @param OutputInterface $output Output object
     *
     * @return int 0 for success
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $counter = 1;

        $verbose = $input->getOption('verbose');
        $limit = $input->getArgument('limit');

        echo "Start building hierarchy tree cache\n";

        if ($limit) {
            echo "Limit for child records is set to $limit\n";
        }

        echo "\n";

        $hierarchies = $this->solrResults->getFullFieldFacets(['hierarchy_top_id']);

        foreach ($hierarchies['hierarchy_top_id']['data']['list'] as $hierarchy) {
            if ($verbose) {
                echo "Building tree for {$hierarchy['value']} (" .
                    ($counter++) . ")\n";
            }

            $driver = $this->recordLoader->load($hierarchy['value']);
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

        return 0;
    }
}
