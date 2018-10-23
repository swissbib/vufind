<?php

/**
 * UtilController
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 23.10.18
 * Time: 09:16
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
 * @package  Controller
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */

namespace Swissbib\Controller;

use VuFindConsole\Controller\UtilController as VFUtilController;
use Swissbib\VuFind\Sitemap\Generator as SitemapGenerator;
use Zend\Console\Console;


/**
 * UtilController
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 * @link     http://www.swissbib.ch
 */
class UtilController extends VFUtilController
{

    /**
     * Generate a Sitemap based on distributed SolrCloud
     *
     * @throws \Exception
     *
     * @return \Zend\Console\Response
     */
    public function sitemapAction()
    {
        // Build sitemap and display appropriate warnings if needed:
        $configLoader = $this->serviceLocator->get('VuFind\Config\PluginManager');
        $generator = new SitemapGenerator(
            $this->serviceLocator->get('VuFind\Search\BackendManager'),
            $configLoader->get('config')->Site->url, $configLoader->get('sitemap')
        );
        $generator->generate();
        foreach ($generator->getWarnings() as $warning) {
            Console::writeLine("$warning");
        }
        return $this->getSuccessResponse();
    }

}