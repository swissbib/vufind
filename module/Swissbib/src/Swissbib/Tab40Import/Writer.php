<?php
/**
 * Writer
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
 * @package  Tab40Import
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\Tab40Import;

use Zend\Config\Config;
use Zend\Config\Writer\Ini as IniWriter;

/**
 * Write tab40 data to label file
 *
 * @category Swissbib_VuFind2
 * @package  Tab40Import
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Writer
{
    /**
     * Base path for storage
     *
     * @var String
     */
    protected $basePath;

    /**
     * Initialize with base path
     *
     * @param String $basePath BasePath
     */
    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Write data to label file
     *
     * @param String  $network Network
     * @param String  $locale  Locale
     * @param Array[] $data    Data
     *
     * @return String Path to file
     */
    public function write($network, $locale, array $data)
    {
        $data = $this->convertData($data);
        $config = new Config($data, false);
        $writer = new IniWriter();

        $pathFile    = $this->buildPath($network, $locale);

        $writer->toFile($pathFile, $config);

        return $pathFile;
    }

    /**
     * Convert data to label file format
     *
     * @param Array $data Data
     *
     * @return Array
     */
    protected function convertData(array $data)
    {
        $labelData = [];

        foreach ($data as $item) {
            $key = strtolower($item['sublibrary'] . '_' . $item['code']);
            $label = str_replace('"', '', $item['label']);

            $labelData[$key] = $label;
        }

        return $labelData;
    }

    /**
     * Build file path based on base path, network and locale
     *
     * @param String $network Network
     * @param String $locale  Locale
     *
     * @return String
     */
    protected function buildPath($network, $locale)
    {
        $network = strtolower(trim($network));
        $locale = strtolower(trim($locale));

        $path = realpath($this->basePath . '-' . $network) . '/' . $locale . '.ini';

        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }
}
