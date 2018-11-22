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
 * @package  VuFind_Search_Options
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\VuFind\Search\Options;

use Swissbib\VuFind\Search\Helper\ExtendedSolrFactoryHelper;
use VuFind\Search\Options\PluginFactory as VuFindOptionsPluginFactory;

/**
 *  VuFind enhancements to extend the VuFind Options type for the Solr target
 *
 * @category Swissbib_VuFind
 * @package  VuFind_Search_Options
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class PluginFactory extends VuFindOptionsPluginFactory
{
    /**
     * CanCreateServiceWithName
     *
     * @param ServiceLocatorInterface $serviceLocator ServiceLocator
     * @param String                  $name           Name
     * @param String                  $requestedName  RequestedName
     *
     * @return mixed
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator,
        $name, $requestedName
    ) {
        /**
         * ExtendedSolrFactoryHelper
         *
         * @var ExtendedSolrFactoryHelper $extendedTargetHelper
         */
        $extendedTargetHelper = $serviceLocator
            ->get('Swissbib\ExtendedSolrFactoryHelper');
        $this->defaultNamespace = $extendedTargetHelper
            ->getNamespace($name, $requestedName);

        return parent::canCreateServiceWithName(
            $serviceLocator, $name, $requestedName
        );
    }
}
