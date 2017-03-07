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

/**
 * Read data from tab40 file
 * Data in tab40 files have a fixed layout
 * See the header of a tab40 file for details about the format
 *
 * @category Swissbib_VuFind2
 * @package  Tab40Import
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Reader
{
    /**
     * Read source file into associative data array
     *
     * @param String $sourceFile SourceFile
     *
     * @return Array[]
     */
    public function read($sourceFile)
    {
        $rawLines = $this->readLines($sourceFile);
        $lines = $this->filterLines($rawLines);
        $data = [];

        foreach ($lines as $line) {
            $data[] = [
                'code'            => trim(substr($line, 0, 5)),
                'sublibrary'    => trim(substr($line, 5, 5)),
                'label'            => trim(substr($line, 14))
            ];
        }

        return $data;
    }

    /**
     * Read file into lines
     * Convert data to utf8
     *
     * @param String $sourceFile SourceFile
     *
     * @return String[]
     *
     * @throws Exception
     */
    protected function readLines($sourceFile)
    {
        if (!file_exists($sourceFile)) {
            throw new Exception('File not found "' . $sourceFile . '"');
        }

        $rawLines    = file($sourceFile);
        $rawLines    = array_map('utf8_encode', $rawLines);

        return $rawLines;
    }

    /**
     * Filter out empty and comment lines
     *
     * @param String[] $rawLines Rawlines
     *
     * @return String[]
     */
    protected function filterLines(array $rawLines)
    {
        foreach ($rawLines as $index => $line) {
                // Commented line
            if ('!' === substr($line, 0, 1)) {
                unset($rawLines[$index]);
            }
            if ('' === trim($line)) {
                unset($rawLines[$index]);
            }
        }

        return $rawLines;
    }
}
