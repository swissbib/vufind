<?php
/**
 * Factory for types used to implement favorites logic.
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
 * @package  Favorites
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Favorites;

use Zend\ServiceManager\ServiceManager;
use Swissbib\Favorites\DataSource as FavoritesDataSource;
use Swissbib\Favorites\Manager as FavoritesManager;

/**
 * Factory for Favorites types.
 *
 * @category Swissbib_VuFind2
 * @package  Favorites
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Factory
{
    /**
     * Creates a DataSource which contains elements used as favorites
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return DataSource
     */
    public static function getFavoritesDataSource(ServiceManager $sm)
    {
        $objectCache = $sm->get('VuFind\CacheManager')->getCache('object');
        $configManager = $sm->get('VuFind\Config');

        return new FavoritesDataSource($objectCache, $configManager);
    }

    /**
     * FavoritesManager
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return Manager
     */
    public static function getFavoritesManager(ServiceManager $sm)
    {
        $sessionStorage = $sm->get('VuFind\SessionManager')->getStorage();
        $groupMapping = $sm->get('VuFind\Config')->get('libadmin-groups')
            ->institutions;
        $authManager = $sm->get('VuFind\AuthManager');

        return new FavoritesManager($sessionStorage, $groupMapping, $authManager);
    }
}
