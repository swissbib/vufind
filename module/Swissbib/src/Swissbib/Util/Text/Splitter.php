<?php
/**
 * Splitter.php
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
namespace Swissbib\Util\Text;

/**
 * Class Splitter
 *
 * Utility component that provides functionality to split text based on words or
 * characters.
 *
 * @category VuFind
 * @package  Swissbib\Util\Text
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class Splitter
{
    /**
     * Regular expression pattern to search for whitespaces. This is the same as the
     * default set of whitespace characters also used by PHP's trim() function.
     */
    const PATTERN_WHITESPACE = '/[ \t\n\r\0\x0B]/';

    /**
     * Splitter constructor.
     *
     * @param bool $useWords Indicates whether to split based on a word limit (true)
     *                       or on characters (false).
     */
    public function __construct(bool $useWords = true)
    {
        $this->_useWords = $useWords;
    }

    /**
     * Internal storage for the splitting behavior indicator.
     *
     * @var bool
     */
    private $_useWords;

    /**
     * Splitting behavior indicator.
     *
     * @return bool
     */
    public function useWords(): bool
    {
        return $this->_useWords;
    }

    /**
     * Performs a text split at the specified $limit and returns an object that
     * contains the following information:
     *
     * 'text': The text before the actual split point.
     * 'truncated': Boolean that indicates whether the text was split actually.
     * 'overflow': The text after the split point.
     *
     * The actual split position must not be at $limit in case it points directly
     * into a word or whitespace character sequence. In those cases it will be the
     * less than $limit.
     *
     * @param string $text  The text to split.
     * @param int    $limit The position where ideally to split the text.
     *
     * @return \stdClass
     */
    public function split(string $text, int $limit): \stdClass
    {
        $splitPoint = $this->calculateSplitPoint($text, $limit);
        $info = (object)[
            'text'      => '',
            'truncated' => false,
            'overflow'  => ''
        ];

        if ($splitPoint === 0) {
            $info->text = $text;
        } else {
            $info->truncated = true;
            $info->text = substr($text, 0, $splitPoint);
            $info->overflow = substr($text, $splitPoint);
        }

        return $info;
    }

    /**
     * Calculates the position where to split the text actually based on the current
     * splitting behavior.
     *
     * When {@link #useWords} is true, then the text is split after last the number
     * of word expressed by the given limit. In case the has less than $limit words
     * the resulting split point will be the length of the given text. In this
     * scenario whitespaces will be excluded from counting.
     *
     * When {@link #useWords} is false, then characters (including any whitespace
     * except leading and trailing) is counted and the split is made at the last
     * word boundary that is less than or equal to the given limit.
     *
     * @param string $text  The text to split
     * @param int    $limit The number of words resp. characters after which to apply
     *                      the split.
     *
     * @return int The computed actual split position.
     */
    public function calculateSplitPoint(string $text, int $limit): int
    {
        $sanitized = trim($text);
        $data = $this->_analyze($sanitized);

        return $this->useWords()
            ? $this->_calculateWordSplitPoint($data, $limit)
            : $this->_calculateCharacterSplitPoint($data, $limit);
    }

    /**
     * Calculates the split point based on word count.
     *
     * @param \stdClass $data  The analysis out of the incoming text.
     * @param int       $limit The number of words after which to split.
     *
     * @return int
     */
    private function _calculateWordSplitPoint(\stdClass $data, int $limit): int
    {
        $words = array_slice($data->words, 0, $limit);
        $processed = [];

        while (count($words) > 1) {
            $processed[] = array_shift($words);
            $processed[] = array_shift($data->whitespaces);
        }

        // append last word
        $processed[] = array_shift($words);

        return strlen(implode('', $processed));
    }

    /**
     * Calculates the split point based on word count.
     *
     * @param \stdClass $data  The analysis out of the incoming text.
     * @param int       $limit The number of words after which to split.
     *
     * @return int
     */
    private function _calculateCharacterSplitPoint(\stdClass $data, int $limit): int
    {
        $sequence = $data->sequence;
        $processed = '';
        $result = 0;

        if ($data->length < $limit) {
            $result = $data->length;
        } else {
            while (!empty($sequence)) {
                $current = array_shift($sequence);

                // take a look into the future
                if (strlen($processed . $current) > $limit) {
                    break;
                }

                $processed .= $current;
            }

            // remove trailing whitespace in case limit overflow took place when
            // $current pointed to a word during the last loop cycle before break
            $result = strlen(rtrim($processed));
        }

        return $result;
    }

    /**
     * Internal method that performs text analysis to retrieve whitespace and word
     * components besides the original text and its length for further calculation.
     *
     * @param string $text The text to analyze.
     *
     * @return \stdClass
     */
    private function _analyze(string $text): \stdClass
    {
        $data = (object)[
            'text'        => $text,
            'length'      => strlen($text),
            'whitespaces' => $this->_extractWhitespaces($text),
            'words'       => $this->_extractWords($text),
            'sequence'    => []
        ];

        // Note: We know that splitting a two-end trimmed string by whitespace will
        // give us a word array which is exactly one element longer then the
        // whitespace array
        for ($index = 0; $index < count($data->whitespaces); ++$index) {
            $data->sequence[] = $data->words[$index];
            $data->sequence[] = $data->whitespaces[$index];
        }

        $data->sequence[] = $data->words[count($data->words) - 1];

        return $data;
    }

    /**
     * Searches for all whitespace sequences in the given text and returns them in
     * the order they appear in the text.
     *
     * @param string $text The text to extract whitespaces from.
     *
     * @return array
     */
    private function _extractWhitespaces(string $text): array
    {
        $extractions = [];
        preg_match_all(self::PATTERN_WHITESPACE, $text, $extractions);

        return $extractions[0];
    }

    /**
     * Extracts all potential word sequences from the given text and returns them in
     * the order they appear in the text.
     *
     * @param string $text The text to extract words from.
     *
     * @return array
     */
    private function _extractWords(string $text): array
    {
        return preg_split(self::PATTERN_WHITESPACE, $text);
    }
}
