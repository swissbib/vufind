<?php
/**
 * InstitutionLoader
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
 * @category Libadmin_VuFind2
 * @package  Institution
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Libadmin\Institution;

use Zend\Json\Server\Exception\ErrorException;

/**
 * InstitutionLoader
 *
 * @category Libadmin_VuFind2
 * @package  Institution
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class InstitutionLoader
{
    /**
     * Cache folder
     *
     * @var string
     */
    protected $cacheDir;

    /**
     * Cache file
     *
     * @var string
     */
    protected $cacheFile;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cacheDir     = realpath(APPLICATION_PATH . '/data/cache');
        $this->cacheFile    = 'libadmin_all.json';
    }

    /**
     * Returns grouped institutions
     *
     * @throws ErrorException
     *
     * @return array
     */
    public function getGroupedInstitutions()
    {
        $filePath   = $this->cacheDir . '/' . $this->cacheFile;
        $cacheData  = file_exists($filePath) ? file_get_contents($filePath) : '';
        $jsonData   = json_decode($cacheData, true);

        if (empty($jsonData['data'])) {
            throw new ErrorException("No valid library data supplied.");
        }

        return $jsonData['data'];
    }
}
