<?php
/**
 * HoldingItemsPaging
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
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Build holdings items paging
 *
 * @category Swissbib_VuFind2
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class HoldingItemsPaging extends AbstractHelper
{
    /**
     * PageSize
     *
     * @var Integer
     */
    protected $pageSize = 10;

    /**
     * Invoke HoldingItemsPaging
     *
     * @param String  $baseUrl    BaseUrl
     * @param Integer $total      Total
     * @param Integer $activePage ActivePage
     * @param Integer $year       Year
     * @param String  $volume     Volume
     *
     * @return mixed
     */
    public function __invoke($baseUrl, $total, $activePage = 1,
        $year = null, $volume = null
    ) {
        $maxPages    = 10;
        $maxReqPages = ceil($total / $this->pageSize);
        $activePage  = $activePage > $total ? 1 : $activePage;
        $spread      = $maxPages / 2;
        $startPage   = $activePage > $spread ? $activePage - $spread : 1;
        $endPage     = $startPage + $maxPages > $maxReqPages ?
            $maxReqPages : $startPage + $maxPages;

        $data = [
            'pages'     => $maxReqPages,
            'active'    => $activePage,
            'baseUrl'   => $baseUrl,
            'startPage' => $startPage,
            'endPage'   => $endPage,
            'year'      => $year,
            'volume'    => $volume
        ];

        return $this->getView()->render('Holdings/holding-items-paging', $data);
    }
}
