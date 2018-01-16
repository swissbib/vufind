<?php
/**
 * FlatArrayConverter.php
 *
 * PHP Version 7
 *
 * Copyright (C) swissbib 2018
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
 *
 * @category VuFind
 * @package  Swissbib\Util\Config
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Util\Config;

use Zend\Config\Config;

/**
 * Class FlatArrayConverter
 *
 * Utility component that converts flat arrays into multi-dimensional arrays based on key-path delimiters. The delimiter
 * is set to '.' my default which allows you to pass in arrays (or ini-file paths) with key in form of 'my.1.key.path'.
 *
 * @category VuFind
 * @package  Swissbib\Util\Config
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
final class FlatArrayConverter
{
    /**
     * The default value used as delimiter, if none was specified on construction time.
     */
    const DEFAULT_KEY_PATH_DELIMITER = '.';

    /**
     * Used to check whether a key path component is a number.
     */
    const NUMBER_SEARCH_STRING = '/[0-9]/';

    /**
     * The key path delimiter
     *
     * @var null|string
     */
    private $_keyPathDelimiter = null;

    /**
     * FlatArrayConverter constructor.
     *
     * @param null|string $keyPathDelimiter The key path delimiter
     */
    public function __construct($keyPathDelimiter = null)
    {
        $this->_keyPathDelimiter = is_string($keyPathDelimiter) ? $keyPathDelimiter : self::DEFAULT_KEY_PATH_DELIMITER;
    }

    /**
     * Converts the given source array into a multi-dimensional array. It assumes an associative array and expects each
     * key to be a path to some property where each path component is separated by the key path delimiter passed in to
     * the constructor. In case the key path of a property contains a numeric component it will be converted into a
     * number and is used as numeric index of the according sub-array.
     *
     * @param Config $source The source
     *
     * @return array
     */
    public function fromFlatArray(Config $source)
    {
        $result = [];

        foreach ($source as $key => $value) {
            $path = explode($this->_keyPathDelimiter, $key);
            $this->_convertKeyPath($result, $path, $value);
        }

        return $result;
    }

    /**
     * Converts the content of the specified sections in the given Config object.
     *
     * @param \Zend\Config\Config $config       The config
     * @param string|array        $sectionNames A string or an array of strings holding the names of the sections in
     *                                          the config to process.
     *
     * @return \Zend\Config\Config The array will contain only the sections specified.
     */
    public function fromConfigSections(Config $config, $sectionNames)
    {
        $sectionNames = is_string($sectionNames) ? [$sectionNames] : [];
        $result = $this->_processSections($config, $sectionNames);

        return new Config($result);
    }

    /**
     * Process sections
     *
     * @param \Zend\Config\Config $config       The config
     * @param array               $sectionNames The section names
     *
     * @return array
     */
    private function _processSections(Config $config, array $sectionNames)
    {
        $result = [];

        foreach ($sectionNames as $sectionName) {
            $sectionData = $this->_fromSection($config, $sectionName);

            if (!is_null($sectionData)) {
                $result[$sectionName] = $sectionData;
            }
        }

        return $result;
    }

    /**
     * From Section
     *
     * @param \Zend\Config\Config $config      The config
     * @param string              $sectionName The section name
     *
     * @return array|null
     */
    private function _fromSection(Config $config, $sectionName)
    {
        $sectionData = isset($config->{$sectionName}) ? $config->{$sectionName} : null;
        $result = null;

        if (!is_null($sectionName)) {
            $result = $this->fromFlatArray($sectionData);
        }

        return $result;
    }

    /**
     * Normalizes the key
     *
     * @param string $key The key
     *
     * @return int
     */
    private function _normalizeKey(string $key)
    {
        $isInteger = 1 === preg_match(self::NUMBER_SEARCH_STRING, $key);
        return $isInteger ? intval($key) : $key;
    }

    /**
     * Converts the key path
     *
     * @param array  $target The target
     * @param string $path   The path
     * @param string $value  The value
     *
     * @return void
     */
    private function _convertKeyPath(array &$target, string $path, string $value)
    {

        for ($index = 0; $index < count($path); ++$index) {
            $key = $this->_normalizeKey($path[$index]);

            if ($index < count($path) - 1) {
                if (!isset($target[$key])) {
                    $target[$key] = [];
                }
                $target = &$target[$key];
            } else {
                $target[$key] = $value;
            }
        }
    }
}
