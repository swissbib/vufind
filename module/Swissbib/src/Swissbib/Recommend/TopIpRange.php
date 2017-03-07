<?php
/**
 * TopIpRange
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 04.09.15
 * Time: 16:15
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
 * @package  Recommend
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Recommend;
use VuFind\Recommend\RecommendInterface;

/**
 * TopIpRange
 *
 * @category Swissbib_VuFind2
 * @package  Recommend
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class TopIpRange implements RecommendInterface
{
    /**
     * SetConfig
     *
     * @param string $settings Settings
     *
     * @return void
     */
    public function setConfig($settings)
    {
    }

    /**
     * Init
     *
     * @param \VuFind\Search\Base\Params $params  params
     * @param \Zend\StdLib\Parameters    $request request
     *
     * @return void
     */
    public function init($params, $request)
    {
    }

    /**
     * Process
     *
     * @param \VuFind\Search\Base\Results $results results
     *
     * @return void
     */
    public function process($results)
    {
    }

}
