<?php
/**
 * Factory for instantiating content loaders
 *
 * PHP version 7
 *
 * Copyright (C) Villanova University 2009.
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
 * @package  Content
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\Content\Covers;

use Laminas\ServiceManager\ServiceManager;
use VuFind\Content\Covers\PluginManager as VFPluginManager;

/**
 * Factory for instantiating content loaders
 *
 * @category VuFind2
 * @package  Content
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 *
 * @codeCoverageIgnore
 */
class PluginManager extends VFPluginManager
{
    /**
     * Create Amazon loader
     *
     * @param ServiceManager $sm Service manager
     *
     * @return mixed
     */
    public static function getAmazon(ServiceManager $sm)
    {
        $config = $sm->get('VuFind\Config\PluginManager')->get('config');
        $associate = isset($config->Content->amazonassociate)
            ? $config->Content->amazonassociate : null;
        $secret = isset($config->Content->amazonsecret)
            ? $config->Content->amazonsecret : null;
        return new Amazon($associate, $secret);
    }
}
