<?php
/**
 * CustomizedMethods
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
 * @package  RecordDriver_Helper
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\RecordDriver\Helper;

use Zend\Config\Config;

/**
 * Base class for customizable method calls
 * Call callMethod with a method name. It will try the following things:
 * Example method: myDummyMethod, example key: a100
 * - myDummyMethodA100
 * - myDummyMethodBase
 * - missingMethod
 *
 * @category Swissbib_VuFind2
 * @package  RecordDriver_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
abstract class CustomizedMethods
{
    /**
     * Config
     *
     * @var Config
     */
    protected $config;

    /**
     * Initialize with config
     *
     * @param Config $config Config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Calling internal method
     *
     * @param String $methodName MethodName
     * @param String $key        Key
     * @param array  $arguments  Arguments
     *
     * @return mixed
     */
    protected function callMethod($methodName, $key, array $arguments = [])
    {
        $customMethod    = $methodName . strtoupper($key);
        $baseMethod      = $methodName . 'Base';

        if (method_exists($this, $customMethod)) {
            return call_user_func_array([$this, $customMethod], $arguments);
        } elseif (method_exists($this, $baseMethod)) {
            return call_user_func_array([$this, $baseMethod], $arguments);
        } else {
            return $this->missingMethod($methodName, $key, $arguments);
        }
    }

    /**
     * Handle calls to missing methods
     * This means neither the base method nor the custom method was implemented
     *
     * @param String $methodName MethodName
     * @param String $key        Key
     * @param Array  $arguments  Arguments
     *
     * @return Boolean|Mixed
     */
    protected function missingMethod($methodName, $key, $arguments)
    {
        return false;
    }

    /**
     * Parse values from data array into template string
     *
     * @param String  $string    String
     * @param Array   $data      Key => Data to be replaced in String
     * @param Boolean $addBraces Wrap array keys with currly
     *                           braces for template usage
     *
     * @return String
     */
    protected function templateString($string, array $data, $addBraces = true)
    {
        if ($addBraces) {
            $newData    = [];
            foreach ($data as $key => $value) {
                $newData['{' . $key . '}'] = $value;
            }
            $data = $newData;
        }

        return str_replace(array_keys($data), array_values($data), trim($string));
    }

    /**
     * Check whether config value exits
     *
     * @param String $key Key
     *
     * @return Boolean
     */
    protected function hasConfigValue($key)
    {
        return $this->config->offsetExists($key);
    }

    /**
     * Get config value
     *
     * @param String $key Key
     *
     * @return String
     */
    protected function getConfigValue($key)
    {
        return $this->config->get($key);
    }

    /**
     * Check whether value is defined in a comma separated config parameter
     *
     * @param String $configKey ConfigKey
     * @param String $value     Value
     *
     * @return Boolean
     */
    protected function isValueInConfigList($configKey, $value)
    {
        $configValues    = $this->getConfigList($configKey);

        return in_array($value, $configValues);
    }

    /**
     * Get list items from config
     *
     * @param String  $configKey ConfigKey
     * @param Boolean $trim      Trim
     * @param String  $delimiter Delimiter
     *
     * @return String[]
     */
    protected function getConfigList($configKey, $trim = true, $delimiter = ',')
    {
        $data = [];

        if ($this->config->offsetExists($configKey)) {
            $configValue = $this->config->get($configKey);
            $data        = explode($delimiter, $configValue);

            if ($trim) {
                $data = array_map('trim', $data);
            }
        }
        return $data;
    }
}
