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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
 *
 * @category VuFind
 * @package  Swissbib\Controller\Plugin
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Controller\Plugin;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Factory
 *
 * @category VuFind
 * @package  Swissbib\Controller\Plugin
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class Factory
{
    /**
     * Constructs the TagCloud plugin.
     *
     * @param ServiceLocatorInterface $sm The service locator
     *
     * @return \Swissbib\Controller\Plugin\TagCloud
     */
    public static function getTagCloud(ServiceLocatorInterface $sm)
    {
        return new TagCloud(
            $sm->get('VuFind\Config')->get(
                'config'
            )->TagCloud
        );
    }

    /**
     * Constructs the SolrSearch plugin
     *
     * @param ServiceLocatorInterface $sm The service locator
     *
     * @return \Swissbib\Controller\Plugin\SolrSearch
     */
    public static function getSolrSearch(ServiceLocatorInterface $sm)
    {
        return new SolrSearch($sm);
    }

    /**
     * Constructs the ElasticSearchSearch plugin
     *
     * @param ServiceLocatorInterface $sm The service locator
     *
     * @return \Swissbib\Controller\Plugin\ElasticSearchSearch
     */
    public static function getElasticSearchSearch(ServiceLocatorInterface $sm)
    {
        return new ElasticSearchSearch($sm);
    }
}
