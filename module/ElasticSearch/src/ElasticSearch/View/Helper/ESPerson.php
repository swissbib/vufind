<?php
/**
 * Created by IntelliJ IDEA.
 * User: edmundmaruhn
 * Date: 20.12.17
 * Time: 08:18
 */

namespace ElasticSearch\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Class ESPerson
 * @package ElasticSearch\View\Helper
 */
class ESPerson extends AbstractHelper
{
    /**
     * @var string
     * Used by the getMetadataList() method to resolve localization values.
     */
    private static $metadataKeyPrefix = 'card.knowledge.person.metadata';

    /**
     * @var array
     * Maps metadata keys on helper methods and css class names for dynamic metadata list construction.
     */
    private static $metadataKeyToMethodMap = [
        'job' => 'getJobInfo',
        'birth' => 'getBirthInfo',
        'death' => 'getDeathInfo',
        'nationality' => 'getNationalityInfo'
    ];

    /**
     * @var \ElasticSearch\VuFind\RecordDriver\ESPerson
     */
    private $person;

    public function getPerson()
    {
        return $this->person;
    }

    public function setPerson(\ElasticSearch\VuFind\RecordDriver\ESPerson $person)
    {
        $this->person = $person;
    }

    /**
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
     * @param $dateFormat
     * @param $separator
     * @return null|string
     */
    public function getBirthInfo(string $dateFormat = 'd.m.Y', string $separator = ', ')
    {
        return $this->getDateAndPlaceInfo($dateFormat, $separator,
            $this->getPerson()->getBirthDate(), $this->getPerson()->getBirthPlaceDisplayField());
    }

    /**
     * @param $dateFormat
     * @param $separator
     * @return null|string
     */
    public function getDeathInfo(string $dateFormat = 'd.m.Y', string $separator = ', ')
    {
        return $this->getDateAndPlaceInfo($dateFormat, $separator,
            $this->getPerson()->getDeathDate(), $this->getPerson()->getDeathPlaceDisplayField());
    }


    /**
     * @param string $dateFormat
     * @param string $separator
     * @param \DateTime|null $date
     * @param array $place
     * @return null|string
     */
    protected function getDateAndPlaceInfo(string $dateFormat, string $separator, \DateTime $date = null, array $place = null)
    {
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
     * @return null
     */
    public function getJobInfo()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getNationalityInfo()
    {
        return null;
    }

    /**
     * A list of metadata information for the current person. Keys which are not found or for which no value exists are
     * filtered and will not be part of the resulting list. The resulting array is in the order of the keys passed in.
     *
     * @param string[] ...$keys
     * Arbitrary sequence of metadata keys.
     *
     * @return array
     * An indexed array of associative arrays with 'label', 'value' and 'cssClass' keys. The label is the localized
     * value of key passed in, the value is the resolved content that belongs to the key passed in and cssClass is the
     * value to be set or added to the element that renders the list entry.
     */
    public function getMetadataList(string ...$keys)
    {
        $metadataList = [];

        foreach ($keys as $key) {
            $entry = $this->getMetadataListEntry($key);

            if (!is_null($entry)) {
                $metadataList[] = $entry;
            }
        }

        return $metadataList;
    }

    /**
     * @param string $key
     * @return array|null
     */
    protected function getMetadataListEntry(string $key)
    {
        $entry = null;

        if (isset(self::$metadataKeyToMethodMap[$key])) {
            $method = self::$metadataKeyToMethodMap[$key];
            $value = $this->{$method}();

            if (!is_null($value)) {
                $translationKey = sprintf('%s.%s', self::$metadataKeyPrefix, $key);
                $entry = [
                    'label' => $this->getView()->translate($translationKey),
                    'value' => $value,
                    'cssClass' => $key
                ];
            }
        }

        return $entry;
    }


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


    public function hasAbstract()
    {
        return !is_null($this->getPerson()->getAbstract());
    }

    public function getAbstractInfo()
    {
        $info = [
            'label' => $this->getView()->translate('card.knowledge.person.metadata.abstract'),
            'text' => '',
            'truncated' => false,
            'overflow' => ''
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

    protected function calculateSplitPoint(string $text, int $truncationWordCount = 30)
    {
        # pattern matches the same way as trim() will do by default
        $words = preg_split('/[ \t\n\r\0\x0B]/', $text);
        $wordCount = 0;
        $processedWords = '';
        $splitPoint = -1;

        foreach ($words as $word) {
            # exclude any whitespace from word count
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
     * @return string
     */
    public function getRelatedSubjectsLabel()
    {
        return $this->resolveLabelWithDisplayName('card.knowledge.person.metadata.related.subjects');
    }


    /**
     * @return bool
     */
    public function hasNotableWork()
    {
        $notableWork = $this->getPerson()->getNotableWork();
        return !is_null($notableWork) && count($notableWork) > 0;
    }

    /**
     * @return string
     */
    public function getNotableWorkLabel()
    {
        return $this->resolveLabelWithDisplayName('card.knowledge.books');
    }

    public function getMoreNotableWorkLabel()
    {
        return $this->resolveLabelWithDisplayName('card.knowledge.books.more');
    }

    public function getNotableWorkSearchLink(string $template): string
    {
        $label = $this->getMoreNotableWorkLabel();
        $url = $this->getView()->url('search-results');
        $url = sprintf('%s?lookfor=%s&type=Author', $url, urlencode($this->getPerson()->getName()));

        return sprintf($template, $url, $label);
    }


    # TODO: Remove temporary notable work once actual data is available
    private static $notableWork = [
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

    public function getNotableWork()
    {
        # TODO: Implement method
        return self::$notableWork;
    }


    /**
     * @return string
     */
    public function getDetailPageLinkLabel()
    {
        return $this->resolveLabelWithDisplayName('card.knowledge.person.page.link');
    }

    /**
     * @param string $template
     * @param string|null $label
     * If not null it is treated as the localization key and will be resolved before it is merged into the template.
     * @return string
     */
    public function getDetailPageLink(string $template, string $label = null): string
    {
        $label = is_null($label)
            ? $this->getDetailPageLinkLabel()
            : $this->getView()->translate($label);

        $segments = ['id' => $this->getPerson()->getUniqueID()];
        $url = $this->getView()->url('page-detail-person', $segments);

        return sprintf($template, $url, $label);
    }

    /**
     * @param string $translationKeyBase
     * @return string
     */
    protected function resolveLabelWithDisplayName(string $translationKeyBase)
    {
        $displayName = $this->getDisplayName();
        $label = null;

        if (is_null($displayName)) {
            $label = $this->getView()->translate(sprintf('%s.no.name', $translationKeyBase));
        } else {
            $label = $this->getView()->translate($translationKeyBase);
            $label = sprintf($label, $displayName);
        }

        return $label;
    }
}