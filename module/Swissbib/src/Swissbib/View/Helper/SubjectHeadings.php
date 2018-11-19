<?php
/**
 * SubjectHeadings
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

use Zend\View\Helper\AbstractHelper;

/**
 * View script for rendering subject headings of all vocabulairies,
 * including local ones
 *
 * @category Swissbib_VuFind
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class SubjectHeadings extends AbstractHelper
{
    /**
     * Invoke SubjectHeadings
     *
     * @param array $subjectHeadings SubjectHeadings
     *
     * @return string
     */
    public function __invoke(array $subjectHeadings = [])
    {
        $title = '';
        $lcsh = '';
        foreach ($subjectHeadings as $heading) {
            if (array_key_exists('@ind2', $heading) && $heading['@ind2'] === '0') {
                $title = '<h4>LCSH</h4>';
                $lcsh = $heading['650a'];
            }
        }

        return 'aus dem View-Helper SubjectHeadings' .
            $title . '<p>' . $lcsh . '</p>';
    }
}
