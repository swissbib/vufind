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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
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
        return 'person.metadata';
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
            'job'                           => 'getJobInfo',
            'birth'                         => 'getBirthInfo',
            'death'                         => 'getDeathInfo',
            'periodOfActivity'              => 'getPeriodOfActivity',
            'nationality'                   => 'getNationalityInfo',
            'notable.work'                  => 'getNotableWorkList',
            'genre'                         => 'getGenreList',
            'movement'                      => 'getMovementList',
            'names'                         => 'getAlternateName',
            'pseudonym'                     => 'getPseudonym',
            'influencers'                   => 'getInfluencedBy',
            'influenced'                    => 'getInfluenced',
            'awardReceived'                 => 'getAwardReceived',
            'positionHeld'                  => 'getpositionHeld',
            'playedInstrument'              => 'getPlayedInstrument',
            'fieldOfStudy'                  => 'getFieldOfStudy',
            'religion'                      => 'getReligion',
            'nativeLanguage'                => 'getNativeLanguage',
            'languageSpoken'                => 'getLanguageSpoken',
            'realIdentity'                  => 'getRealIdentity',
            'affiliation'                   => 'getAffiliation',
            'relatedCorporateBody'          => 'getRelatedCorporateBody',
            'employer'                      => 'getEmployer',
            'memberOfPoliticalParty'        => 'getMemberOfPoliticalParty',
            'educatedAt'                    => 'getEducatedAt',
            'participantOf'                 => 'getParticipantOf',
            'spouse'                        => 'getSpouse',
            'child'                         => 'getChild',
            'parent'                        => 'getParent',
            'sibling'                       => 'getSibling',
            'professionalRelationship'      => 'getProfessionalRelationship',
            'acquaintanceshipOrFriendship'  => 'getAcquaintanceshipOrFriendship',
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
     * Provides the type to use as search queries.
     *
     * @return string
     */
    public function getSearchType(): string
    {
        return 'Author';
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
        $recordHelper = $this->getView()->record($this->getDriver());
        return $recordHelper->getDisplayName();
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

        if (null !== $birth && null !== $death) {
            $lifetime = sprintf('(%s - %s)', $birth, $death);
        } elseif (null !== $birth) {
            $lifetime = sprintf('(%s - ?)', $birth);
        } elseif (null !== $death) {
            $lifetime = sprintf('(? - %s)', $death);
        }

        return $this->escape($lifetime);
    }

    /**
     * Gets the BirthInfo
     *
     * @param string $separator The separator
     *
     * @return null|string
     */
    public function getBirthInfo(string $separator = ', ')
    {
        return $this->getDateAndPlaceInfo(
            $separator, $this->formatDate($this->getPerson()->getBirthDate()),
            $this->getPerson()->getBirthPlaceDisplayField()
        );
    }

    /**
     * Gets the DeathInfo
     *
     * @param string $separator The separator
     *
     * @return null|string
     */
    public function getDeathInfo(string $separator = ', ')
    {
        return $this->getDateAndPlaceInfo(
            $separator, $this->formatDate($this->getPerson()->getDeathDate()),
            $this->getPerson()->getDeathPlaceDisplayField()
        );
    }

    /**
     * Gets the DateAndPlaceInfo
     *
     * @param string      $separator The separator
     * @param string|null $date      The (optional) date
     * @param array       $place     The (optional) place
     *
     * @return string|null
     */
    protected function getDateAndPlaceInfo(
        string $separator, string $date = null, array $place = null
    ) {
        $place = null === $place ? null : implode($separator, $place);
        $result = null;

        if (null !== $date && null !== $place) {
            $result = sprintf('%s' . $separator . '%s', $date, $place);
        } elseif (null !== $date) {
            $result = sprintf('%s', $date);
        } elseif (null !== $place) {
            $result = sprintf('%s', $place);
        }

        return $this->escape($result);
    }

    /**
     * Gets the PeriodOfActivity
     *
     * @return null|string
     */
    public function getPeriodOfActivity()
    {
        $val = $this->getPerson()->getPeriodOfActivity();
        return $val;
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
        return $this->fieldToString('occupationDisplayField', $delimiter);
    }

    /**
     * Gets the NationalityInfo
     *
     * @param string $delimiter Used to join multiple values to a single string.
     *
     * @return string|null
     */
    public function getNationalityInfo(string $delimiter = ', ')
    {
        return $this->fieldToString('nationalityDisplayField', $delimiter);
    }

    /**
     * Has Abstract
     *
     * @return bool
     */
    public function hasAbstract()
    {
        return null !== $this->getPerson()->getAbstract();
    }

    /**
     * Gets the AbstractInfo
     *
     * @param bool $countWords Indicates whether $splitPoint expresses the number of
     *                         words (true) or characters (false) after which
     *                         truncation has to be performed.
     * @param int  ...$limits  Indicates after how many words (or characters) to
     *                         split. Can be any number of integer values. If not
     *                         specified then the default split point will be at 30
     *                         characters/words.
     *
     * @return \stdClass
     */
    public function getAbstractInfo(bool $countWords = true, ...$limits)
    {
        $info = null;

        if ($this->hasAbstract()) {
            $limits = count($limits) === 0 ? [30] : $limits;

            $abstract = $this->getPerson()->getAbstract();
            // ignore surrounding whitespace at all
            $abstract = trim($abstract);

            $splitter = new Splitter($countWords);
            $info = count($limits) === 1
                ? $splitter->split($abstract, $limits[0])
                : $splitter->splitMultiple($abstract, ...$limits);

            $info->label = $this->getView()->translate('person.metadata.abstract');

            $info->text = $this->escape($info->text);

            if ($info->truncated) {
                $info->overflow = $this->escape($info->overflow);
            }
        }

        return $info;
    }

    /**
     * Gets the RelatedSubjectsLabel
     *
     * @return string
     */
    public function getRelatedSubjectsLabel()
    {
        return $this->resolveLabelWithDisplayName(
            'person.metadata.related.subjects'
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
        return null !== $notableWork && count($notableWork) > 0;
    }

    /**
     * Gets the NotableWorkLabel
     *
     * @return string
     */
    public function getNotableWorkLabel()
    {
        return $this->resolveLabelWithDisplayName(
            'person.medias'
        );
    }

    /**
     * Gets the MoreNotableWorkLabel
     *
     * @return string
     */
    public function getMoreMediasLabel()
    {
        return $this->resolveLabelWithDisplayName(
            'person.medias.more'
        );
    }

    /**
     * Gets the RelatedMediasLink
     *
     * @param string $template The template
     *
     * @return string
     */
    public function getMoreMediasLink(string $template): string
    {
        return $this->getMediaSearchLink(
            $template, $this->getMoreMediasLabel()
        );
    }

    /**
     * Gets the NotableWork
     *
     * @param string $delimiter The notable work item delimiter to join all items
     *                          with.
     *
     * @return string|null
     */
    public function getNotableWorkList(string $delimiter = ', ')
    {
        return $this->fieldToString('notableWorkDisplayField', $delimiter);
    }

    /**
     * Provides the genre as string that can be rendered directly.
     *
     * @param string $delimiter The genre item delimiter to join all items with.
     *
     * @return string|null
     */
    public function getGenreList(string $delimiter = ', ')
    {
        return $this->fieldToString('genreDisplayField', $delimiter);
    }

    /**
     * Provides the movement as string that can be rendered directly.
     *
     * @param string $delimiter The movement item delimiter to join all items with.
     *
     * @return string|null
     */
    public function getMovementList(string $delimiter = ', ')
    {
        return $this->fieldToString('movementDisplayField', $delimiter);
    }

    /**
     * Provides the alternate names for the underlying person.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getAlternateName(string $delimiter = ', ')
    {
        return $this->fieldToString('alternateName', $delimiter);
    }

    /**
     * Provides the pseudonym for the underlying person.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getPseudonym(string $delimiter = ', ')
    {
        return $this->fieldToString('pseudonym', $delimiter);
    }

    /**
     * Provides the spouse value for the underlying person.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getSpouse(string $delimiter = ', ')
    {
        return $this->fieldToString('spouseDisplayField', $delimiter);
    }

    /**
     * Provides the influencers for the underlying person.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getInfluencedBy(string $delimiter = ', ')
    {
        return $this->fieldToString('influencedByDisplayField', $delimiter);
    }

    /**
     * Provides the influenced value for the underlying person.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getInfluenced(string $delimiter = ', ')
    {
        return $this->fieldToString('influencedDisplayField', $delimiter);
    }

    /**
     * Provides the award received.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getAwardReceived(string $delimiter = ', ')
    {
        return $this->fieldToString('awardReceived', $delimiter);
    }

    /**
     * Provides the positionHeld.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getPositionHeld(string $delimiter = ', ')
    {
        return $this->fieldToString('positionHeld', $delimiter);
    }

    /**
     * Provides the fieldOfStudy.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getFieldOfStudy(string $delimiter = ', ')
    {
        return $this->fieldToString('fieldOfStudy', $delimiter);
    }

    /**
     * Provides the playedInstrument.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getPlayedInstrument(string $delimiter = ', ')
    {
        return $this->fieldToString('playedInstrument', $delimiter);
    }

    /**
     * Provides the religion.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getReligion(string $delimiter = ', ')
    {
        return $this->fieldToString('religion', $delimiter);
    }

    /**
     * Provides the nativeLanguage.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getNativeLanguage(string $delimiter = ', ')
    {
        return $this->fieldToString('nativeLanguage', $delimiter);
    }

    /**
     * Provides the languageSpoken.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getLanguageSpoken(string $delimiter = ', ')
    {
        return $this->fieldToString('languageSpoken', $delimiter);
    }

    /**
     * Provides the realIdentity.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getRealIdentity(string $delimiter = ', ')
    {
        return $this->fieldToString('realIdentity', $delimiter);
    }

    /**
     * Provides the affiliation.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getAffiliation(string $delimiter = ', ')
    {
        $val = $this->fieldToString('affiliation', $delimiter);
        return $val;
    }

    /**
     * Provides the relatedCorporateBody.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getRelatedCorporateBody(string $delimiter = ', ')
    {
        $val = $this->fieldToString('relatedCorporateBody', $delimiter);
        return $val;
    }

    /**
     * Provides the employer.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getEmployer(string $delimiter = ', ')
    {
        $val = $this->fieldToString('employer', $delimiter);
        return $val;
    }

    /**
     * Provides the memberOfPoliticalParty.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getMemberOfPoliticalParty(string $delimiter = ', ')
    {
        $val = $this->fieldToString('memberOfPoliticalParty', $delimiter);
        return $val;
    }

    /**
     * Provides the participantOf.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getParticipantOf(string $delimiter = ', ')
    {
        $val = $this->fieldToString('participantOf', $delimiter);
        return $val;
    }

    /**
     * Provides the educatedAt.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getEducatedAt(string $delimiter = ', ')
    {
        $val = $this->fieldToString('educatedAt', $delimiter);
        return $val;
    }

    /**
     * Provides the child value for the underlying person.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getChild(string $delimiter = ', ')
    {
        return $this->fieldToString('childDisplayField', $delimiter);
    }

    /**
     * Provides the parent.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getParent(string $delimiter = ', ')
    {
        $val = $this->fieldToString('parentDisplayField', $delimiter);
        return $val;
    }

    /**
     * Provides the sibling.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getSibling(string $delimiter = ', ')
    {
        $val = $this->fieldToString('siblingDisplayField', $delimiter);
        return $val;
    }

    /**
     * Provides the professionalRelationship.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getProfessionalRelationship(string $delimiter = ', ')
    {
        $val = $this->getPerson()->getProfessionalRelationship();
        return $val;
    }


    /**
     * Provides the acquaintanceshipOrFriendship.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getAcquaintanceshipOrFriendship(string $delimiter = ', ')
    {
        $val = $this->fieldToString('acquaintanceshipOrFriendshipDisplayField', $delimiter);
        return $val;
    }

    /**
     * Gets the DetailPageLinkLabel
     *
     * @return string
     */
    public function getDetailPageLinkLabel()
    {
        return $this->resolveLabelWithDisplayName(
            'person.page.link'
        );
    }

    /**
     * Provides a link to the search for coauthors of the underlying person record.
     *
     * @return string
     */
    public function getCoauthorsSearchLink(): string
    {
        return $this->getPersonSearchLink('coauthor', 'getUniqueID');
    }

    /**
     * Provides a link to the search for authors of the same movement.
     *
     * @return string
     */
    public function getSameMovementSearchLink(): string
    {
        return $this->getPersonSearchLink('samemovement', 'getMovement');
    }

    /**
     * Provides a link to the search for authors of the same genre.
     *
     * @return string
     */
    public function getSameGenreSearchLink(): string
    {
        return $this->getPersonSearchLink('samegenre', 'getGenre');
    }
}
