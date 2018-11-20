<?php
/**
 * Search Results Object Factory Class
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
 * @category Swissbib_VuFind
 * @package  Search
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:hierarchy_components Wiki
 */
namespace Swissbib\VuFind\Search\Results;

use Swissbib\VuFind\Search\Favorites\Results;
use Zend\ServiceManager\ServiceManager;

/**
 * Search Results Object Factory Class
 *
 * @category Swissbib_VuFind
 * @package  Search
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:hierarchy_components Wiki
 */
class Factory
{
    /**
     * Factory for Solr results object.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return \Swissbib\VuFind\Search\Solr\Results
     */
    public static function getSolr(ServiceManager $sm)
    {
        $factory = new PluginFactory();

        /**
         * Create Service With Name Solr
         *
         * @var $solr \Swissbib\VuFind\Search\Solr\Results
         */
        $solr = $factory($sm, 'Solr');

        $solr->setSpellingProcessor(
            $sm->get("sbSpellingProcessor")
        );
        return $solr;
    }

    /**
     * Factory for Solr results object.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return \Swissbib\VuFind\Search\SolrClassification\Results
     */
    public static function getSolrClassification(ServiceManager $sm)
    {
        $factory = new PluginFactory();

        /**
         * Create Service With Name SolrClassification
         *
         * @var $solr \Swissbib\VuFind\Search\SolrClassification\Results
         */
        $solr = $factory($sm, 'SolrClassification');

        return $solr;
    }

    /**
     * Returns MixedList
     *
     * @param ServiceManager $sm servicemanager
     *
     * @return object
     */
    public static function getMixdList(ServiceManager $sm)
    {
        $factory = new PluginFactory();

        /**
         * Create Service With Name Solr
         *
         * @var $solr \Swissbib\VuFind\Search\Solr\Results
         */
        $mixedlist = $factory($sm, 'mixedlist');

        return $mixedlist;
    }

    /**
     * Factory for Solr Authors.
     * Achtung: hier müssen wir dringend ein Refactoring machen
     * swissbib hat sich vor einiger Zeit mal dazu entschieden, Autorensuchen wie
     * normale Suchen zu behandlen VuFind hat für die Autoren einen eiegenen Index
     * und damit auch ein eigenes System, wie Recommendations auf Basis der
     * unterschiedlichen Suchen aufgebaut werden die unterschiedlichen
     * Recommendation wiederum ergeben dann Probleme z.B. hier
     * themes/sbvfrd/templates/search/results.phtml Zeile 108
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return \Swissbib\VuFind\Search\Solr\Results
     */
    public static function getSolrAuthorFacets(ServiceManager $sm)
    {
        //weil Autoren wie Facets behandelt werden erfolgt der Aufruf von getSolr
        return static::getSolr($sm);
    }

    /**
     * Factory for Favorites results object.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return Results
     */
    public static function getFavorites(ServiceManager $sm)
    {
        $factory = new PluginFactory();
        $tm = $sm->get('VuFind\DbTablePluginManager');
        $obj = $factory(
            $sm, 'favorites',
            [$tm->get('Resource'), $tm->get('UserList')]
        );

        $init = new \ZfcRbac\Initializer\AuthorizationServiceInitializer();
        $init->initialize($obj, $sm);
        return $obj;
    }

    /**
     * Returns \Swissbib\VuFind\Search\Summon\Results
     *
     * @param ServiceManager $sm servicemanager
     *
     * @return object
     */
    public static function getSummon(ServiceManager $sm)
    {
        $factory = new PluginFactory();

        /**
         * Create Service With Name Summon
         *
         * @var $summon \Swissbib\VuFind\Search\Summon\Results
         */
        $summon = $factory($sm, 'summon');

        return $summon;
    }
}
