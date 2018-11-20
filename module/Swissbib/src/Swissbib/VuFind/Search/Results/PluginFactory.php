<?php
/**
 * PluginFactory
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
 * @package  VuFind_Search_Results
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\VuFind\Search\Results;

use Interop\Container\ContainerInterface;
use Swissbib\VuFind\Search\Helper\ExtendedSolrFactoryHelper;
use VuFind\Search\Results\PluginFactory as VuFindResultsPluginFactory;

/**
 * Class PluginFactory
 *
 * @category Swissbib_VuFind
 * @package  VuFind_Search_Results
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class PluginFactory extends VuFindResultsPluginFactory
{
    /**
     * CanCreateServiceWithName
     *
     * @param ContainerInterface $container     ServiceContainer
     * @param String             $requestedName Name of service
     * @param array              $options       Options (unused)
     *
     * @return object
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function canInvoke(ContainerInterface $container,
        $requestedName, array $options = null
    ) {
        /**
         * ExtendedSolrFactoryHelper
         *
         * @var ExtendedSolrFactoryHelper $extendedTargetHelper
         */
        $extendedTargetHelper
            = $container->get('Swissbib\ExtendedSolrFactoryHelper');

        $this->defaultNamespace = $extendedTargetHelper
            ->getNamespace($requestedName);

        return parent($container, $requestedName, $options);
    }

    /**
     * Create a service for the specified name.
     *
     * @param ContainerInterface $container     Container service
     * @param string             $requestedName Unfiltered name of service
     * @param array              $extraParams   Extra Params
     *
     * @return object
     */
    public function __invoke(ContainerInterface $container,
        $requestedName, array $extraParams = null
    ) {
        /**
         * ExtendedSolrFactoryHelper
         *
         * @var ExtendedSolrFactoryHelper $extendedTargetHelper
         */
        $extendedTargetHelper
            = $container->get('Swissbib\ExtendedSolrFactoryHelper');

        $this->defaultNamespace
            = $extendedTargetHelper->getNamespace($requestedName);

        /**
         * Swissbib specific Results type for Solr
         *
         * @var \Swissbib\VuFind\Search\Solr\Results $sbSolrResults
         */
        $sbSolrResults
            = parent::__invoke($container, $requestedName, $extraParams);
        $facetConfigs = $container->get('VuFind\Config\PluginManager')
            ->get($sbSolrResults->getOptions()->getFacetsIni());

        //todo
        //perhaps not a really nice way to provide the config dependency
        //via Setter methods analyze the complete complex Facets
        //in VuFind 4 perhaps a easier way to implement our
        //query facets (MyLibraries) is possible
        $sbSolrResults->setFacetsConfig($facetConfigs);
        return $sbSolrResults;
    }
}
