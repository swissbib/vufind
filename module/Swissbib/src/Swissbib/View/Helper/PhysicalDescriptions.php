<?php
/**
 * PhysicalDescriptions
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
 * @author   Nicolas Karrer <nkarrer@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Format physical descriptions data array
 * Fetch all relevant data and build a comma separated list
 *
 * @category Swissbib_VuFind
 * @package  View_Helper
 * @author   Nicolas Karrer <nkarrer@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class PhysicalDescriptions extends AbstractHelper
{
    /**
     * Format integer with thousand separator
     *
     * @param Array $physicalDescriptions PhysicalDescriptions
     *
     * @return String
     */
    public function __invoke(array $physicalDescriptions)
    {
        $string = '';

        foreach ($physicalDescriptions as $physicalDescription) {
            unset($physicalDescription['@ind1'], $physicalDescription['@ind2']);
            $types = array_keys($physicalDescription);
            $types = array_map('strval', $types);
            foreach ($types as $type) {
                if (isset($physicalDescription[$type])) {
                    if ($type == "1extent") {
                        $string = $physicalDescription[$type];
                    } else if (strpos($type, 'extent') !== false) {
                        $string .= ', ' . $physicalDescription[$type];
                    } else if (strpos($type, 'details') !== false) {
                        $string .= ' : ' . $physicalDescription[$type];
                    } else if (strpos($type, 'dimensions') !== false) {
                        $string .= ' ; ' . $physicalDescription[$type];
                    } else if (strpos($type, 'materials') !== false) {
                        $string .= ' + ' . $physicalDescription[$type];
                    } else if (strpos($type, 'type') !== false) {
                        $string .= ' ' . $physicalDescription[$type];
                    } else if (strpos($type, 'size') !== false) {
                        $string .= ' ; ' . $physicalDescription[$type];
                    } else if (strpos($type, 'appliesTo') !== false) {
                        $string .= ' ; ' . $physicalDescription[$type];
                    }
                }
            }
        }

        return $string;
    }
}
