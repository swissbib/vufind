<?php
/**
 * Factory.php
 *
 * PHP Version 7
 *
 * Copyright (C) swissbib 2018
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
 * @package  SwissbibRdfDataApi\VuFind\Search\Options
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace SwissbibRdfDataApi\VuFind\Search\Options;

use Zend\ServiceManager\ServiceManager;

/**
 * Class Factory
 *
 * @category VuFind
 * @package  SwissbibRdfDataApi\VuFind\Search\Options
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class Factory
{
    /**
     * Factory for ElasticSearch results object.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return SwissbibRdfDataApi\VuFind\Search\SwissbibRdfDataApi\Options
     */
    public static function getRdfDataApiSearch(ServiceManager $sm)
    {
        $factory = new PluginFactory();
        $es = $factory($sm, 'SwissbibRdfDataApi');
        $config = $sm->get('VuFind\Config')->get('config');
        return $es;
    }
}
