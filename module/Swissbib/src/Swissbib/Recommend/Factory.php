<?php
/**
 * Recommendation Module Factory Class
 *
 * PHP version 7
 *
 * Copyright (C) Villanova University 2014.
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
 * @category VuFind2
 * @package  Recommendations
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:hierarchy_components Wiki
 */
namespace Swissbib\Recommend;

use Zend\ServiceManager\ServiceManager;

/**
 * Recommendation Module Factory Class
 *
 * @category           VuFind2
 * @package            Recommendations
 * @author             Demian Katz <demian.katz@villanova.edu>
 * @license            http://opensource.org/licenses/gpl-2.0.php GNU General
 *                     Public License
 * @link               http://vufind.org/wiki/vufind2:hierarchy_components Wiki
 * @codeCoverageIgnore
 */
class Factory
{
    /**
     * Factory for SideFacets module.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return SideFacets
     */
    public static function getSideFacets(ServiceManager $sm)
    {
        return new SideFacets(
            $sm->get('VuFind\Config\PluginManager'),
            $sm->get('VuFind\HierarchicalFacetHelper')
        );
    }

    /**
     * Factory for TopIpRange module.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return TopIpRange
     */
    public static function getTopIpRange(ServiceManager $sm)
    {
        return new TopIpRange();
    }
}
