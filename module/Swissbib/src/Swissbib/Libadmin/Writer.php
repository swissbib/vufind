<?php
/**
 * Libadmin Writer
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
 * @package  Libadmin
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Libadmin;

use Zend\Config\Writer\Ini as IniWriter;
use Zend\Config\Config;

/**
 * Write imported data to local system
 *
 * @category Swissbib_VuFind2
 * @package  Libadmin
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Writer
{
    /**
     * BasePath
     *
     * @var String
     */
    protected $basePath;

    /**
     * Initialize with base path
     * Defaults base path is languages in override dir
     *
     * @param String|Null $basePath BasePath
     */
    public function __construct($basePath = null)
    {
        $this->setBasePath($basePath);
    }

    /**
     * Set base path
     * null or false resets to default base path
     *
     * @param String|null $path Path
     *
     * @return void
     */
    protected function setBasePath($path)
    {
        if (is_null($path) || $path === false) {
            $this->basePath = LOCAL_OVERRIDE_DIR . '/languages';
        } else {
            $this->basePath = $path;
        }
    }

    /**
     * Save language file data into defined folder (depends on type and locale)
     *
     * @param Array  $data   Data
     * @param String $type   Type
     * @param String $locale Locale
     *
     * @throws \Exception
     *
     * @return String
     */
    public function saveLanguageFile(array $data, $type, $locale)
    {
        $pathFile = $this->basePath . '/' . $type . '/' . $locale . '.ini';
        $pathDir  = dirname($pathFile);
        $dirStatus = is_dir($pathDir) || mkdir($pathDir, 0777, true);

        if (!$dirStatus) {
            throw new \Exception('Cannot create language folder ' . $type);
        }

            // Replace double quotes, because they're invalid for ini format in zend
        $data    = $this->cleanData($data);
        $config    = new Config($data, false);
        $writer    = new IniWriter();

        $writer->toFile($pathFile, $config);

        return $pathFile;
    }

    /**
     * Save configuration file
     *
     * @param Array  $data     Data
     * @param String $filename Filename
     *
     * @throws \Exception
     *
     * @return String
     */
    public function saveConfigFile(array $data, $filename)
    {
        $pathFile = $this->basePath . '/' . $filename . '.ini';
        $pathDir  = dirname($pathFile);
        $dirStatus = is_dir($pathDir) || mkdir($pathDir, 0777, true);

        if (!$dirStatus) {
            throw new \Exception('Cannot create config folder ' . $filename);
        }

        $data    = $this->cleanData($data);
        $config    = new Config($data, false);
        $writer    = new IniWriter();

        $writer->toFile($pathFile, $config);

        return $pathFile;
    }

    /**
     * Clean data
     * Cleanup: Remove double quotes
     *
     * @param Array $data Data
     *
     * @return Array
     */
    protected function cleanData(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->cleanData($value);
            } else {
                $data[$key] = str_replace('"', '', $value);
            }
        }

        return $data;
    }
}
