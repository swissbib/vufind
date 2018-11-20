<?php
/**
 * ValueConverter.php
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
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
 * Class ValueConverter
 *
 * Converter that evaluates config string values to their according data types
 * if possible.
 *
 * @category VuFind
 * @package  Swissbib\Util\Config
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
final class ValueConverter
{
    /**
     * Pattern that matches decimal number strings.
     */
    const DEC_NUMBER_PATTERN = '/^[0-9]+$/';

    /**
     * Pattern that matches hexadecimal number strings.
     */
    const HEX_NUMBER_PATTERN = '/^(0x|#)[0-9A-F]{1,8}$/i';

    /**
     * Pattern that matches octal number strings.
     */
    const OCT_NUMBER_PATTERN = '/^0[1-7][0-7]*$/';

    /**
     * Pattern that matches floating point number strings.
     */
    const FLT_NUMBER_PATTERN = '/^[0-9]*\.?[0-9]*$/';

    /**
     * ValueConverter constructor.
     */
    public function __construct()
    {
    }

    /**
     * Converts the content of the given config and returns a new config out of
     * it.
     *
     * @param Config $config The config to process.
     * @param bool   $fuzzy  Indicates whether to use the fuzzy type checks for
     *                       boolean values. When true (default), then the
     *                       {@link #isTruthy} and {@link #isFalsy} methods are
     *                       used. Otherwise only the strings 'true' and 'false'
     *                       are allowed as values.
     *
     * @return Config
     */
    public function convert(Config $config, $fuzzy = true)
    {
        $source = $config->toArray();
        $target = $this->_convertArray($source, $fuzzy);

        return new Config($target);
    }

    /**
     * Returns true when the given value is a string in a decimal, hexadecimal
     * or octal number format.
     *
     * @param string $value The value
     *
     * @return bool
     */
    public function isInteger($value)
    {
        return $this->isDecInteger($value) || $this->isHexInteger($value)
            || $this->isOctInteger($value);
    }

    /**
     * Returns true when the given value is a string in a decimal number format.
     *
     * @param string $value The value
     *
     * @return bool
     */
    public function isDecInteger($value)
    {
        return $this->_matchesNumericFormat(static::DEC_NUMBER_PATTERN, $value);
    }

    /**
     * Returns true when the given value is a string in a hexadecimal number
     * format.
     *
     * @param string $value The value
     *
     * @return bool
     */
    public function isHexInteger($value)
    {
        return $this->_matchesNumericFormat(static::HEX_NUMBER_PATTERN, $value);
    }

    /**
     * Returns true when the given value is a string in an octal number format.
     *
     * @param string $value The value
     *
     * @return bool
     */
    public function isOctInteger($value)
    {
        return $this->_matchesNumericFormat(static::OCT_NUMBER_PATTERN, $value);
    }

    /**
     * Returns true when the given value is a string in a floating point number
     * format.
     *
     * @param string $value The value
     *
     * @return bool
     */
    public function isFloat($value)
    {
        return $this->_matchesNumericFormat(static::FLT_NUMBER_PATTERN, $value);
    }

    /**
     * Returns true when the given value is a string that equals to 'true'.
     * This check is case-insensitive.
     *
     * @param string $value The value
     *
     * @return bool
     */
    public function isTrue($value)
    {
        return 'true' === strtolower($value);
    }

    /**
     * Returns true when the given value is 1 (numeric), '1' (string), true
     * (boolean), 'true' (string), 'on', 'y' or
     * 'yes'. For string inputs the case will be ignored.
     *
     * @param string $value The value
     *
     * @return bool
     */
    public function isTruthy($value)
    {
        if (is_string($value)) {
            $value = strtolower($value);
        }

        return in_array($value, [1, '1', true, 'true', 'on', 'y', 'yes'], true);
    }

    /**
     * Returns true when the given value is a string that equals to 'false'.
     * This check is case-insensitive.
     *
     * @param string $value The value
     *
     * @return bool
     */
    public function isFalse($value)
    {
        return 'false' === strtolower($value);
    }

    /**
     * Returns true when the given value is 0 (numeric zero), '0' (string
     * zero), false (boolean), 'false' (string),
     * 'off', 'n' or 'no'. For string inputs the case will be ignored.
     *
     * @param string $value The value
     *
     * @return bool
     */
    public function isFalsy($value)
    {
        if (is_string($value)) {
            $value = strtolower($value);
        }

        return in_array(
            $value, [0, '0', false, 'false', 'off', 'n', 'no'], true
        );
    }

    /**
     * Converts array
     *
     * @param array   $source The array
     * @param boolean $fuzzy  Should convert fuzzy?
     *
     * @return array
     */
    private function _convertArray(array &$source, $fuzzy)
    {
        foreach ($source as $key => $value) {
            if (is_array($value)) {
                $source[$key] = $this->_convertArray($value, $fuzzy);
            } else {
                if (is_string($value)) {
                    $source[$key] = $this->_convertValue($value, $fuzzy);
                }
            }
        }

        return $source;
    }

    /**
     * Converts value
     *
     * @param string $value The value
     * @param bool   $fuzzy Should convert fuzzy?
     *
     * @return bool|float|int
     */
    private function _convertValue(string $value, bool $fuzzy)
    {
        $isTrue = true === $fuzzy
            ? $this->isTruthy($value)
            : $this->isTrue(
                $value
            );
        $isFalse = true === $fuzzy
            ? $this->isFalsy($value)
            : $this->isFalse(
                $value
            );

        if ($isTrue) {
            $value = true;
        } elseif ($isFalse) {
            $value = false;
        } elseif ($this->isDecInteger($value)) {
            $value = intval($value);
        } elseif ($this->isHexInteger($value)) {
            $value = hexdec($value);
        } elseif ($this->isOctInteger($value)) {
            $value = octdec($value);
        } elseif ($this->isFloat($value)) {
            $value = (float)$value;
        }

        return $value;
    }

    /**
     * Matches the numeric format
     *
     * @param string $pattern The pattern
     * @param string $value   The value
     *
     * @return bool
     */
    private function _matchesNumericFormat(string $pattern, string $value)
    {
        return 1 === preg_match($pattern, $value);
    }
}
