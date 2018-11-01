<?php
/**
 * Generator
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 23.10.18
 * Time: 11:38
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
 * @package  Swissbib\VuFind\Sitemap
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */

namespace Swissbib\VuFind\Sitemap;
use VuFind\Sitemap\Generator as VFGenerator;
use VuFindSearch\Backend\Solr\Backend;
use VuFindSearch\ParamBag;
use VuFindSearch\Backend\Solr\Response\Json\NamedList;



/**
 * Generator
 *
 * @category Swissbib_VuFind2
 * @package  ${PACKAGE}
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 * @link     http://www.swissbib.ch
 */
class Generator extends VFGenerator
{

    /**
     * Retrieve a batch of IDs using the terms component.
     * We need an additional attribute distrib because of our distributed Solr mode
     *
     * @param Backend $backend  Search backend
     * @param string  $lastTerm Last term retrieved
     *
     * @return array
     */
    protected function getIdsFromBackendUsingTerms(Backend $backend, $lastTerm)
    {
        $key = $backend->getConnector()->getUniqueKey();
        //todo
        //Do we want to have a more configurable solution - local sitemap.ini?
        //Pull request for VuFind?
        $paramBag = new ParamBag();
        $paramBag->set('distrib', 'false');

        /**
         * List of all the requested IDs
         *
         *@var $info  NamedList
         */
        $info = $backend->terms($key, $lastTerm, $this->countPerPage, $paramBag)
            ->getFieldTerms($key);
        return null === $info ? [] : array_keys($info->toArray());
    }


}