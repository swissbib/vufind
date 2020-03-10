<?php
/**
 * ESOrganisation.php
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
 * Class ESOrganisation
 *
 * @category VuFind
 * @package  ElasticSearch\View\Helper
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class ESOrganisation extends AbstractHelper
{
    /**
     * Gets the MetadataPrefix
     *
     * @return string
     */
    protected function getMetadataPrefix(): string
    {
        return 'organisation.metadata';
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
            'dateOfEstablishment'                   => 'getDateOfEstablishment',
            'dateOfTermination'                     => 'getDateOfTermination',
            'dateOfConferenceOrEvent'               => 'getDateOfConferenceOrEvent',
            'startDate'                             => 'getStartDate',
            'endDate'                               => 'getEndDate',
            'precedingCorporateBody'                => 'getPrecedingCorporateBody',
            'precedingConferenceOrEvent'            => 'getPrecedingConferenceOrEvent',
            'suceedingCorporateBody'                => 'getSuceedingCorporateBody',
            'suceedingConferenceOrEvent'            => 'getSuceedingConferenceOrEvent',
            'abbreviatedNameForTheCorporateBody'    => 'getAbbreviatedNameForTheCorporateBody',
            'temporaryNameOfTheCorporateBody'       => 'getTemporaryNameOfTheCorporateBody',
            'temporaryNameOfTheConferenceOrEvent'   => 'getTemporaryNameOfTheConferenceOrEvent',
            'biographicalOrHistoricalInformation'   => 'getBiographicalOrHistoricalInformation',
            'definition'                            => 'getDefinition',
            'location'                              => 'getLocation',
            'country'                               => 'getCountry',
            'legalForm'                             => 'getlegalForm',
            'directorManager'                       => 'getDirectorManager',
            'genre'                                 => 'getGenre',
            'hierarchicalSuperiorOfTheCorporateBody'        => 'getHierarchicalSuperiorOfTheCorporateBody',
            'hierarchicalSuperiorOfTheConferenceOrEvent'    => 'getHierarchicalSuperiorOfTheConferenceOrEvent',
            'relatedCorporateBody'                  => 'getRelatedCorporateBody',
            'relatedConferenceOrEvent'              => 'getRelatedConferenceOrEvent',
            'corporateBodyIsMember'                 => 'getCorporateBodyIsMember',
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
        return 'organisation';
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
     * @var \ElasticSearch\VuFind\RecordDriver\ESOrganisation
     */
    private $_organisation;

    /**
     * Gets the Person
     *
     * @return \ElasticSearch\VuFind\RecordDriver\ESOrganisation
     */
    public function getOrganisation()
    {
        return $this->_organisation;
    }

    /**
     * Sets the Organisation
     *
     * @param \ElasticSearch\VuFind\RecordDriver\ESOrganisation $_organisation The organisation
     *
     * @return void
     */
    public function setOrganisation(
        \ElasticSearch\VuFind\RecordDriver\ESOrganisation $_organisation
    ) {
        parent::setDriver($_organisation);
        $this->_organisation = $_organisation;
    }

    /**
     * Gets the DisplayName
     *
     * @return null|string
     */
    public function getDisplayName()
    {
        return $this->getOrganisation()->getName();
    }

    /**
     * Has Abstract
     *
     * @return bool
     */
    public function hasAbstract()
    {
        return null !== $this->getOrganisation()->getAbstract();
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

            $info->label = $this->getView()->translate('organisation.metadata.abstract');

            $info->text = $this->escape($info->text);

            if ($info->truncated) {
                $info->overflow = $this->escape($info->overflow);
            }
        }

        return $info;
    }

    /**
     * Gets the DateOfEstablishment
     *
     * @return null|string
     */
    public function getDateOfEstablishment()
    {
        return $this->getOrganisation()->getDateOfEstablishment();
    }

    /**
     * Gets the DateOfTermination
     *
     * @return null|string
     */
    public function getDateOfTermination()
    {
        return $this->getOrganisation()->getDateOfTermination();
    }

    /**
     * Gets the DateOfConferenceOrEvent
     *
     * @return null|string
     */
    public function getDateOfConferenceOrEvent()
    {
        $ret = $this->getOrganisation()->getDateOfConferenceOrEvent();
        if (null == $ret) {
            $sd = $this->getOrganisation()->getStartDate();
            $ed = $this->getOrganisation()->getEndDate();
            if (null !== $sd && null != $ed) {
                $ret = $sd . '-' . $ed;
            }
        }
        return $ret;
    }

    /**
     * Gets the StartDate
     *
     * @return null|string
     */
    public function getStartDate()
    {
        return $this->getOrganisation()->getStartDate();
    }

    /**
     * Gets the EndDate
     *
     * @return null|string
     */
    public function getEndDate()
    {
        return $this->getOrganisation()->getEndDate();
    }

    /**
     * Gets the PrecedingCorporateBody
     *
     * @return null|string
     */
    public function getPrecedingCorporateBody()
    {
        return $this->getOrganisation()->getPrecedingCorporateBody();
    }

    /**
     * Gets the PrecedingConferenceOrEvent
     *
     * @return null|string
     */
    public function getPrecedingConferenceOrEvent()
    {
        return $this->getOrganisation()->getPrecedingConferenceOrEvent();
    }

    /**
     * Gets the SuceedingCorporateBody
     *
     * @return null|string
     */
    public function getSuceedingCorporateBody()
    {
        return $this->getOrganisation()->getSuceedingCorporateBody();
    }

    /**
     * Gets the ConferenceOrEvent
     *
     * @return null|string
     */
    public function getConferenceOrEvent()
    {
        return $this->getOrganisation()->getConferenceOrEvent();
    }

    /**
     * Gets the RelatedSubjectsLabel
     *
     * @return string
     */
    public function getRelatedSubjectsLabel()
    {
        return $this->resolveLabelWithDisplayName(
            'organisation.metadata.related.subjects'
        );
    }

    /**
     * Has NotableWork
     *
     * @return bool
     */
    public function hasNotableWork()
    {
        $notableWork = $this->getOrganisation()->getNotableWork();
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
            'organisation.medias'
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
            'organisation.medias.more'
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
     * Provides the abbreviated name for the corporate body.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getAbbreviatedNameForTheCorporateBody(string $delimiter = ', ')
    {
        return $this->fieldToString('abbreviatedNameForTheCorporateBody', $delimiter);
    }

    /**
     * Provides the temporary name of the corporate body.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getTemporaryNameOfTheCorporateBody(string $delimiter = ', ')
    {
        return $this->fieldToString('temporaryNameOfTheCorporateBody', $delimiter);
    }

    /**
     * Provides the temporary name of the conference or event.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getTemporaryNameOfTheConferenceOrEvent(string $delimiter = ', ')
    {
        return $this->fieldToString('temporaryNameOfTheConferenceOrEvent', $delimiter);
    }

    /**
     * Provides the biographical or historical information.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getBiographicalOrHistoricalInformation(string $delimiter = ', ')
    {
        return $this->fieldToString('biographicalOrHistoricalInformation', $delimiter);
    }

    /**
     * Provides the definition.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getDefinition(string $delimiter = ', ')
    {
        return $this->fieldToString('definition', $delimiter);
    }

    /**
     * Provides the location.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getLocation(string $delimiter = ', ')
    {
        return $this->getOrganisation()->getLocation();
    }

    /**
     * Provides the country.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getCountry(string $delimiter = ', ')
    {
        return $this->getOrganisation()->getCountry();
    }

    /**
     * Provides the legalForm.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getLegalForm(string $delimiter = ', ')
    {
        return $this->getOrganisation()->getLegalForm();
    }

    /**
     * Provides the legalForm.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getDirectorManager(string $delimiter = ', ')
    {
        return $this->getOrganisation()->getDirectorManager();
    }


    /**
     * Provides the genre.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getGenre(string $delimiter = ', ')
    {
        return $this->getOrganisation()->getGenre();
    }

    /**
     * Provides the alternate names for the underlying organisation.
     *
     * @param string $delimiter The delimiter to join multiple values with.
     *
     * @return string|null
     */
    public function getAlternateNames(string $delimiter = ', ')
    {
        return $this->fieldToString('alternateNames', $delimiter);
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
     * Gets the DetailPageLinkLabel
     *
     * @return string
     */
    public function getDetailPageLinkLabel()
    {
        return $this->resolveLabelWithDisplayName(
            'organisation.page.link'
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
