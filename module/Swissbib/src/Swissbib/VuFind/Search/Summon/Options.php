<?php
/**
 * Summon Search Options
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
 * @package  VuFind_Search_Summon
 * @author   Oliver Schihin <oliver.schihin@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Vufind\Search\Summon;

use VuFind\Search\Summon\Options as VFSummonOptions;

/**
 * Summon Search Options
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Search_SolrClassification
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
class Options extends VFSummonOptions
{
    /**
     * Set default limit
     *
     * @param Integer $limit DefaultLimit
     *
     * @return void
     */
    public function setDefaultLimit($limit)
    {
        $maxLimit = max($this->getLimitOptions());
        if ($limit > $maxLimit) {
            $this->defaultLimit = $maxLimit;
        } else {
            $this->defaultLimit = intval($limit);
        }
    }

    /**
     * Set default sort
     *
     * @param String $defaultSort DefaultSort
     *
     * @return void
     */
    public function setDefaultSort($defaultSort)
    {
        $this->defaultSort = $defaultSort;
    }
}
