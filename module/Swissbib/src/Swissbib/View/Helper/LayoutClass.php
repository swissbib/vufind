<?php
/**
 * LayoutClass
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
 * @author   Nicolas Karrer <nkarrer@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\View\Helper;

/**
 * Class LayoutClass
 *
 * @category Swissbib_VuFind2
 * @package  View_Helper
 * @author   Nicolas Karrer <nkarrer@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class LayoutClass extends \VuFind\View\Helper\Bootstrap3\LayoutClass
{
    /**
     * Invoke LayoutClass
     *
     * @param String $class Class
     *
     * @return String
     */
    public function __invoke($class)
    {
        $classString = '';

        switch ($class) {
        case 'mainbody':
            $classString .= $this->left ?
                'col-md-9 col-md-push-3 col-table-fix-md' :
                'col-md-9 col-table-fix-md';
            break;
        case 'sidebar':
            $classString .= $this->left
                ? 'sidebar col-md-3 col-md-pull-9 col-table-fix-md hidden-print'
                : 'sidebar col-md-3 col-table-fix-md hidden-print';
        }

        $htmlLayoutClass = $this->getView()->htmlLayoutClass;

        return isset($htmlLayoutClass) ? $classString . ' ' .
            $htmlLayoutClass : $classString;
    }
}
