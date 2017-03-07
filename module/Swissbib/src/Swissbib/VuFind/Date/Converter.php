<?php
/**
 * Converter
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
 * @package  VuFind_Date
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\VuFind\Date;

use VuFind\Date\Converter as VFConverter;
use DateTime, VuFind\Exception\Date as DateException;

/**
 * Converter
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Date
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Converter extends VFConverter
{
    /**
     * Generic method for conversion of a time / date string
     *
     * @param string $inputFormat  The format of the time string to be changed
     * @param string $outputFormat The desired output format
     * @param string $dateString   The date string
     *
     * @throws DateException
     * @return string               A re-formated time string
     */
    public function convert($inputFormat, $outputFormat, $dateString)
    {
        // default return format of DateTime::getLastErrors()
        $getErrors = [
            'warning_count' => 0,
            'warnings' => [],
            'error_count' => 0,
            'errors' => []
        ];

        // For compatibility with PHP 5.2.x, we have to restrict the input formats
        // to a fixed list...  but we'll check to see if we have access to PHP 5.3.x
        // before failing if we encounter an input format that isn't whitelisted.
        $validFormats = [
            "m-d-Y", "m-d-y", "m/d/Y", "m/d/y", "U", "m-d-y H:i", "Y-m-d",
            "Y-m-d H:i"
        ];
        $isValid = in_array($inputFormat, $validFormats);
        if ($isValid) {
            if ($inputFormat == 'U') {
                // Special case for Unix timestamps:
                $dateString = '@' . $dateString;
            } else {
                // Strip leading zeroes from date string and normalize date separator
                // to slashes:
                $regEx = '/0*([0-9]+)(-|\/)0*([0-9]+)(-|\/)0*([0-9]+)/';
                $dateString = trim(preg_replace($regEx, '$1/$3/$5', $dateString));
            }
            try {
                $date = new DateTime($dateString);
            } catch (\Exception $e) {
                $getErrors['error_count']++;
                $getErrors['errors'][] = $e->getMessage();
            }
        } else {
            if (!method_exists('DateTime', 'createFromFormat')) {
                throw new DateException(
                    "Date format {$inputFormat} requires PHP 5.3 or higher."
                );
            }
            $date = DateTime::createFromFormat($inputFormat, $dateString);
            $getErrors = DateTime::getLastErrors();
        }

        if ($getErrors['warning_count'] == 0
            && $getErrors['error_count'] == 0 && $date
        ) {

            return $date->format($outputFormat);
        } else {
            //yymd
            //todo GH: just an intermediary solution because we get an conversion
            // error using the content sent by Aleph
            //dates with 00000000
            //e.g.: http://alephtest.unibas.ch:1891/rest-dlf/patron/B219684/
            // circulationActions/loans/?view=full
            //<z30-inventory-number-date>00000000</z30-inventory-number-date>

            return DateTime::createFromFormat('yymd', '19000101');
        }
    }
}
