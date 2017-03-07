<?php
/**
 * Factory
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, 2015.
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Search_Solr
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
namespace Swissbib\VuFind\Search\Solr;

use Zend\ServiceManager\ServiceManager;

/**
 * Factory to create specialized types in the Search/Solr namespace
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Search_Solr
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class Factory
{
    /**
     * GetSpellchecker
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return SpellingProcessor
     */
    public static function getSpellchecker(ServiceManager $sm)
    {
        return new SpellingProcessor($sm->get("sbSpellingResults"));
    }

    /**
     * GetSpellingResults
     *
     * @param ServiceManager $sm ServiceManagers
     *
     * @return SpellingResults
     */
    public static function getSpellingResults(ServiceManager $sm)
    {
        return new SpellingResults();
    }
}
