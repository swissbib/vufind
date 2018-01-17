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

/**
 * Class ESPerson
 *
 * @category VuFind
 * @package  ElasticSearch\View\Helper
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
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
     * @return array
     */
    public function getAbstractInfo()
    {
        $info = [
            'label' => $this->getView()->translate(
                'card.knowledge.person.metadata.abstract'
            ), 'text' => '', 'truncated' => false, 'overflow' => ''
        ];

        if ($this->hasAbstract()) {
            $abstract = $this->getPerson()->getAbstract();
            $splitPoint = $this->calculateSplitPoint($abstract);

            if ($splitPoint === -1) {
                $info['text'] = $abstract;
            } else {
                $info['truncated'] = true;
                $info['text'] = substr($abstract, 0, $splitPoint);
                $info['overflow'] = substr($abstract, $splitPoint);
            }
        }

        return $info;
    }

    /**
     * Calculates the SplitPoint
     *
     * @param string $text                The text
     * @param int    $truncationWordCount The truncationWordCount
     *
     * @return int
     */
    protected function calculateSplitPoint(
        string $text, int $truncationWordCount = 30
    ) {
        // pattern matches the same way as trim() will do by default
        $words = preg_split('/[ \t\n\r\0\x0B]/', $text);
        $wordCount = 0;
        $processedWords = '';
        $splitPoint = -1;

        foreach ($words as $word) {
            // exclude any whitespace from word count
            $wordCount += strlen($word) === 0 ? 0 : 1;
            $processedWords .= $word;

            if ($wordCount === $truncationWordCount) {
                $splitPoint = strlen($processedWords);
                break;
            }
        }

        return $splitPoint === strlen($text) ? -1 : $splitPoint;
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

