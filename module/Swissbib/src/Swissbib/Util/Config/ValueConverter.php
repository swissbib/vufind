<?php
/**
 * Created by IntelliJ IDEA.
 * User: edmundmaruhn
 * Date: 05.12.17
 * Time: 19:02
 */

namespace Swissbib\Util\Config;

use Zend\Config\Config;

/**
 * Converter that evaluates config string values to their according data types if possible.
 *
 * @package Swissbib\Util\Config
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
     * Converts the content of the given config and returns a new config out of it.
     *
     * @param Config $config
     * The config to process.
     *
     * @param bool   $fuzzy
     * Indicates whether to use the fuzzy type checks for boolean values. When true (default), then the
     * {@link #isTruthy} and {@link #isFalsy} methods are used. Otherwise only the strings 'true' and 'false' are
     * allowed as values.
     *
     * @return Config
     */
    public function convert(Config $config, $fuzzy = true) 
    {
        $source = $config->toArray();
        $target = $this->convertArray($source, $fuzzy);

        return new Config($target);
    }

    /**
     * Returns true when the given value is a string in a decimal, hexadecimal or octal number format.
     *
     * @param  string $value
     * @return bool
     */
    public function isInteger($value) 
    {
        return $this->isDecInteger($value) || $this->isHexInteger($value) || $this->isOctInteger($value);
    }

    /**
     * Returns true when the given value is a string in a decimal number format.
     *
     * @param  string $value
     * @return bool
     */
    public function isDecInteger($value) 
    {
        return $this->matchesNumericFormat(static::DEC_NUMBER_PATTERN, $value);
    }

    /**
     * Returns true when the given value is a string in a hexadecimal number format.
     *
     * @param  string $value
     * @return bool
     */
    public function isHexInteger($value) 
    {
        return $this->matchesNumericFormat(static::HEX_NUMBER_PATTERN, $value);
    }

    /**
     * Returns true when the given value is a string in an octal number format.
     *
     * @param  string $value
     * @return bool
     */
    public function isOctInteger($value) 
    {
        return $this->matchesNumericFormat(static::OCT_NUMBER_PATTERN, $value);
    }

    /**
     * Returns true when the given value is a string in a floating point number format.
     *
     * @param  string $value
     * @return bool
     */
    public function isFloat($value) 
    {
        return $this->matchesNumericFormat(static::FLT_NUMBER_PATTERN, $value);
    }

    /**
     * Returns true when the given value is a string that equals to 'true'. This check is case-insensitive.
     *
     * @param  string $value
     * @return bool
     */
    public function isTrue($value) 
    {
        return 'true' === strtolower($value);
    }

    /**
     * Returns true when the given value is 1 (numeric), '1' (string), true (boolean), 'true' (string), 'on', 'y' or
     * 'yes'. For string inputs the case will be ignored.
     *
     * @param  string $value
     * @return bool
     */
    public function isTruthy($value) 
    {
        if (is_string($value)) {
            $value = strtolower($value);
        }

        return in_array($value, array(1, '1', true, 'true', 'on', 'y', 'yes'), true);
    }

    /**
     * Returns true when the given value is a string that equals to 'false'. This check is case-insensitive.
     *
     * @param  string $value
     * @return bool
     */
    public function isFalse($value) 
    {
        return 'false' === strtolower($value);
    }

    /**
     * Returns true when the given value is 0 (numeric zero), '0' (string zero), false (boolean), 'false' (string),
     * 'off', 'n' or 'no'. For string inputs the case will be ignored.
     *
     * @param  string $value
     * @return bool
     */
    public function isFalsy($value) 
    {
        if (is_string($value)) {
            $value = strtolower($value);
        }

        return in_array($value, array(0, '0', false, 'false', 'off', 'n', 'no'), true);
    }


    /**
     * @private
     * @param array   $source
     * @param boolean $fuzzy
     * @return array
     */
    private function convertArray(array &$source, $fuzzy) 
    {
        foreach ($source as $key => $value) {
            if (is_array($value)) {
                $source[$key] = $this->convertArray($value, $fuzzy);
            } else if (is_string($value)) {
                $source[$key] = $this->convertValue($value, $fuzzy);
            }
        }

        return $source;
    }

    /**
     * @private
     * @param string  $value
     * @param boolean $fuzzy
     * @return bool|float|int
     */
    private function convertValue($value, $fuzzy) 
    {
        $isTrue = true === $fuzzy ? $this->isTruthy($value) : $this->isTrue($value);
        $isFalse = true === $fuzzy ? $this->isFalsy($value) : $this->isFalse($value);

        if ($isTrue) {
            $value = true;
        } else if ($isFalse) {
            $value = false;
        } else if ($this->isDecInteger($value)) {
            $value = intval($value);
        } else if ($this->isHexInteger($value)) {
            $value = hexdec($value);
        } else if ($this->isOctInteger($value)) {
            $value = octdec($value);
        } else if ($this->isFloat($value)) {
            $value = (double) $value;
        }

        return $value;
    }

    /**
     * @private
     * @param $pattern
     * @param string  $value
     * @return bool
     */
    private function matchesNumericFormat($pattern, $value) 
    {
        return 1 === preg_match($pattern, $value);
    }
}