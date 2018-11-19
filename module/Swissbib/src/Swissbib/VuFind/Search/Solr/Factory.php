<?php
/**
 * Factory
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  VuFind_Search_Solr
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
namespace Swissbib\VuFind\Search\Solr;

use Zend\Config\Config as ZendConfig;
use Zend\ServiceManager\ServiceManager;

/**
 * Factory to create specialized types in the Search/Solr namespace
 *
 * @category Swissbib_VuFind
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
        $config = $sm
            ->get('VuFind\Config\PluginManager')->get('config');
        /**
         * SpellConfig
         *
         *  @var $spellConfig ZendConfig
         * todo: no unit test so far - what happens if we provide an empty configu
         * should be better as null
         */
        $spellConfig = isset($config->Spelling)
            ? $config->Spelling : new ZendConfig([]);

        /**
         * Spelling Results
         *
         *  @var $spellingResults SpellingResults
         */
        $spellingResults = $sm->get("sbSpellingResults");

        return new SpellingProcessor($spellingResults, $spellConfig);
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
