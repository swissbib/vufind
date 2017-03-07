<?php
/**
 * Libadmin Logger
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 1/2/13
 * Time: 4:09 PM
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
 * @package  Log
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Log;

use Zend\Log\Logger as ZendLogger;

/**
 * Log special events
 *
 * @category Swissbib_VuFind2
 * @package  Log
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Logger extends ZendLogger
{
    /**
     * UntranslatedInstitutions
     *
     * @var String[]
     */
    protected $untranslatedInstitutions = [];

    /**
     * UngroupedInstitutions
     *
     * @var String[]
     */
    protected $ungroupedInstitutinos = [];

    /**
     * Log an untranslated institution
     *
     * @param String $institutionCode Institution code
     *
     * @return void
     */
    public function logUntranslatedInstitution($institutionCode)
    {
        if (!isset($this->untranslatedInstitutions[$institutionCode])) {
            $this->info('Untranslated institution: "' . $institutionCode . '"');

            $this->untranslatedInstitutions[$institutionCode] = $institutionCode;
        }
    }

    /**
     * Log an ungrouped institution
     *
     * @param String $institutionCode Institution code
     *
     * @return void
     */
    public function logUngroupedInstitution($institutionCode)
    {
        if (!isset($this->ungroupedInstitutinos[$institutionCode])) {
            $this->info(
                'No group found for institution: "' . $institutionCode . '"'
            );

            $this->ungroupedInstitutinos[$institutionCode] = $institutionCode;
        }
    }
}
