<?php
/**
 * Jusbib Factory
 *
 * PHP version 7
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * This program is
 * free software; you can redistribute it and/or modify
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
 * @category Jusbib_VuFind2
 * @package  VuFind_Search
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Jusbib\VuFind\Search;

use Jusbib\VuFind\Search\Helper\ExtendedSolrFactoryHelper;
use VuFind\View\Helper\Root\SearchOptions;
use Laminas\ServiceManager\ServiceManager;

/**
 * Jusbib Factory
 *
 * @category Jusbib_VuFind2
 * @package  VuFind_Search
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Factory
{
    /**
     * Creates JusbibSolrFactoryHelper
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return Helper\ExtendedSolrFactoryHelper
     */
    public static function getJusbibSOLRFactoryHelper(ServiceManager $sm)
    {
        $config
            = $sm->get('VuFind\Config\PluginManager')
            ->get('config')->SwissbibSearchExtensions;
        $extendedTargets = explode(',', $config->extendedTargets);

        return new ExtendedSolrFactoryHelper($extendedTargets);
    }

    /**
     * Creates Jusbib Search Options
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return SearchOptions
     */
    public static function getJusbibSearchOptionsForHelperOptions(ServiceManager $sm)
    {
        return new SearchOptions(
            $sm->get('Jusbib\Search\Options\PluginManager')
        );
    }
}
