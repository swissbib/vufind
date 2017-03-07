<?php
/**
 * Importer
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

/**
 * Import and convert a tab40 file into a vufind language file
 *
 * @category Swissbib_VuFind2
 * @package  Tab40Import
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Importer
{
    /**
     * Config
     *
     * @var Config
     */
    protected $config;

    /**
     * Constructor
     *
     * @param Config $config Config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Import data from source file and write to predefined path
     *
     * @param String $network    Network
     * @param String $locale     Locale
     * @param String $sourceFile SourceFile
     *
     * @return Result
     */
    public function import($network, $locale, $sourceFile)
    {
            // Read data
        $importedData    = $this->read($sourceFile);
            // Write data
        $languageFile    = $this->write($network, $locale, $importedData);

        return new Result(
            [
            'file'        => $languageFile,
            'count'        => sizeof($importedData),
            'network'    => $network,
            'locale'    => $locale,
            'source'    => $sourceFile
            ]
        );
    }

    /**
     * Read file into named list
     *
     * @param String $sourceFile SourceFile
     *
     * @return Array[]
     */
    protected function read($sourceFile)
    {
        $reader    = new Reader();

        return $reader->read($sourceFile);
    }

    /**
     * Write imported data to language file
     *
     * @param String  $network Network
     * @param String  $locale  Locale
     * @param Array[] $data    Data
     *
     * @return String
     */
    protected function write($network, $locale, array $data)
    {
        $writer    = new Writer($this->config->path);

        return $writer->write($network, $locale, $data);
    }
}
