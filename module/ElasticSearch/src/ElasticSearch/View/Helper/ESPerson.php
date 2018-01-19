<?php
/**
 * ESPerson.php
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
 * @package  ElasticSearch\View\Helper
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace ElasticSearch\View\Helper;

use Swissbib\Util\Text\Splitter;

/**
 * Class ESPerson
 *
 * @category VuFind
 * @package  ElasticSearch\View\Helper
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class ESPerson extends AbstractHelper
{
    /**
     * Gets the MetadataPrefix
     *
     * @return string
     */
    protected function getMetadataPrefix(): string
    {
        return 'card.knowledge.person.metadata';
    }

    /**
     * Template method subclasses may override to provide an array that maps
     * metadata keys on methods on this helper. It will be set on the metadata
     * view helper. Then you can call the MetadataViewHelper#getMetadataList()
     * method with the keys of this array to retrieve these metadata
     * information.
     *
     * @return array
     */
    protected function getMetadataMethodMap(): array
    {
        return [
            'job'         => 'getJobInfo',
            'birth'       => 'getBirthInfo',
            'death'       => 'getDeathInfo',
            'nationality' => 'getNationalityInfo'
        ];
    }

    /**
     * The type of data this helper handles. Used to resolve type specific urls
     * like for the detail page link.
     *
     * @return string
     */
    public function getType(): string
    {
        return 'person';
    }

    /**
     * The person
     *
     * @var \ElasticSearch\VuFind\RecordDriver\ESPerson
     */
    private $_person;

    /**
     * Gets the Person
     *
     * @return \ElasticSearch\VuFind\RecordDriver\ESPerson
     */
    public function getPerson()
    {
        return $this->_person;
    }

    /**
     * Sets the Person
     *
     * @param \ElasticSearch\VuFind\RecordDriver\ESPerson $_person The person
     *
     * @return void
     */
    public function setPerson(
        \ElasticSearch\VuFind\RecordDriver\ESPerson $_person
    ) {
        parent::setDriver($_person);
        $this->_person = $_person;
    }

    /**
     * Gets the DisplayName
     *
     * @return null|string
     */
    public function getDisplayName()
    {
        $first = $this->getPerson()->getFirstName();
        $last = $this->getPerson()->getLastName();
        $name = $this->getPerson()->getName();
        $displayName = null;

        $first = is_array($first) ? implode(' ', $first) : $first;
        $last = is_array($last) ? implode(' ', $last) : $last;
        $name = is_array($name) ? implode(' ', $name) : $name;

        if (!is_null($first) && !is_null($last)) {
            $displayName = sprintf('%s %s', $first, $last);
        } else if (!is_null($first)) {
            $displayName = sprintf('%s', $first);
        } else if (!is_null($last)) {
            $displayName = sprintf('%s', $last);
        } else if (!is_null($name)) {
            $displayName = sprintf('%s', $name);
        }

        return $displayName;
    }

    /**
     * Gets the Lifetime
     *
     * @return null|string
     */
    public function getLifetime()
    {
        $birth = $this->getPerson()->getBirthYear();
        $death = $this->getPerson()->getDeathYear();
        $lifetime = null;

        if (!is_null($birth) && !is_null($death)) {
            $lifetime = sprintf('(%s - %s)', $birth, $death);
        } else if (!is_null($birth)) {
            $lifetime = sprintf('(%s - ?)', $birth);
        } else if (!is_null($death)) {
            $lifetime = sprintf('(? - %s)', $death);
        }

        return $lifetime;
    }

    /**
     * Gets the BirthInfo
     *
     * @param string $dateFormat The date format
     * @param string $separator  The separator
     *
     * @return null|string
     */
    public function getBirthInfo(
        string $dateFormat = 'd.m.Y', string $separator = ', '
    ) {
        return $this->getDateAndPlaceInfo(
            $dateFormat, $separator, $this->getPerson()->getBirthDate(),
            $this->getPerson()->getBirthPlaceDisplayField()
        );
    }

    /**
     * Gets the DeathInfo
     *
     * @param string $dateFormat The date format
     * @param string $separator  The separator
     *
     * @return null|string
     */
    public function getDeathInfo(
        string $dateFormat = 'd.m.Y', string $separator = ', '
    ) {
        return $this->getDateAndPlaceInfo(
            $dateFormat, $separator, $this->getPerson()->getDeathDate(),
            $this->getPerson()->getDeathPlaceDisplayField()
        );
    }

    /**
     * Gets the DateAndPlaceInfo
     *
     * @param string         $dateFormat The date format
     * @param string         $separator  The separator
     * @param \DateTime|null $date       The (optional) date
     * @param array          $place      The (optional) place
     *
     * @return null|string
     */
    protected function getDateAndPlaceInfo(
        string $dateFormat, string $separator, \DateTime $date = null,
        array $place = null
    ) {
        $date = is_null($date) ? null : $date->format($dateFormat);
        $place = is_null($place) ? null : implode($separator, $place);
        $result = null;

        if (!is_null($date) && !is_null($place)) {
            $result = sprintf('%s' . $separator . '%s', $date, $place);
        } else if (!is_null($date)) {
            $result = sprintf('%s', $date);
        } else if (!is_null($place)) {
            $result = sprintf('%s', $place);
        }

        return $result;
    }

    /**
     * Gets the JobInfo
     *
     * @param string $delimiter The delimiter
     *
     * @return null|string
     */
    public function getJobInfo(string $delimiter = ', ')
    {
        $occupation = $this->getPerson()->getOccupationDisplayField();

        if (is_array($occupation)) {
            $occupation = implode($delimiter, $occupation);
        }

        return strlen($occupation) > 0 ? $occupation : null;
    }

    /**
     * Gets the NationalityInfo
     *
     * @return null
     */
    public function getNationalityInfo()
    {
        $nationality = $this->getPerson()->getNationalityDisplayField();

        if (is_array($nationality)) {
            $nationality = count($nationality) > 0 ? $nationality[0] : '';
        }

        return strlen($nationality) > 0 ? $nationality : null;
    }

    /**
     * Has Thumbnail
     *
     * @return bool
     */
    public function hasThumbnail(): bool
    {
        $thumbnail = $this->getPerson()->getThumbnail();
        return is_array($thumbnail) && count($thumbnail) > 0;
    }

    /**
     * Resolves the url to the thumbnail image for a person.
     *
     * @return string
     */
    public function getThumbnailPath()
    {
        return $this->hasThumbnail() ? $this->getPerson()->getThumbnail()[0] : null;
    }

    /**
     * Has Abstract
     *
     * @return bool
     */
    public function hasAbstract()
    {
        return !is_null($this->getPerson()->getAbstract());
    }

    /**
     * Gets the AbstractInfo
     *
     * @param int  $limit      Indicates after how many words (or characters) to
     *                         split.
     * @param bool $countWords Indicates whether $splitPoint expresses the number of
     *                         words (true) or characters (false) after which
     *                         truncation has to be performed.
     *
     * @return \stdClass
     */
    public function getAbstractInfo(int $limit = 30, bool $countWords = true)
    {
        $info = null;

        if ($this->hasAbstract()) {
            $abstract = $this->getPerson()->getAbstract();
            // ignore surrounding whitespace at all
            $abstract = trim($abstract);

            $info = (new Splitter($countWords))->split($abstract, $limit);
            $info->label = $this->getView()->translate(
                'card.knowledge.person.metadata.abstract'
            );
        }

        return $info;
    }

    /**
     * Calculates the SplitPoint
     *
     * @param string $text     The text
     * @param int    $limit    The truncationWordCount
     * @param bool   $useWords The useWords
     *
     * @return int
     */
    protected function calculateSplitPoint(
        string $text, int $limit = 30, $useWords = true
    ) {
        return $useWords
            ? $this->_calculateWordSplitPoint($text, $limit)
            : $this->_calculateCharacterSplitPoint($text, $limit);
    }

    /**
     * Computes the text split point based on word count.
     *
     * @param string $text  The text to split.
     * @param int    $limit The number of words after which to split.
     *
     * @return int
     */
    private function _calculateWordSplitPoint(string $text, int $limit): int
    {
        // split at space character
        $delimiter = ' ';
        $words = explode($delimiter, $text);
        $processed = [];
        $count = 0;

        // loop over chunks
        foreach ($words as $word) {
            // trim chunk
            $sanitized = trim($word);

            // check if at least one character is in the trimmed chunk
            if (strlen($sanitized) > 0) {
                // if so, count word
                ++$count;
            }

            // append untrimmed chunk to list of processed words
            $processed[] = $word;

            // check if word count limit has been reached
            if ($count === $limit) {
                // if so, break the loop
                break;
            }
        }

        // join all processed words with space character
        // return length of joined string
        return strlen(implode($delimiter, $processed));
    }

    /**
     * Computes the text split point based on character count. The text will be split
     * at the nearest word boundary which is equal to or less than the given limit.
     *
     * @param string $text  The text to split.
     * @param int    $limit The number of characters after which to attempt to split.
     *
     * @return int
     */
    private function _calculateCharacterSplitPoint(string $text, int $limit): int
    {
        $delimiters = '/[ \t\n\r\0\x0B]/';
        $length = strlen($text);
        $position = 0;

        // check whether the limit equals or exceeds the length of the text
        if ($limit >= $length) {
            // if so, use length of text
            $position = $length;
        } else {
            // check if limit points to a word boundary
            $character = mb_substr($text, $limit, 1);

            if (preg_match($delimiters, $character) === 1) {
                // if so, use limit
                $position = $limit;
            } else {
                // otherwise remove the last potential word
                $whitespaces = [];
                preg_match_all($delimiters, $text, $whitespaces);
                $whitespaces = $whitespaces[0];

                $words = preg_split($delimiters, substr($text, 0, $limit));
                $result = [];

                while (!empty($words) || !empty($whitespaces)) {
                    if (!empty($words)) {
                        $result[] = array_shift($words);
                    }
                    if (!empty($whitespaces)) {
                        $result[] = array_shift($whitespaces);
                    }
                }
                // if not found or zero, use zero
                array_pop($words);
                // otherwise use found position
            }
        }
        return 0;
    }

    private function _analyzeText(string $text): array
    {
        $delimiters = '/[ \t\n\r\0\x0B]/';
    }

    /**
     * Performs sanity checks on the given text to validate whether it contains
     *
     * @param string $text     The text to validate.
     * @param int    $limit    The split point.
     * @param bool   $useWords Indicates whether $limit expresses word or
     *                         character count.
     *
     * @return bool
     */
    private function _validateText(string $text, int $limit, bool $useWords): bool
    {
        $sanitizedText = trim($text);
        $length = strlen($sanitizedText);
        $matched = preg_match('/[ \t\n\r\0\x0B]/', $sanitizedText) === 1;

        if ($matched) {

        }
    }

    /**
     * Gets the RelatedSubjectsLabel
     *
     * @return string
     */
    public function getRelatedSubjectsLabel()
    {
        return $this->resolveLabelWithDisplayName(
            'card.knowledge.person.metadata.related.subjects'
        );
    }

    /**
     * Has NotableWork
     *
     * @return bool
     */
    public function hasNotableWork()
    {
        $notableWork = $this->getPerson()->getNotableWork();
        return !is_null($notableWork) && count($notableWork) > 0;
    }

    /**
     * Gets the NotableWorkLabel
     *
     * @return string
     */
    public function getNotableWorkLabel()
    {
        return $this->resolveLabelWithDisplayName(
            'card.knowledge.person.medias'
        );
    }

    /**
     * Gets the MoreNotableWorkLabel
     *
     * @return string
     */
    public function getMoreNotableWorkLabel()
    {
        return $this->resolveLabelWithDisplayName(
            'card.knowledge.person.medias.more'
        );
    }

    /**
     * Gets the NotableWorkSearchLink
     *
     * @param string $template The template
     *
     * @return string
     */
    public function getNotableWorkSearchLink(string $template): string
    {
        $label = $this->getMoreNotableWorkLabel();
        $url = $this->getView()->url('search-results');
        $url = sprintf(
            '%s?lookfor=%s&type=Author', $url,
            urlencode($this->getPerson()->getName())
        );

        return sprintf($template, $url, $label);
    }

    // TODO: Remove temporary notable work once actual data is available
    private static $_notableWork
        = [
            ['label' => 'Werk 01', 'link' => '#'],
            ['label' => 'Werk 02', 'link' => '#'],
            ['label' => 'Werk 03', 'link' => '#'],
            ['label' => 'Werk 04', 'link' => '#'],
            ['label' => 'Werk 05', 'link' => '#'],
            ['label' => 'Werk 06', 'link' => '#'],
            ['label' => 'Werk 07', 'link' => '#'],
            ['label' => 'Werk 08', 'link' => '#'],
            ['label' => 'Werk 09', 'link' => '#'],
            ['label' => 'Werk 10', 'link' => '#'],
        ];

    /**
     * Gets the NotableWork
     *
     * @return array
     */
    public function getNotableWork()
    {
        // TODO: Implement method
        return self::$_notableWork;
    }

    /**
     * Gets the DetailPageLinkLabel
     *
     * @return string
     */
    public function getDetailPageLinkLabel()
    {
        return $this->resolveLabelWithDisplayName(
            'card.knowledge.person.page.link'
        );
    }
}
