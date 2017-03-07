<?php
/**
 * PluginManager
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
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
 * @package  VuFind_Search_Results
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\VuFind\Search\Results;

use VuFind\Search\Results\PluginManager as VuFindSearchResultsPluginManager;

/**
 * Swissbib (service) Manager responsible for a factory to create an extended
 * Solr Results type. Customized ResultsPluginManger has to extend
 * Vufind\Search\Results\PluginManger and not directly
 * Vufind\ServiceManager\AbstractPluginManger because type ResultsPluginManger
 * is expected in (some ?) other methods e.g.
 * Vufind\Db\Table\Search->saveSearch()
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Search_Results
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class PluginManager extends VuFindSearchResultsPluginManager
{
}
