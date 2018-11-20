<?php
/**
 * TypeLabelMappingHelper
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  VuFind_Search_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\VuFind\Search\Helper;

use VuFind\Search\Base\Params;
use VuFindSearch\Query\Query;

/**
 * TypeLabelMappingHelper
 *
 * @category Swissbib_VuFind
 * @package  VuFind_Search_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class TypeLabelMappingHelper
{
    /**
     * GetLabel
     *
     * @param Params $params Params
     *
     * @return string
     */
    public function getLabel(Params $params)
    {
        if ($params->getQuery() instanceof Query) {
            $type = strtolower($params->getQuery()->getHandler());

            if ($type !== 'allfields') {
                return 'adv_search_' . $type;
            }
        }

        return 'All_Fields';
    }
}
