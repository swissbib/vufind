<?php

namespace Swissbib\Util\Config;

use Zend\Config\Config;

/**
 * Utility component that converts flat arrays into multi-dimensional arrays based on key-path delimiters. The delimiter
 * is set to '.' my default which allows you to pass in arrays (or ini-file paths) with key in form of 'my.1.key.path'.
 *
 * @package Swissbib\Util\Config
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
     * @private
     * @var null|string
     */
    private $keyPathDelimiter = null;

    /**
     * FlatArrayConverter constructor.
     * @param null|string $keyPathDelimiter
     */
    public function __construct($keyPathDelimiter = null) {
        $this->keyPathDelimiter = is_string($keyPathDelimiter) ? $keyPathDelimiter : self::DEFAULT_KEY_PATH_DELIMITER;
    }

    /**
     * Converts the given source array into a multi-dimensional array. It assumes an associative array and expects each
     * key to be a path to some property where each path component is separated by the key path delimiter passed in to
     * the constructor. In case the key path of a property contains a numeric component it will be converted into a
     * number and is used as numeric index of the according sub-array.
     *
     * @param Config $source
     * @return array
     */
    public function fromFlatArray(Config $source) {
        $result = array();

        foreach ($source as $key => $value) {
            $path = explode($this->keyPathDelimiter, $key);
            $this->convertKeyPath($result, $path, $value);
        }

        return $result;
    }

    /**
     * Converts the content of the specified sections in the given Config object.
     *
     * @param \Zend\Config\Config $config
     * @param string|array $sectionNames
     * A string or an array of strings holding the names of the sections in the config to process.
     *
     * @return \Zend\Config\Config
     * The array will contain only the sections specified.
     */
    public function fromConfigSections(Config $config, $sectionNames) {
        $sectionNames = is_string($sectionNames) ? array($sectionNames) : array();
        $result = $this->processSections($config, $sectionNames);

        return new Config($result);
    }


    /**
     * @private
     * @param \Zend\Config\Config $config
     * @param array $sectionNames
     * @return array
     */
    private function processSections(Config $config, array $sectionNames) {
        $result = array();

        foreach ($sectionNames as $sectionName) {
            $sectionData = $this->fromSection($config, $sectionName);

            if (!is_null($sectionData)) {
                $result[$sectionName] = $sectionData;
            }
        }

        return $result;
    }

    /**
     * @private
     * @param \Zend\Config\Config $config
     * @param $sectionName
     * @return array|null
     */
    private function fromSection(Config $config, $sectionName) {
        $sectionData = isset($config->{$sectionName}) ? $config->{$sectionName} : null;
        $result = null;

        if (!is_null($sectionName)) {
            $result = $this->fromFlatArray($sectionData);
        }

        return $result;
    }

    /**
     * @private
     * @param array $target
     * @param $path
     * @param $value
     */
    private function convertKeyPath(array &$target, $path, $value) {

        for ($index = 0; $index < count($path); ++$index) {
            $key = $this->normalizeKey($path[$index]);

            if ($index < count($path) - 1) {
                if (!isset($target[$key])) {
                    $target[$key] = array();
                }
                $target = &$target[$key];
            } else {
                $target[$key] = $value;
            }
        }
    }

    /**
     * @private
     * @param $key
     * @return int
     */
    private function normalizeKey($key) {
        $isInteger = 1 === preg_match(self::NUMBER_SEARCH_STRING, $key);
        return $isInteger ? intval($key) : $key;
    }
}
