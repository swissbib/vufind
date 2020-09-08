<?php
/**
 * Factory for Libadmin Types.
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
 * @package  Libadmin
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Libadmin;

use Swissbib\Libadmin\Importer as LibadminImporter;
use Laminas\ServiceManager\ServiceManager;

/**
 * Factory for Types used for communication with the Libadmin web application.
 *
 * @category Swissbib_VuFind
 * @package  Libadmin
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Factory
{
    /**
     * Returns LibadminImporter
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return Importer
     */
    public static function getLibadminImporter(ServiceManager $sm)
    {
        $config = $sm->get('VuFind\Config\PluginManager')->get('config')->Libadmin;
        $languageCache = $sm->get('VuFind\CacheManager')->getCache('language');

        return new LibadminImporter($config, $languageCache);
    }
}
