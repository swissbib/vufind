<?php
/**
 * SwissCollections: Factory.php
 *
 * PHP version 7
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swisscollections.org  / http://www.swisscollections.ch / http://www.ub.unibas.ch
 *
 * Date: 1/12/20
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
 * @category SwissCollections_VuFind
 * @package  SwissCollections\Controller
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\Controller;

use Laminas\ServiceManager\ServiceManager;
use VuFind\Controller\AbstractBaseFactory;

/**
 * Class Factory.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\Controller
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Factory extends AbstractBaseFactory
{
    /**
     * Factory method to create a new {@link KeywordSearchController}
     *
     * @param ServiceManager $sm the service manager
     *
     * @return KeywordSearchController
     */
    public static function getKeywordSearchController(ServiceManager $sm)
    {
        return new KeywordSearchController($sm);
    }

    /**
     * Factory method to create a new {@link AbcSearchController}
     *
     * @param ServiceManager $sm the service manager
     *
     * @return AbcSearchController
     */
    public static function getAbcSearchController(ServiceManager $sm)
    {
        return new AbcSearchController($sm);
    }

    /**
     * Factory method to create a new {@link getTektonikController}
     *
     * @param ServiceManager $sm the service manager
     *
     * @return TektonikController
     */
    public static function getTektonikController(ServiceManager $sm)
    {
        return new TektonikController($sm);
    }

    /**
     * Factory method to create a new {@link BibliographiesController}
     *
     * @param ServiceManager $sm the service manager
     *
     * @return BibliographiesController
     */
    public static function getBibliographiesController(ServiceManager $sm)
    {
        return new BibliographiesController($sm);
    }

    /**
     * Factory method to create a new {@link BrowseController}
     *
     * @param ServiceManager $sm the service manager
     *
     * @return BrowseController
     */
    public static function getBrowseController(ServiceManager $sm)
    {
        return new BrowseController($sm);
    }
}