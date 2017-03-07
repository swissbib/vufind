<?php
/**
 * InstitutionSorter
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
 * Sort institutions based on position in list
 *
 * @category Swissbib_VuFind2
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class InstitutionSorter extends AbstractHelper
{
    /**
     * Institutions
     *
     * @var Array List of institutions. BibCode is the key, position the value
     */
    protected $institutions = [];

    /**
     * Initialize with institution list
     *
     * @param Array $institutions Institutions
     */
    public function __construct(array $institutions)
    {
        $this->institutions = array_flip($institutions);
    }

    /**
     * Sort list of institution
     *
     * @param Array   $institutions Institutions
     * @param Boolean $extended     Extended
     *
     * @return Array
     */
    public function sortInstitutions(array $institutions, $extended = false)
    {
        $sorted         = [];
        $missingCounter = 2000;

            // No sorting for single institution
        if (sizeof($institutions) === 1) {
            return $institutions;
        }

        foreach ($institutions as $institution) {
            $institutionKey = $extended ? $institution['institution'] : $institution;
            $pos    = isset($this->institutions[$institutionKey]) ?
                $this->institutions[$institutionKey] : $missingCounter++;
            $sorted[$pos] = $institution;
        }

        ksort($sorted);

        return array_values($sorted);
    }
}
