<?php
/**
 * Swissbib DataSource
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

use Zend\Cache\Storage\StorageInterface;
use Zend\Config\Config;

use VuFind\Config\PluginManager as ConfigManager;

/**
 * Helper for favorite institutions
 *
 * @category Swissbib_VuFind2
 * @package  Favorites
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class DataSource
{
    /**
     * Storage
     *
     * @var StorageInterface
     */
    protected $cache;

    /**
     * ConfigManager
     *
     * @var ConfigManager
     */
    protected $configManager;

    const CACHE_KEY = 'favorite-institutions';

    /**
     * Initialize with cache and options data source
     *
     * @param StorageInterface $cache         Storage
     * @param ConfigManager    $configManager ConfigManager
     */
    public function __construct(
        StorageInterface $cache, ConfigManager $configManager
    ) {
        $this->cache        = $cache;
        $this->configManager = $configManager;
    }

    /**
     * Get favorite institutions
     *
     * @return Array
     */
    public function getFavoriteInstitutions()
    {
        if ($this->isCached()) {
            return $this->getCachedData();
        } else {
            $institutionAutocompleteData = $this->loadInstitutionFavoriteData();

            $this->setCachedData($institutionAutocompleteData);

            return $institutionAutocompleteData;
        }
    }

    /**
     * Check whether institutions are already cached
     *
     * @return Boolean
     */
    protected function isCached()
    {
        return $this->cache->hasItem(self::CACHE_KEY);
    }

    /**
     * Load data from cache
     *
     * @return Array
     */
    protected function getCachedData()
    {
        return $this->cache->getItem(self::CACHE_KEY);
    }

    /**
     * Write data to cache
     *
     * @param Array $institutionList institution list
     *
     * @return Boolean
     */
    protected function setCachedData(array $institutionList)
    {
        return $this->cache->setItem(self::CACHE_KEY, $institutionList);
    }

    /**
     * Load favorites data
     * Extract from a config object
     *
     * @return Array
     */
    protected function loadInstitutionFavoriteData()
    {
        /**
         * Favorite institution config
         *
         * @var Config $config
         */
        $config = $this->configManager->get('favorite-institutions');

        return $config->toArray();
    }
}
