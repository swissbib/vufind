<?php
/**
 * Authors
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
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\View\Helper;

use Laminas\I18n\View\Helper\AbstractTranslatorHelper;

/**
 * Search favorite institutions in holding list and add as a new group as first group
 *
 * @category Swissbib_VuFind
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class ExtractFavoriteInstitutionsForHoldings extends AbstractTranslatorHelper
{
    /**
     * UserInstitiutionCodes
     *
     * @var Array
     */
    protected $userInstitutionCodes;

    /**
     * Constructor
     *
     * @param String[] $userInstitutionCodes UserInstitutionCodes
     */
    public function __construct(array $userInstitutionCodes)
    {
        $this->userInstitutionCodes = array_flip($userInstitutionCodes);
    }

    /**
     * Convert holdings list. Copy favorite institutions
     *
     * @param Array[] $holdings Holdings
     *
     * @return Array[]
     */
    public function __invoke(array $holdings)
    {
        $favoriteInstitutions = [];

        foreach ($holdings as $group => $groupData) {
            foreach ($groupData['institutions'] as
                $institutionCode => $institution
            ) {
                if (isset($this->userInstitutionCodes[$institutionCode])) {
                    $favoriteInstitutions[$institutionCode] = $institution;
                    // Mark as favorite in favorite group and original group
                    $favoriteInstitutions[$institutionCode]['favorite'] = true;
                    $holdings[$group]['institutions'][$institutionCode]['favorite']
                        = true;
                }
            }
        }

        if ($favoriteInstitutions) {
            $favoriteHoldings = [
                'label'            => 'mylibraries',
                'institutions'    => $favoriteInstitutions
            ];

            $holdings = ['favorite' => $favoriteHoldings] + $holdings;
        }

        return $holdings;
    }
}
