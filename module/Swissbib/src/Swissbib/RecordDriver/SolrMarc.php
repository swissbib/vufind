<?php
/**
 * Swissbib / VuFind swissbib enhancements for MARC records in Solr
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  RecordDriver
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @author   Oliver Schihin <oliver.schihin@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:record_drivers Wiki
 * @link     http://www.swissbib.org Project Wiki
 */
namespace Swissbib\RecordDriver;

use Laminas\I18n\Translator\TranslatorInterface as Translator;
use Swissbib\RecordDriver\Helper\Holdings as HoldingsHelper;
use VuFind\Log\Logger;
use VuFind\RecordDriver\SolrMarc as VuFindSolrMarc;

/**
 * SolrDefaultAdapter
 *
 * @category Swissbib_VuFind
 * @package  RecordDriver
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class SolrMarc extends VuFindSolrMarc implements SwissbibRecordDriver
{
    /**
     * HoldingsHelper
     *
     * @var HoldingsHelper
     */
    protected $holdingsHelper;

    /**
     * AvailabilityHelper
     *
     * @var AvailabilityHelper
     */
    protected $availabilityHelper;

    /**
     * HoldingsHelper
     *
     * @var HoldingsHelper
     */
    protected $solrDefaultAdapter;

    /**
     * Change behaviour if getFormats() to return openUrl compatible formats
     *
     * @var Boolean
     */
    protected $useOpenUrlFormats = false;

    /**
     * UseMostSpecificFormat
     *
     * @var Boolean
     */
    protected $useMostSpecificFormat = false;

    /**
     * Lookup table for marc-unions VS alephLibrary
     *
     * @var Array
     */
    protected $libraryNetworkLookup = null;

    /**
     * Logger
     *
     * @var Logger
     */
    protected $logger;

    /**
     * Used also for field 100 _ means repeatable
     *
     * @var Array
     */
    protected $personFieldMap = [
        'a' => 'name',
        'b' => 'numeration',
        '_c' => 'titles', // R
        'd' => 'dates',
        '_e' => 'relator', // R
        'f' => 'date_of_work',
        'g' => 'misc',
        'i' => 'relationship',
        '_k' => 'form_subheading', //R
        'l' => 'language',
        '_m' => 'music_performance_medium',
        '_n' => 'parts_number',
        'o' => 'arranged_music',
        '_p' => 'parts_name',
        'q' => 'fullername',
        'r' => 'music_key',
        's' => 'version',
        'D' => 'forename',
        't' => 'title_of_work',
        '4' => 'relator_code',
        '5' => 'institution',
        '_8' => 'extras',
        '9' => 'unknownNumber',
        'P' => 'originField', //swissbib specific subfield, indicates
                              //original tag of park field. Only in use for field 950
    ];

    /**
     * Used for field 110/710 _ is repeatable
     *
     * @var Array
     */
    protected $corporationFieldMap = [
        'a' => 'name',
        '_b' => 'unit',
        'c' => 'meeting_location',
        '_d' => 'meeting_date',
        '_e' => 'relator',
        'f' => 'date_of_work',
        'g' => 'misc',
        'h' => 'medium',
        'i' => 'relationship',
        '_k' => 'form_subheading',
        'l' => 'language',
        '_m' => 'music_performance_medium',
        '_n' => 'parts_number',
        'o' => 'arranged_music',
        '_p' => 'parts_name',
        'r' => 'music_key',
        's' => 'version',
        't' => 'title_of_work',
        'u' => 'affiliation',
        'x' => 'issn',
        '3' => 'materials_specified',
        '4' => 'relator_code',
        '5' => 'institution',
        '_8' => 'label',
        'P' => 'originField', // swissbib specific subfield, indicates
                              //original tag of park field. Only in use for field 950
    ];

    /**
     * MultiValuedFRBRField
     *
     * @var Boolean
     */
    protected $multiValuedFRBRField = true;

    /**
     * List of all Elements of the description, to figure out whether
     * to show tab or not
     *
     * @var Array
     */
    protected $partsOfDescription = [
        'ISBNs',
        'ISSNs',
        'ISMNs',
        'DOIs',
        'URNs',
        'AllSubjectVocabularies',
        'Series',
        'CollectionTitle',
        'ArchivalVolumeTitle',
        'AltTitle',
        'NewerTitles',
        'PreviousTitles',
        'GeneralNotes',
        'DissertationNotes',
        'BibliographyNotes',
        'PublicationFrequency',
        'AccessRestrictions',
        'ProductionCredits',
        'OriginalTitle',
        'PerformerNote',
        'Awards',
        'CitationNotes',
        'ContResourceDates',
        'OriginalVersionNotes',
        'CopyNotes',
        'SystemDetails',
        'Publications',
        'Exhibitions',
        'RelationshipNotes',
        'HierarchicalPlaceNames',
        'RelatedEntries',
    ];

    /**
     * Constructor
     *
     * @param \Laminas\Config\Config $mainConfig           VuFind main configuration
     *                                                     (omit for built-in defaults)
     * @param \Laminas\Config\Config $recordConfig         Record-specific configu-
     *                                                     ration file
     *                                                     (omit to use $mainConfig as
     *                                                     $recordConfig)
     * @param \Laminas\Config\Config $searchSettings       Search-specific configu-
     *                                                     ration file
     * @param HoldingsHelper         $holdingsHelper       Holdings helper
     * @param HoldingsHelper         $solrDefaultAdapter   SOLR adapter
     * @param AvailabilityHelper     $availabilityHelper   Availability helper
     * @param Array                  $libraryNetworkLookup lookuptable for unions
     * @param Logger                 $logger               Logger
     */
    public function __construct($mainConfig = null, $recordConfig = null,
        $searchSettings = null, $holdingsHelper = null, $solrDefaultAdapter = null,
        $availabilityHelper = null, $libraryNetworkLookup = null, $logger = null
    ) {
        parent::__construct($mainConfig, $recordConfig, $searchSettings);

        $this->multiValuedFRBRField
            = isset($searchSettings->General->multiValuedFRBRField) ?
            $searchSettings->General->multiValuedFRBRField : true;

        $this->solrDefaultAdapter = $solrDefaultAdapter;
        $this->holdingsHelper = $holdingsHelper;
        $this->availabilityHelper = $availabilityHelper;

        $this->logger = $logger;

        if ($libraryNetworkLookup !== null) {
            foreach ($libraryNetworkLookup as $key => $value) {
                $values = explode(',', $value);
                $libraryNetworkLookup[$key] = end($values);
            }
            $this->libraryNetworkLookup = $libraryNetworkLookup;
        }
    }

    /**
     * Wrapper for getOpenURL()
     * Set flag to get special values from getFormats()
     *
     * @param boolean $overrideSupportsOpenUrl OverrideSupportsOpenUrl
     *
     * @see getFormats()
     *
     * @return String
     */
    public function getOpenURL($overrideSupportsOpenUrl = false)
    {
        // get the coinsID from config.ini or default to swissbib.ch
        $coinsID = $this->mainConfig->OpenURL->rfr_id;
        if (empty($coinsID)) {
            $coinsID = 'swissbib.ch';
        }

        // Get a representative publication date, using the view helper:
        $pubDate = $this->getHumanReadablePublicationDates();

        // Start an array of OpenURL parameters:
        $params = [
            'ctx_ver' => 'Z39.88-2004',
            'ctx_enc' => 'info:ofi/enc:UTF-8',
            'rfr_id' => "info:sid/{$coinsID}:generator",
            'rft.title' => $this->getTitle(),
            'rft.date' => $pubDate[0] ?? null,
        ];

        $this->useOpenUrlFormats = true;
        $format = $this->getOpenURLFormat();
        switch ($format) {
        case 'Book':
            $params['rft_val_fmt'] = 'info:ofi/fmt:kev:mtx:book';
            $params['rft.genre'] = 'book';
            $params['rft.btitle'] = $params['rft.title'];
            $series = $this->getSeries();
            if (count($series) > 0) {
                // Handle both possible return formats of getSeries:
                $params['rft.series'] = is_array($series[0]) ?
                    $series[0]['name'] : $series[0];
            }
            $authors[] = $this->getPrimaryAuthor();
            $addauthors = $this->getSecondaryAuthors();
            if (!empty($addauthors)) {
                foreach ($addauthors as $addauthor) {
                    $authors[] = $addauthor;
                }
            }
            if (!empty($authors)) {
                foreach ($authors as $author) {
                    $params['rft.au'][] = $author;
                }
            }
            $corporations = $this->getCorporationNames(true);
            if (!empty($corporations)) {
                foreach ($corporations as $corporation) {
                    $params['rft.aucorp '][] = $corporation;
                }
            }

            $publications = $this->getPublicationDetails();
            foreach ($publications as $field) {
                $pubPlace = $field->getPlace();
                $pubName = $field->getName();

                if (!empty($pubPlace)) {
                    $params['rft.place'] = preg_replace('/ : .*$/', '', $pubPlace);
                }
                if (!empty($pubName)) {
                    $params['rft.pub'] = preg_replace('/^.* : /', '', $pubName);
                }
            }

            $params['rft.edition'] = $this->getEdition();
            $params['rft.isbn'] = $this->getCleanISBN();
            break;
        case 'Article':
            $params['rft_val_fmt'] = 'info:ofi/fmt:kev:mtx:journal';
            $params['rft.genre'] = 'article';
            $params['rft.issn'] = $this->getCleanISSN();
            // an article may have also an ISBN:
            $params['rft.isbn'] = $this->getCleanISBN();
            $params['rft.volume'] = $this->getContainerVolume();
            $params['rft.issue'] = $this->getContainerIssue();
            $params['rft.spage'] = $this->getContainerStartPage();
            // unset default title -- we only want jtitle/atitle here:
            unset($params['rft.title']);
            $jtitle = $this->getContainerTitle();
            if (empty($jtitle)) {
                $hostItems = $this->tryMethod('getHostItemEntry');
                if (!empty($hostItems)) {
                    foreach ($hostItems as $hostItem) {
                        $params['rft.jtitle'] = $hostItem['title'];
                    }
                }
            }
            $params['rft.atitle'] = $this->getTitle();

            $authors[] = $this->getPrimaryAuthor();
            $addauthors = $this->getSecondaryAuthors();
            if (!empty($addauthors)) {
                foreach ($addauthors as $addauthor) {
                    $authors[] = $addauthor;
                }
            }
            if (!empty($authors)) {
                foreach ($authors as $author) {
                    $params['rft.au'][] = $author;
                }
            }
            $params['rft.format'] = $format;
            $langs = $this->getLanguages();
            if (count($langs) > 0) {
                $params['rft.language'] = $langs[0];
            }
            break;
        /* case 'Journal':
            /* This is probably the most technically correct way to represent
            * a journal run as an OpenURL; however, it doesn't work well with
            * Zotero, so it is currently commented out -- instead, we just add
            * some extra fields and then drop through to the default case.
            $params['rft_val_fmt'] = 'info:ofi/fmt:kev:mtx:journal';
            $params['rft.genre'] = 'journal';
            $params['rft.jtitle'] = $params['rft.title'];
            $params['rft.issn'] = $this->getCleanISSN();
            $params['rft.au'] = $this->getPrimaryAuthor();
            break;
            */
            /* $params['rft_val_fmt'] = 'info:ofi/fmt:kev:mtx:journal';
            $params['rft.genre'] = 'journal';
            $params['rft.issn'] = $this->getCleanISSN();
            // Including a date in a title-level Journal OpenURL may be too
            // limiting -- in some link resolvers, it may cause the exclusion
            // of databases if they do not cover the exact date provided!
            //unset($params['rft.date']);
            // If we're working with the SFX resolver, we should add a
            // special parameter to ensure that electronic holdings links
            // are shown even though no specific date or issue is specified:
            //if (isset($this->mainConfig->OpenURL->resolver)
            // && strtolower($this->mainConfig->OpenURL->resolver) == 'sfx'
            //) {
            // $params['sfx.ignore_date_threshold'] = 1;
            //};                }
            break;*/
        default:
            $params['rft_val_fmt'] = 'info:ofi/fmt:kev:mtx:dc';

            $authors[] = $this->getPrimaryAuthor();
            $addauthors = $this->getSecondaryAuthors();
            if (!empty($addauthors)) {
                foreach ($addauthors as $addauthor) {
                    $authors[] = $addauthor;
                }
            }
            if (!empty($authors)) {
                foreach ($authors as $author) {
                    $params['rft.au'][] = $author;
                }
            }

            $publications = $this->getPublicationDetails();
            foreach ($publications as $field) {
                $pubPlace = $field->getPlace();
                $pubName = $field->getName();

                if (!empty($pubPlace)) {
                    $params['rft.place'] = preg_replace('/ : .*$/', '', $pubPlace);
                }
                if (!empty($pubName)) {
                    $params['rft.pub'] = preg_replace('/^.* : /', '', $pubName);
                }
            }

            $params['rft.format'] = $format;
            $langs = $this->getLanguages();
            if (count($langs) > 0) {
                $params['rft.language'] = $langs[0];
            }
            break;
        }

        // Assemble the URL:
        $parts = [];
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $content) {
                    $parts[] = $key . '=' . urlencode($content);
                }
            } else {
                $parts[] = $key . '=' . urlencode($value);
            }
        }
        return implode('&', $parts);
    }

    /**
     * Get formats. By default, get translated values
     * If flag useOpenUrlFormats in class is set, get prepared formats for openUrl
     *
     * @return String[]
     */
    public function getFormats()
    {
        if ($this->useOpenUrlFormats) {
            return $this->getFormatsOpenUrl();
        } elseif ($this->useMostSpecificFormat) {
            return $this->getMostSpecificFormat();
        } else {
            return $this->getFormatsTranslated();
        }
    }

    /**
     * Return an array of associative URL arrays with one or more of the following
     * keys:
     *
     * <li>
     *   <ul>desc: URL description text to display (optional)</ul>
     *   <ul>url: fully-formed URL (required if 'route' is absent)</ul>
     *   <ul>route: VuFind route to build URL with (required if 'url' is absent)</ul>
     *   <ul>routeParams: Parameters for route (optional)</ul>
     *   <ul>queryString: Query params to append after building route (optional)</ul>
     * </li>
     *
     * @return array
     */
    public function getURLs()
    {
        $retVal = [];

        // Which fields/subfields should we check for URLs?
        $fieldsToCheck = [
            '856' => ['u', '3', 'z'], // Standard URL
            '956' => ['u', 'y'], // Standard URL
            //'555' => array('a')         // Cumulative index/finding aids
        ];

        foreach ($fieldsToCheck as $field => $subfields) {
            $urls = $this->getMarcRecord()->getFields($field);
            if ($urls) {
                foreach ($urls as $url) {
                    // Is there an address in the current field?
                    $address = $url->getSubfield('u');
                    if ($address) {
                        $address = $address->getData();

                        $tSubField = end($subfields);

                        $descSubField = $url->getSubfield($tSubField);

                        // if no content in subfield z/y, try subfield 3
                        if (!$descSubField) {
                            $descSubField = $url->getSubfield('3');
                        }

                        $desc = $address;
                        if ($descSubField) {
                            $desc = $descSubField->getData();
                        }
                        // Is there a description?  If not, just use the URL itself.

                        $retVal[] = ['url' => $address, 'desc' => $desc];
                    }
                }
            }
        }

        return $retVal;
    }

    /**
     * Return an array of associative URL arrays with one or more of the following
     * keys:
     *
     * <li>
     *   <ul>desc: URL description text to display (optional)</ul>
     *   <ul>url: fully-formed URL (required if 'route' is absent)</ul>
     *   <ul>route: VuFind route to build URL with (required if 'url' is absent)</ul>
     *   <ul>routeParams: Parameters for route (optional)</ul>
     *   <ul>queryString: Query params to append after building route (optional)</ul>
     * </li>
     * the URLs consider restrictions by unions and tags defined by the client
     * of this functionality
     *
     * @param array $globalunions GlobalUnions
     * @param array $tags         Tags
     *
     * @return array
     */
    public function getExtendedURLs($globalunions = [],
        $tags = []
    ) {
        $retVal = [];

        // Which fields/subfields should we check for URLs?
        $fieldsToCheck = [
            '856' => ['u', '3'], // Standard URL
            '956' => ['u', 'y','B'], // Standard URL
            //'555' => array('a')         // Cumulative index/finding aids
        ];

        $tFieldsToCheck = [];
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                if (strcmp($tag, '856') == 0) {
                    $tFieldsToCheck['856'] = ['u','3'];
                } elseif (strcmp($tag, '956') == 0) {
                    $tFieldsToCheck['956'] = ['u','y','B'];
                }
            }
        }

        if (!empty($tFieldsToCheck)) {
            $fieldsToCheck = $tFieldsToCheck;
        }

        foreach ($fieldsToCheck as $field => $subfields) {
            $urls = $this->getMarcRecord()->getFields($field);
            if ($urls) {
                foreach ($urls as $url) {
                    // Is there an address in the current field?
                    $address = $url->getSubfield('u');
                    if ($address) {
                        $address = $address->getData();

                        $tagDataField = $url->getTag();

                        if (strcmp($tagDataField, '856') == 0) {
                            $descSubField = $url->getSubField('3');
                        } else {
                            //it has to be 956 -> now we could have a
                            // union restriction

                            $union = $url->getSubfield('B')->getData();
                            //limited to specific networks?
                            if (sizeof($globalunions) > 0
                                && !in_array($union, $globalunions)
                            ) {
                                continue;
                            }

                            $descSubField = $url->getSubField('y');
                        }

                        $desc = $address;
                        if ($descSubField) {
                            $desc = $descSubField->getData();
                        }
                        // Is there a description?  If not, just use the URL itself.

                        $retVal[] = ['url' => $address, 'desc' => $desc];
                    }
                }
            }
        }

        return $retVal;
    }

    /**
     * Get LocalValues
     *
     * @param array $localunions Localunions
     * @param array $localtags   Localtags
     * @param array $indicators  Indicators
     * @param array $subfields   Subfields
     *
     * @return array
     */
    public function getLocalValues($localunions = [],
        $localtags = [],
        $indicators = [],
        $subfields = []
    ) {
        $retValues = [];

        $localValues = $this->getMarcRecord()->getFields('950');
        if ($localValues) {
            foreach ($localValues as $localValue) {
                // what are tags and source code?

                $ts = $localValue->getSubfield('B');
                if (empty($ts)) {
                    continue;
                }

                $union = $localValue->getSubfield('B')->getData();
                //limited to specific networks?
                if (sizeof($localunions) > 0 && !in_array($union, $localunions)) {
                    continue;
                }

                $ts = $localValue->getSubfield('P');
                if (empty($ts)) {
                    continue;
                }

                $tLocalTagInSubField = $localValue->getSubfield('P');
                $tLocalTagValue = $tLocalTagInSubField->getData();
                //not the requested localTag?
                if (sizeof($localtags) > 0
                    && !in_array($tLocalTagValue, $localtags)
                ) {
                    continue;
                }

                //any Indicator rules?
                if (sizeof($indicators) > 0) {
                    for ($i = 0; $i <= 1; $i++) {
                        $ts = $localValue->getSubfield('E');
                        if (empty($ts)) {
                            continue 2;
                        }

                        $t = $localValue->getSubfield('E')->getData();
                        $indicator = substr($t, $i, 1);

                        if ($indicator !== $indicators[$i]) {
                            continue 2;
                        }
                    }
                }

                $tlocalTagReturn = [];
                $tSubfields = [];
                $tlocalTagReturn['localtag'] = $tLocalTagValue;
                $tlocalTagReturn['localunion'] = $union;

                if (sizeof($subfields) > 0) {
                    foreach ($subfields as $subfield) {
                        $tLocalValueSubField = $localValue->getSubField($subfield);
                        //is there a value for the requested subfield?
                        if (!empty($tLocalValueSubField)) {
                            $tCode = $tLocalValueSubField->getCode();
                            $tSubfields[$tCode] = $tLocalValueSubField->getData();
                        }
                    }
                } else {
                    foreach ($localValue->getSubFields() as $subfield) {
                        $tCode = $subfield->getCode();
                        $tSubfields[$tCode] = $subfield->getData();
                    }
                }

                $tlocalTagReturn['subfields'] = $tSubfields;
                $retValues[] = $tlocalTagReturn;
            }
        }
        return $retValues;
    }

    /**
     * Returns one of two things:
     * a full URL to a thumbnail preview of the record
     * if an image is available in an external system; an array of parameters to
     * send to VuFind's internal cover generator if no fixed URL exists
     *
     * Extended from SolrDefault
     *
     * See documentation to swissbib-specific functions:
     * http://www.swissbib.org/wiki/index.php?title=Staff:Thumbnail_locations
     *
     * @param string $size Size of thumbnail (small, medium or large -- small is
     *                     default).
     *
     * @return string|array
     */
    public function getThumbnail($size = 'small')
    {
        $useMostSpecificState = $this->getUseMostSpecificFormat();
        $format = $this->getMostSpecificFormat();
        $this->setUseMostSpecificFormat($useMostSpecificState);
        if ($isbn = $this->getCleanISBN()) {
            return  isset($format) &&
            is_array($format) && count($format)
            >= 1 ? ['isn' => $isbn, 'size' => $size, 'format' =>
                $format[0], 'contenttype' => $format[0]] : ['isn' => $isbn,
                'size' => $size];
        } elseif ($path = $this->getThumbnail956()) {
            return $path;
        } elseif ($path = $this->getThumbnail856()) {
            return $path;
        } elseif ($path = $this->getThumbnailErara()) {
            return $path;
        } elseif ($path = $this->getThumbnailEmanuscripta()) {
            return $path;
        } elseif (isset($format) && is_array($format) && count($format) >= 1) {
            return ['size' => $size, 'format' => $format[0],
                'contenttype' => $format[0]];
        } else {
            return [];
        }
    }

    /**
     * Get thumbnail link from 956, cases I and II (see wiki documentation)
     *
     * @return string
     */
    protected function getThumbnail956()
    {
        $thumbnailURL = null;

        $fields = $this->getMarcSubFieldMaps(
            956, [
                'B' => 'union',
                'C' => 'ADM',
                'D' => 'library',
                'a' => 'institution',
                'u' => 'URL',
                'd' => 'directory',
                'f' => 'filename',
                'q' => 'type',
                'x' => 'usage',
                'y' => 'description',
            ]
        );
        if (!$fields) {
            return [];
        }

        foreach ($fields as $field) {
            if ($field['union'] === 'IDSBB' || $field['union'] === 'IDSLU') {
                if (preg_match(
                    '/Vorschau zum Bild|Porträt|Bild$/', $field['description']
                )
                ) {
                    $thumbnailURL = 'https://externalservices.swissbib.ch/' .
                        'services/ImageTransformer?imagePath=' . $field['URL'] .
                        '&scale=1&reqServicename=ImageTransformer';
                }
                //} elseif ($field['union'] === 'SGBN'
                //  && mb_strtoupper($field['type']) === 'JPG'
                // ) {
                // $dirpath = preg_replace('/^.*sgb50/', '', $field['directory']);
                // $dirpath = empty($dirpath) ? $dirpath : substr($dirpath, 1) . '/';
                // $thumbnailURL = 'https://externalservices.swissbib.ch/services/' .
                // 'ImageTransformer?imagePath=http://aleph.sg.ch/adam/' .
                // $dirpath . $field['filename'] . '&scale=1';
            } elseif ($field['union'] === 'BGR'
                && mb_strtoupper($field['type']) === 'JPG'
            ) {
                $dirpath = substr($field['directory'], 29);
                $thumbnailURL = 'https://externalservices.swissbib.ch/services/' .
                    'ImageTransformer?imagePath=http://aleph.gr.ch/adam/' .
                    $dirpath . '/' . $field['filename'] . '&scale=1';
            } elseif (isset($field['ADM']) && $field['ADM'] === 'ZAD50') {
                if (array_key_exists('directory', $field)
                    && preg_match('/^.*thumbnail/', $field['directory'])
                ) {
                    $dirpath = preg_replace(
                        '/^.*thumbnail/', '', $field['directory']
                    );
                    $dirpath
                        = empty($dirpath) ? $dirpath : substr($dirpath, 1) . '/';
                    $thumbnailURL = 'https://externalservices.swissbib.ch/' .
                        'services/ImageTransformer?imagePath=http://opac.nebis.ch/' .
                        'thumb_zb/' . $dirpath . $field['filename'] . '&scale=1';
                }
            } elseif (isset($field['institution'])
                && $field['institution'] === 'E45' && $field['usage'] === 'VIEW'
            ) {
                $thumbnailURL = 'https://externalservices.swissbib.ch/services/' .
                    'ImageTransformer?imagePath=' . $field['URL'] .
                    '&scale=1&reqServicename=ImageTransformer';
            } elseif (isset($field['union'])
                && $field['union'] === 'ECOD'
                && $field['usage'] === 'THUMBNAIL'
            ) {
                $thumbnailURL = 'https://externalservices.swissbib.ch/services/' .
                    'ImageTransformer?imagePath=' . $field['URL'] .
                    '&scale=1&reqServicename=ImageTransformer';
            } elseif (isset($field['union'])
                && $field['union'] === 'CHARCH'
                && $field['usage'] === 'THUMBNAIL'
            ) {
                //thumbnail of CHARCH is already https-service, therefore no wrapper
                $thumbnailURL = $field['URL'];
            }
        }

        return $thumbnailURL;
    }

    /**
     * Get thumbnail link from 856 (see wiki documentation)
     *
     * @return string
     */
    protected function getThumbnail856()
    {
        $fields = $this->get950();
        if (!$fields) {
            return [];
        }
        foreach ($fields as $field) {
            if (!isset($field['union'])) {
                continue;
            }

            if ($field['union'] === 'RERO' && $field['tag'] === '856') {
                if (isset($field['sf_u'])
                    && preg_match('/^.*v_bcu\/media\/images/', $field['sf_u']) == 1
                ) {
                    return 'https://externalservices.swissbib.ch/services/' .
                    'ImageTransformer?imagePath=' . $field['sf_u'] . '&scale=1';
                } elseif (isset($field['sf_u']) && preg_match(
                    '/^.*bibliotheques\/iconographie/', $field['sf_u']
                ) == 1
                ) {
                    return 'https://externalservices.swissbib.ch/services/' .
                    'ImageTransformer?imagePath='
                    /*return 'https://externalservices.swissbib.ch/services/
                    ProtocolWrapper?targetURL='*/
                    . $field['sf_u'] . '&scale=1';
                }
            } elseif ($field['union'] === 'CCSA' && $field['tag'] === '856') {
                $URL_thumb = preg_replace(
                    '/hi-res.cgi/', 'get_thumb.cgi', $field['sf_u']
                );
                return 'https://externalservices.swissbib.ch/services/' .
                'ImageTransformer?imagePath=' . $URL_thumb . '&scale=1';
            }
        }
    }

    /**
     * Get thumbnail link from e-rara-DOI (see wiki documentation)
     *
     * @return string
     */
    protected function getThumbnailErara()
    {
        $field = $this->getDOIs();
        if (!empty($field) && preg_match('/^.*e-rara/', $field['0'])) {
            $URL_thumb = 'http://www.e-rara.ch/titlepage/doi/'
                . $field['0']
                . '/128';

            return 'https://externalservices.swissbib.ch/services/' .
                'ImageTransformer?imagePath=' . $URL_thumb . '&scale=1';
        }

        return false;
    }

    /**
     * Get thumbnail link from e-manuscripta-DOI (see wiki documentation)
     *
     * @return string
     */
    protected function getThumbnailEmanuscripta()
    {
        $field = $this->getDOIs();
        if (!empty($field) && preg_match('/^.*e-manuscripta/', $field['0'])) {
            $URL_thumb = 'http://www.e-manuscripta.ch/titlepage/doi/'
                . $field['0']
                . '/128';
            return 'https://externalservices.swissbib.ch/services/'
            . 'ImageTransformer?imagePath='
            . $URL_thumb
            . '&scale=1';
        }
        return false;
    }

    /**
     * Get fully mapped field 956 (local links, ADAM objects)
     *
     * @return array
     */
    protected function get956()
    {
        return $this->getMarcSubFieldMap(
            956, [
            'B' => 'union',
            'C' => 'ADM',
            'D' => 'library',
            'a' => 'institution',
            'u' => 'URL',
            'd' => 'directory',
            'f' => 'filename',
            'q' => 'type',
            'x' => 'usage',
            'y' => 'description',
            ]
        );
    }

    /**
     * Get partially mapped field 950 (Parking-field)
     *
     * @return array
     */
    protected function get950()
    {
        return $this->getMarcSubFieldMaps(
            950, [
            'B' => 'union',
            'P' => 'tag',
            'a' => 'sf_a',
            'u' => 'sf_u',
            'z' => 'sf_z',
            '3' => "sf_3",
            ]
        );
    }

    /**
     * Get translated formats
     *
     * @return String[]
     */
    protected function getFormatsTranslated()
    {
        $formats = $this->getFormatsRaw();
        $translator = $this->getTranslator();

        foreach ($formats as $index => $format) {
            $formats[$index] = $translator->translate($format);
        }

        return $formats;
    }

    /**
     * Get ISMN (International Standard Music Number)
     *
     * @return array
     */
    public function getISMNs()
    {
        return isset($this->fields['ismn_isn_mv'])
            && is_array($this->fields['ismn_isn_mv']) ?
            $this->fields['ismn_isn_mv'] : [];
    }

    /**
     * Get DOI (Digital Object Identifier)
     *
     * @return array
     */
    public function getDOIs()
    {
        return isset($this->fields['doi_isn_mv'])
            && is_array($this->fields['doi_isn_mv']) ?
            $this->fields['doi_isn_mv'] : [];
    }

    /**
     * Get URN (Uniform Resource Name)
     *
     * @return array
     */
    public function getURNs()
    {
        return isset($this->fields['urn_isn_mv'])
            && is_array($this->fields['urn_isn_mv']) ?
            $this->fields['urn_isn_mv'] : [];
    }

    /**
     * Get formats modified to work with openURL
     * Formats: Book (is default), Journal, Article
     *
     * @return String[]
     */
    public function getFormatsOpenUrl()
    {
        $formats = $this->getFormatsRaw();
        $found = false;
        $mapping = [
            'XK01' => 'Article',
            'XK02' => 'Book',
            'XR0300' => 'Journal',
        ];

        // Check each format for all patterns
        foreach ($formats as $rawFormat) {
            foreach ($mapping as $pattern => $targetFormat) {
                // Test for begin of string
                if (stristr($rawFormat, $pattern)) {
                    $formats[] = $targetFormat;
                    $found = true;
                    break 2; // Stop both loops
                }
            }
        }

        // Fallback: Book
        if ($found == false) {
            $formats[] = 'Book';
        }

        return $formats;
    }

    /**
     * Get raw formats as provided by the basic driver
     * Wrap for getFormats() because it's overwritten in this driver
     *
     * @return String[]
     */
    public function getFormatsRaw()
    {
        return parent::getFormats();
    }

    /**
     * Returns as array to use same template with foreach as normally
     *
     * @return array
     */
    public function getMostSpecificFormat()
    {
        if (isset($this->fields["format_str_mv"])) {
            $formatsRaw = $this->fields["format_str_mv"];
            natsort($formatsRaw);
            $formatsRaw = array_values(array_reverse($formatsRaw));

            return [$formatsRaw[0]];
        } else {
            return [];
        }
    }

    /**
     * Get years and datetype from field 008
     * format in calling functions for display and/or output
     *
     * @return Array
     */
    public function getPublicationDates()
    {
        // Get field 008 fixed field code
        $code = $this->getMarcRecord()->getField('008')->getData();

        // Get parts
        $dateType = substr($code, 6, 1);
        $year1 = substr($code, 7, 4);
        $year2 = substr($code, 11, 4);

        return [$dateType, $year1, $year2];
    }

    /**
     * Get the main authors of the record.
     *
     * @return array
     */
    public function getPrimaryAuthors()
    {
        $primaryAuthors = $this->getPrimaryAuthor();
        if (empty($primaryAuthors)) {
            return [];
        }
        if (!is_array($primaryAuthors)) {
            $primaryAuthors = [$primaryAuthors];
        }
        return $primaryAuthors;
    }

    /**
     * Get primary author
     *
     * @param Boolean $asString AsString
     *
     * @return Array|String
     */
    public function getPrimaryAuthor($asString = true)
    {
        $data = $this->getMarcSubFieldMap(100, $this->personFieldMap);

        if ($asString) {
            $name = $data['name'] ?? '';
            $name .= isset($data['forename']) ? ', ' . $data['forename'] : '';
            $name .= isset($data['numeration']) ? ' ' . $data['numeration'] : '';
            $name .= isset($data['1titles']) ? ', ' . $data['1titles'] : '';

            return trim($name);
        }

        return $data;
    }

    /**
     * Get list of secondary authors data
     *
     * @param Boolean $asString AsString
     *
     * @return Array[]
     */
    public function getSecondaryAuthors($asString = true)
    {
        $authors = $this->getMarcSubFieldMaps(700, $this->personFieldMap);

        if ($asString) {
            $stringAuthors = [];

            foreach ($authors as $author) {
                if ($author['@ind2'] !== '2') {
                    $name = $author['name'] ?? '';
                    $name .=
                        isset($author['forename']) ? ', ' . $author['forename'] : '';
                    $name .=
                        isset($author['numeration']) ?
                            ' ' . $author['numeration'] : '';
                    $name .=
                        isset($author['1titles']) ? ', ' . $author['1titles'] : '';
                    $stringAuthors[] = trim($name);
                }
            }

            $authors = $stringAuthors;
        }

        return $authors;
    }

    /**
     * Get the main corporate author (if any) for the record.
     *
     * @return string
     */
    public function getCorporateAuthors()
    {
        $tempAuthor = $this->getCorporateAuthor();
        return empty($tempAuthor) ?
            [] : [$this->getCorporateAuthor()];
    }

    /**
     * Get the main corporate author (if any) for the record.
     *
     * @return string
     */
    public function getCorporateAuthor()
    {
        // Try 110 first -- if none found, try 710 next.
        $main = $this->getFirstFieldValue('110', ['a', 'b']);
        if (!empty($main)) {
            return $main;
        }
        return $this->getFirstFieldValue('710', ['a', 'b']);
    }

    /**
     * GetCorporationNames
     *
     * @param Boolean $asString AsString
     *
     * @return array|Array[]
     */
    public function getCorporationNames($asString = true)
    {
        $unit = $units = $corporation = $corporations = $stringCorporations = null;
        $corporationsMain = $this->getMainCorporateName();
        $corporationsAdded = $this->getAddedCorporateNames();

        if (!empty($corporationsMain)) {
            $corporations[] = $corporationsMain;
            $corporations = array_merge($corporations, $corporationsAdded);
        } else {
            $corporations = $corporationsAdded;
        }

        if ($asString) {
            $stringCorporations = [];

            foreach ($corporations as $corporation) {
                $name = isset($corporation['name']) ?
                    rtrim($corporation['name'], '.') : '';
                if (isset($corporation['unit'])) {
                    foreach ($corporation['unit'] as $unit) {
                        $units = '. ' . $unit;
                    }
                    $stringCorporations[] = trim($name . $units);
                } else {
                    $stringCorporations[] = $name;
                }
            }
            return $stringCorporations;
        }
        return false;
    }

    /**
     * Get corporate name (authors)
     *
     * @return Array[]
     */
    public function getMainCorporateName()
    {
        return $this->getMarcSubFieldMap(110, $this->corporationFieldMap);
    }

    /**
     * Get added corporate names
     *
     * @return Array[]
     */
    public function getAddedCorporateNames()
    {
        return $this->getMarcSubFieldMaps(710, $this->corporationFieldMap);
    }

    /**
     * Get entries for related personal and corporate entries
     *
     * @return array
     */
    public function getRelatedEntries()
    {
        $related = explode(',', $this->mainConfig->RelatedEntries->related);

        $related_persons_100 = array_filter(
            $this->getMarcSubFieldMaps('100', $this->personFieldMap),
            function ($field) use ($related) {
                return isset($field['relator_code'])
                && in_array($field['relator_code'], $related);
            }
        );

        $related_persons = array_filter(
            $this->getMarcSubFieldMaps('700', $this->personFieldMap),
            function ($field) use ($related) {
                return isset($field['relator_code'])
                    && in_array($field['relator_code'], $related);
            }
        );

        $related_corporations_110 = array_filter(
            $this->getMarcSubFieldMaps('110', $this->corporationFieldMap),
            function ($field) use ($related) {
                return isset($field['relator_code'])
                && in_array($field['relator_code'], $related);
            }
        );

        $related_corporations = array_filter(
            $this->getMarcSubFieldMaps('710', $this->corporationFieldMap),
            function ($field) use ($related) {
                return isset($field['relator_code'])
                    && in_array($field['relator_code'], $related);
            }
        );

        if ($related_persons_100 || $related_persons
            || $related_corporations_110 || $related_corporations
        ) {
            return [
                'persons100' => $related_persons_100,
                'persons' => $related_persons,
                'corporations110' => $related_corporations_110,
                'corporations' => $related_corporations,
            ];
        } else {
            return null;
        }
    }

    /**
     * Get collection title
     *
     * @param Boolean $asStrings AsStrings
     *
     * @return array
     */
    public function getCollectionTitle($asStrings = true)
    {
        $data = $this->getMarcSubFieldMaps(
            499,
            [
                'a' => 'collection',
                'v' => 'part',
            ]
        );

        if ($asStrings) {
            $strings = [];

            foreach ($data as $field) {
                $string = '';

                if (isset($field['collection'])) {
                    $string = $field['collection'];
                }
                if (isset($field['part'])) {
                    $string .= ', ' . $field['part'];
                }
                $strings[] = trim($string);
            }
            $data = $strings;
        }

        return $data;
    }

    /**
     * Get title of archival volumes
     *
     * @return String
     */
    public function getArchivalVolumeTitle()
    {
        $data = $this->getMarcSubFieldMap(
            779,
            [
            'g' => 'number',
            't' => 'partTitle',
            ]
        );
        if (empty($data)) {
            return null;
        } else {
            $string = [];
            if (isset($data['number'])) {
                $string = $data['number'];
            }
            if (isset($data['partTitle'])) {
                $string .= ': ' . $data['partTitle'];
            }
            return $string;
        }
    }

    /**
     * Get Hierarchical level of record
     *
     * @return String
     */
    public function getHierachicalLevel()
    {
        return $this->getFirstFieldValue('351', ['c']);
    }

    /**
     * Get date from 046
     *
     * @param Boolean $asStrings AsStrings
     *
     * @return array
     */
    public function getSpecialCodedDate($asStrings = true)
    {
        $data = $this->getMarcSubFieldMaps(
            '046', [
                'b' => 'date1BCE',
                'c' => 'date1',
                'd' => 'date2BCE',
                'e' => 'date2',
            ]
        );
        if ($asStrings) {
            $strings = [];

            foreach ($data as $date) {
                $string = '';

                if (isset($date['date1BCE'])) {
                    $string
                        = implode(
                            '.', array_reverse(explode('.', $date['date1BCE']))
                        )
                        . ' (v. Chr.)';
                }
                if (isset($date['date1'])) {
                    $string
                        .= implode('.', array_reverse(explode('.', $date['date1'])));
                }
                if (isset($date['date2BCE'])) {
                    $string
                        .= '-' . implode(
                            '.', array_reverse(explode('.', $date['date2BCE']))
                        )
                        . ' (v. Chr.)';
                }
                if (isset($date['date2'])) {
                    $string
                        .= '-' . implode(
                            '.', array_reverse(explode('.', $date['date2']))
                        );
                }

                $strings[] = trim($string);
            }

            $data = $strings;
        }
        return $data;
    }

    /**
     * Get Immediate Source of Acquisition Note (MARC21 field 541)
     *
     * @param Boolean $asStrings AsStrings
     *
     * @return array
     */
    public function getAcquisitionNote($asStrings = true)
    {
        $data = $this->getMarcSubFieldMaps(
            541, [
                'a' => 'source',
                'b' => 'address',
                'c' => 'method',
                'd' => 'date',
                'e' => 'accessionNo',
                'f' => 'owner'
            ]
        );

        if ($asStrings) {
            $strings = [];

            foreach ($data as $field) {
                $string = '';

                if (isset($field['source'])) {
                    $string = $field['source'] . ', ';
                }
                if (isset($field['address'])) {
                    $string .= $field['address'] . ', ';
                }
                if (isset($field['method'])) {
                    $string .= $field['method'] . ', ';
                }
                if (isset($field['date'])) {
                    $string .= $field['date'] . ', ';
                }
                if (isset($field['owner'])) {
                    $string .= $field['owner'];
                }
                if (isset($field['accessionNo'])) {
                    $string .= ' (' . $field['accessionNo'] . ')';
                }

                $strings[] = trim($string);
            }
            $data = $strings;
        }
        return $data;
    }

    /**
     * Get biographical information or administrative history
     *
     * @return array
     */
    public function getHistData()
    {
        $data = $this->getMarcSubFieldMaps(
            545, [
                'a' => 'histdata',
                'b' => 'expansion',
                '_u' => 'url',
            ]
        );
        return $data;
    }

    /**
     * Get added entry geographic name
     *
     * @return array
     */
    public function getPlaceNames()
    {
        return $this->getFieldArray('751', ['a']);
    }

    /**
     * Get subtitle
     *
     * @param Boolean $full Get full field data. Else only field c is fetched
     *
     * @return String|String[]
     */
    public function getTitleStatement($full = false)
    {
        if ($full) {
            return $this->getMarcSubFieldMap(
                245, [
                'a' => 'title',
                'b' => 'title_remainder',
                'c' => 'statement_responsibility',
                'f' => 'inclusive_dates',
                'g' => 'bulk_dates',
                'h' => 'medium',
                '_k' => 'form',
                '_n' => 'parts_amount',
                '_p' => 'parts_name',
                's' => 'version'
                ]
            );
        } else {
            return parent::getTitleStatement();
        }
    }

    /**
     * Get edition
     *
     * @return String
     */
    public function getEdition()
    {
        $data = $this->getMarcSubFieldMaps(
            250, [
                'a' => 'statement',
                'b' => 'remainder',
            ]
        );

        if (!empty($data)) {
            foreach ($data as $field) {
                if (isset($field['statement'])) {
                    $string = $field['statement'];
                }
                if (isset($field['remainder'])) {
                    $string .= ' / ' . $field['remainder'];
                }
            }
            return $string;
        }
        return false;
    }

    /**
     * Get alternative title
     *
     * @return array
     */
    public function getAltTitle()
    {
        return $this->getFieldArray('246', ['a', 'b', 'i']);
    }

    /**
     * Get former title
     *
     * @return array
     */
    public function getFormerTitle()
    {
        return $this->getFieldArray('247', ['a', 'b']);
    }

    /**
     * Get Cartographic Mathematical Data
     *
     * @return string
     */
    public function getCartMathData()
    {
        $data = $this->getMarcSubFieldMaps(
            255, [
                'a' => 'scale',
                'b' => 'projection',
                'c' => 'coordinates',
                'd' => 'zone',
                'e' => 'equinox',
            ]
        );

        if (!empty($data)) {
            foreach ($data as $field) {
                $string = '';

                if (isset($field['scale'])) {
                    $string = $field['scale'];
                }
                if (isset($field['projection'])) {
                    $string .= '; ' . $field['projection'];
                }
                if (isset($field['coordinates'])) {
                    $string .= ' - ' . $field['coordinates'];
                }
            }
            return $string;
        }

        return false;
    }

    /**
     * Get dissertation notes for the record.
     *
     * @return array
     */
    public function getDissertationNotes()
    {
        return $this->getFieldArray('502', ['a', 'b', 'c', 'd', 'g']);
    }

    /**
     * Get original title from IDS MARC
     *
     * @param Boolean $asStrings AsStrings
     *
     * @return array
     */
    public function getOriginalTitle($asStrings = true)
    {
        $data = $this->getMarcSubFieldMaps(
            509, [
            'a' => 'title',
            'n' => 'count',
            'p' => 'worktitle',
            'r' => 'author',
            'i' => 'addtext',
            ]
        );
        if ($asStrings) {
            $strings = [];

            foreach ($data as $origtitle) {
                $string = '';

                if (isset($origtitle['title'])) {
                    $string = str_replace('@', '', $origtitle['title']);
                }
                if (isset($origtitle['count'])) {
                    $string .= '(' . $origtitle['count'] . ')';
                }
                if (isset($origtitle['author'])) {
                    $string .= ' / ' . $origtitle['author'];
                }
                if (isset($origtitle['addtext'])) {
                    $string .= '. - ' . $origtitle['addtext'];
                }

                $strings[] = trim($string);
            }

            $data = $strings;
        }
        return $data;
    }

    /**
     * Get Title of Work (field 240 or field 130)
     *
     * @param Boolean $asStrings AsStrings
     *
     * @return array
     */
    public function getWorkTitle($asStrings = true)
    {
        $fieldsToCheck = [
        '240',
        '130',
        '730',
         ];

        foreach ($fieldsToCheck as $field) {
            $data = $this->getMarcSubFieldMaps(
                $field, [
                'a' => 'title',
                'g' => 'misc',
                '_m' => 'medium',
                'n' => 'count',
                'r' => 'key',
                's' => 'version',
                'p' => 'part',
                'k' => 'form',
                'o' => 'arranged',
                'f' => 'date',
                ]
            );

            if ($asStrings) {
                $strings = [];

                foreach ($data as $worktitle) {
                    $string = '';

                    if (isset($worktitle['title'])) {
                        $string = $worktitle['title'];
                    }
                    if (isset($worktitle['1medium'])) {
                        $string .= ', ' .
                            $worktitle['1medium'];
                    }
                    if (isset($worktitle['2medium'])) {
                        $string .= ', ' .
                            $worktitle['2medium'];
                    }
                    if (isset($worktitle['3medium'])) {
                        $string .= ', ' .
                            $worktitle['3medium'];
                    }
                    if (isset($worktitle['4medium'])) {
                        $string .= ', ' .
                            $worktitle['4medium'];
                    }
                    if (isset($worktitle['5medium'])) {
                        $string .= ', ' .
                            $worktitle['5medium'];
                    }
                    if (isset($worktitle['6medium'])) {
                        $string .= ', ' .
                            $worktitle['6medium'];
                    }
                    if (isset($worktitle['7medium'])) {
                        $string .= ', ' .
                            $worktitle['7medium'];
                    }
                    if (isset($worktitle['count'])) {
                        $string .= ', ' . $worktitle['count'];
                    }
                    if (isset($worktitle['key'])) {
                        $string .= ', ' . $worktitle['key'];
                    }
                    if (isset($worktitle['version'])) {
                        $string .= ', ' . $worktitle['version'];
                    }
                    if (isset($worktitle['part'])) {
                        $string .= ', ' . $worktitle['part'];
                    }
                    if (isset($worktitle['form'])) {
                        $string .= ', ' . $worktitle['form'];
                    }
                    if (isset($worktitle['arranged'])) {
                        $string .= ', ' . $worktitle['arranged'];
                    }
                    if (isset($worktitle['misc'])) {
                        $string .= '. ' . $worktitle['misc'];
                    }
                    if (isset($worktitle['date'])) {
                        $string .= ' (' . $worktitle['date'] . ')';
                    }

                    $strings[] = trim($string);
                }

                if ($strings) {
                    $data = $strings;
                    break;
                }
            }
        }
        return $data;
    }

    /**
     * Get list works from 700/710
     *
     * @param Boolean $asStrings AsString
     *
     * @return array
     */
    public function getRelatedWork($asStrings = true)
    {
        $relatedWorks = null;
        $fieldsToCheck = [
            '700',
            '710',
        ];

        foreach ($fieldsToCheck as $field) {
            if ($field === '700') {
                $data = $this->getMarcSubFieldMaps($field, $this->personFieldMap);
            } else {
                $data = $this->getMarcSubFieldMaps(
                    $field,
                    $this->corporationFieldMap
                );
            }

            if ($asStrings) {
                $strings = [];

                foreach ($data as $worktitle) {
                    if ($worktitle['@ind2'] === '2') {
                        $string = '';

                        if (isset($worktitle['relationship'])) {
                            $string = $worktitle['relationship'] . ': ';
                        }

                        if (isset($worktitle['name'])) {
                            $string .= $worktitle['name'];
                        }

                        if (isset($worktitle['forename'])) {
                            $string .= ', ' . $worktitle['forename'];
                        }

                        if (isset($worktitle['dates'])) {
                            $string .= ' (' . $worktitle['dates'] . ')';
                        }

                        if (isset($worktitle['title_of_work'])) {
                            $string .= ' : ' . $worktitle['title_of_work'];
                        }

                        if (isset($worktitle['1music_performance_medium'])) {
                            $string .= ', ' .
                                $worktitle['1music_performance_medium'];
                        }

                        if (isset($worktitle['2music_performance_medium'])) {
                            $string .= ', ' .
                                $worktitle['2music_performance_medium'];
                        }

                        if (isset($worktitle['3music_performance_medium'])) {
                            $string .= ', ' .
                                $worktitle['3music_performance_medium'];
                        }

                        if (isset($worktitle['4music_performance_medium'])) {
                            $string .= ', ' .
                                $worktitle['4music_performance_medium'];
                        }

                        if (isset($worktitle['5music_performance_medium'])) {
                            $string .= ', ' .
                                $worktitle['5music_performance_medium'];
                        }

                        if (isset($worktitle['6music_performance_medium'])) {
                            $string .= ', ' .
                                $worktitle['6music_performance_medium'];
                        }

                        if (isset($worktitle['7music_performance_medium'])) {
                            $string .= ', ' .
                                $worktitle['7music_performance_medium'];
                        }

                        if (isset($worktitle['1parts_number'])) {
                            $string .= ', ' . $worktitle['1parts_number'];
                        }
                        if (isset($worktitle['music_key'])) {
                            $string .= ', ' . $worktitle['music_key'];
                        }
                        if (isset($worktitle['version'])) {
                            $string .= ', ' . $worktitle['version'];
                        }
                        if (isset($worktitle['1parts_name'])) {
                            $string .= ', ' . $worktitle['1parts_name'];
                        }
                        if (isset($worktitle['1form_subheadings'])) {
                            $string .= ', ' . $worktitle['1form_subheadings'];
                        }
                        if (isset($worktitle['arranged_music'])) {
                            $string .= ', ' . $worktitle['arranged_music'];
                        }
                        if (isset($worktitle['misc'])) {
                            $string .= '. ' . $worktitle['misc'];
                        }
                        if (isset($worktitle['date_of_work'])) {
                            $string .= ' (' . $worktitle['date_of_work'] . ')';
                        }

                        $strings[] = trim($string);
                    }
                }

                if ($strings) {
                    $relatedWorks = $strings;
                    break;
                }
            }
        }
        return $relatedWorks;
    }

    /**
     * Get Physical Medium (MARC21: field 340)
     *
     * @return array
     */
    public function getPhysicalMedium()
    {
        return $this->getFieldArray('340', ['a', 'd', 'i']);
    }

    /**
     * Get Dates of Publication and/or Sequential Designation (field 362)
     *
     * @return Array
     */
    public function getContResourceDates()
    {
        return $this->getFieldArray('362');
    }

    /**
     * Get access restriction note for the record
     *
     * @return array
     */
    public function getAccess()
    {
        $data = $this->getMarcSubFieldMaps(
            506, [
                'a' => 'accessrestrict',
                'c' => 'usability',
                'u' => 'url',
            ]
        );
        return $data;
    }

    /**
     * Get citation / reference note for the record
     *
     * @return array
     */
    public function getCitationNotes()
    {
        $data = $this->getMarcSubFieldMaps(
            510, [
                'a' => 'source',
                'c' => 'location',
                'u' => 'url',
            ]
        );
        return $data;
    }

    /**
     * Get participant or performer note for the record.
     *
     * @return array
     */
    public function getPerformerNote()
    {
        return $this->getFieldArray('511');
    }

    /**
     * Get type of computer file or data note for the record.
     *
     * @return array
     */
    public function getFileNote()
    {
        return $this->getFieldArray('516');
    }

    /**
     * Get date/time and place of an event note for the record.
     *
     * @return array
     */
    public function getEventNote()
    {
        return $this->getFieldArray('518');
    }

    /**
     * Get citation / reference note for the record
     *
     * @return array
     */
    public function getAdditionalPhysicalForm()
    {
        $data = $this->getMarcSubFieldMaps(
            530, [
                'a' => 'form',
                'c' => 'condition',
                'u' => 'url',
            ]
        );
        return $data;
    }

    /**
     * Get original version note for the record.
     *
     * @return array
     */
    public function getOriginalVersionNotes()
    {
        return $this->getFieldArray('534', ['p', 't', 'b', 'c', 'n']);
    }

    /**
     * Get language information
     *
     * @return array
     */
    public function getLangData()
    {
        return $this->getFieldArray('546', ['a', 'b']);
    }

    /**
     * Get information for the record (HAN: field 561)
     *
     * @return array
     */
    public function getOwnerNote()
    {
        $data = $this->getMarcSubFieldMaps(
            561, [
                'a' => 'custodhist',
                'u' => 'url',
            ]
        );
        return $data;
    }

    /**
     * Get original version note for the record (MARC21: field 562)
     * and item-specific note for the record (swissbib MARC: field 590)
     *
     * @return array
     */
    public function getCopyNotes()
    {
        $f562 = $this->getFieldArray('562');
        $f590 = $this->getFieldArray('590');

        $copynotes = array_merge_recursive($f562, $f590);

        return $copynotes;
    }

    /**
     * Get original version note for the record (MARC21: field 563)
     *
     * @return array
     */
    public function getBinding()
    {
        $data = $this->getMarcSubFieldMaps(
            563, [
                'a' => 'binding',
                'u' => 'url',
            ]
        );
        return $data;
    }

    /**
     * Get publications about described materials note (581)
     *
     * @return array
     */
    public function getPublications()
    {
        return $this->getFieldArray('581');
    }

    /**
     * Get exhibitions note (585)
     *
     * @return array
     */
    public function getExhibitions()
    {
        return $this->getFieldArray('585');
    }

    /**
     * HAN  - Beschreibung - Description-Tab für HAN
     * Get information for the record (HAN: field 254)
     *
     * @return array
     */
    public function getMusicPresentation()
    {
        return $this->getFieldArray('254');
    }

    /**
     * Get information for the record (field 348, gnd_music)
     *
     * @return array
     */
    public function getMusicFormat()
    {
        return $this->getFieldArray('348', ['a']);
    }

    /**
     * HAN  - Beschreibung - Description-Tab für HAN
     * Get information for the record (HAN: field 351 $a, $c)
     *
     * @return array
     */
    public function getOrderClassification()
    {
        $separator = isset($this->mainConfig->Record->marcPublicationInfoSeparator)
            ? $this->mainConfig->Record->marcPublicationInfoSeparator : ' ';

        return $this->getFieldArray('351', ['a', 'c'], true, $separator);
    }

    /**
     * Get information for the record (HAN: field 525)
     *
     * @return array
     */
    public function getSupplement()
    {
        return $this->getFieldArray('525');
    }

    /**
     * Get information for the record (HAN: field 355)
     *
     * @return array
     */
    public function getSecurityClassification()
    {
        $data = $this->getMarcSubFieldMaps(
            355, [
                'a' => 'security',
                'h' => 'date',
            ]
        );
        return $data;
    }

    /**
     * Get information for the record (HAN: field 533)
     *
     * @return array
     */
    public function getReproduction()
    {
        $separator = isset($this->mainConfig->Record->marcPublicationInfoSeparator)
            ? $this->mainConfig->Record->marcPublicationInfoSeparator : ' ';

        return $this->getFieldArray('533', ['a', 'b', 'c', 'n'], true, $separator);
    }

    /**
     * Get reproducitonClassification note for the record
     *
     * @return array
     */
    public function getReproductionClassification()
    {
        $data = $this->getMarcSubFieldMaps(
            540, [
                'a' => 'regularisation',
                'c' => 'resource',
                'n' => 'notice',
                'u' => 'url',
            ]
        );
        return $data;
    }

    /**
     * Get information for the record (HAN: field 544)
     *
     * @return array
     */
    public function getArchivalLevel()
    {
        return $this->getFieldArray('544', ['a',  'n',]);
    }

    /**
     * Get information for the record (HAN: field 555)
     *
     * @return array
     */
    public function getFindingAids()
    {
        $data = $this->getMarcSubFieldMaps(
            555, [
                'a' => 'cumulativeindex',
                'c' => 'degreecontrol',
                'u' => 'url',
            ]
        );
        return $data;
    }

    /**
     * Get information for the record (HAN: field 584)
     *
     * @return array
     */
    public function getAccumulationFrequency()
    {
        return $this->getFieldArray('584');
    }

    /**
     * Get data used for NationalLicences
     *
     * @return array
     */
    public function getNationalLicenceData()
    {
        $ref = $this->getFieldArray('949', ['F']);
        //gives always the same publisher in result list
        //$publisher = array_values($this->getHoldingsStructure())[0]['institution'];

        $publisherArray = $this->getFieldArray('035', ['a']);
        $publisher = "";
        foreach ($publisherArray as $key => $val) {
            if (strpos($val, '(NATIONALLICENCE)') === 0) {
                $publisher = $val;
            }
        }
        $publisher = explode("-", $publisher)[0];
        $publisher = str_replace("(NATIONALLICENCE)", "NL-", $publisher);

        $pii = "";
        /* publisher identifier (needed for linking to cambridge)
        stored in 024, with $2 pii */

        $identifiers = $this->getFieldArray('024', ['a','2'], true, "$$$");
        // example
        // [0] => 10.1017/S1014233900003497$$$doi
        // [1] => S1014233900003497$$$pii

        foreach ($identifiers as $identifier) {
            $result = explode("$$$", $identifier);
            if (count($result) > 1 && $result[1] == "pii") {
                $pii = $result[0];
            }
        }

        $journalCode = $this->getFieldArray('773', ['o']);
        $nl = !empty($ref) && in_array("NATIONALLICENCE", $ref) ?
            $nl = "NATIONALLICENCE" : "";
        $nlData = [ $nl,
                    $publisher,
                    !empty($journalCode) ? $journalCode[0] : "",
                    $pii,
        ];

        return $nlData;
    }

    /**
     * Get group-id from solr-field to display FRBR-Button
     *
     * @return String|Number
     */
    public function getGroup()
    {
        return isset($this->fields['groupid_isn_mv']) ?
                $this->multiValuedFRBRField ? $this->fields['groupid_isn_mv'][0] :
                    $this->fields['groupid_isn_mv'] : '';
    }

    /**
     * Get institution codes
     *
     * @param Boolean $extended Extended
     *
     * @return array
     */
    public function getInstitutions($extended = false)
    {
        $institutions = [];

        if (isset($this->fields['institution'])
            && is_array($this->fields['institution'])
        ) {
            $institutions = $this->fields['institution'];

            if ($extended) {
                $fields = $this->getMarcRecord()->getFields('949');
                $konkordanzTabelle949 = [];
                if (!empty($fields)) {
                    foreach ($fields as $currentField) {
                        $b = $this->getSubfieldArray($currentField, ['b']);
                        $F = $this->getSubfieldArray($currentField, ['F']);
                        if (!array_key_exists($F[0], $konkordanzTabelle949)) {
                            $konkordanzTabelle949[$F[0]] = [];
                        }
                        if (isset($b[0])) {
                            array_push($konkordanzTabelle949[$F[0]], $b[0]);
                        }
                    }
                }
                foreach ($institutions as $key => $institution) {
                    $institution_b = [];
                    if (isset($konkordanzTabelle949[$institution])) {
                        $institution_b = $konkordanzTabelle949[$institution];
                    }
                    $institutions[$key] = [
                        'institution' => $institution,
                        'group' => $this->getHoldingsHelper()
                            ->getGroup($institution),
                        'institution_b' => $institution_b
                    ];
                }
            }
        }
        return $institutions;
    }

    /**
     * Get structured subject vocabularies from predefined fields
     * Extended version of getAllSubjectHeadings()
     *
     * $fieldIndexes contains keys of fields to check
     * $vocabConfigs contains checks for vocabulary detection
     *
     * $vocabConfigs:
     * - ind: Value for indicator 2 in tag
     * - field: sub field 2 in tag
     * - fieldsOnly: Only check for given field indexes
     * - detect: The vocabulary key is defined in sub field 2.
     *   Don't use the key in the config (only used for local)
     *
     * Expected result:
     * [
     *        gnd => [
     *            600 => [{},{},{},...]
     *            610 => [{},{},{},...]
     *            620 => [{},{},{},...]
     *        ],
     *    rero => [
     *            600 => [{},{},{},...]
     *            610 => [{},{},{},...]
     *            620 => [{},{},{},...]
     *        ]
     * ]
     * {} is an assoc array which contains the field data
     *
     * @param Boolean $ignoreControlFields Ignore control fields 0 and 2
     *
     * @see getAllSubjectHeadings
     *
     * @return Array[]
     */
    public function getAllSubjectVocabularies($ignoreControlFields = false)
    {
        $subjectVocabularies = [];
        $fieldIndexes = [600, 610, 611, 630, 648, 650, 651, 653, 655, 656, 690, 691];
        $vocabConfigs = [
            'lcsh' => [
                'ind' => 0
            ],
            'mesh' => [
                'ind' => 2
            ],
            'unspecified' => [
                'ind' => 4
            ],
            'gnd' => [
                'ind' => 7,
                'field' => 'gnd'
            ],
            'gndcontent' => [
                'ind' => 7,
                'field' => 'gnd-content'
            ],
            'gndcarrier' => [
                'ind' => 7,
                'field' => 'gnd-carrier'
            ],
            'gndmusic' => [
                'ind' => 7,
                'field' => 'gnd-music'
            ],
            'rero' => [
                'ind' => 7,
                'field' => 'rero'
            ],
            'idsbb' => [
                'ind' => 7,
                'field' => 'idsbb'
            ],
            'idszbz' => [
                'ind' => 7,
                'field' => 'idszbz'
            ],
            /*'idslu'       => array(
                'ind'   => 7,
                'field' => 'idslu'
            ),
            'bgr'         => array(
                'ind'   => 7,
                'field' => 'bgr'
            ),*/
            'sbt' => [
                'ind' => 7,
                'field' => 'sbt'
            ],
            'jurivoc' => [
                'ind' => 7,
                'field' => 'jurivoc'
            ],
            /* only works for one indicator (test case)
               implement with new CBS-data (standardised MARC, not IDSMARC)
            */
            'local' => [
                'ind' => 7,
                'fieldsOnly' => [690],
                'detect' => false // extract vocabulary from sub field 2
            ],

            'uncontrolledtopical' => [
                'ind' => 0,
                'fieldsOnly' => [653],
                'detect' => false // extract vocabulary from sub field 2
            ],

            'uncontrolledpersonal' => [
                'ind' => 1,
                'fieldsOnly' => [653],
                'detect' => false // extract vocabulary from sub field 2
            ],

            'uncontrolledcoporate' => [
                'ind' => 2,
                'fieldsOnly' => [653],
                'detect' => false // extract vocabulary from sub field 2
            ],

            'uncontrolledmeeting' => [
                'ind' => 3,
                'fieldsOnly' => [653],
                'detect' => false // extract vocabulary from sub field 2
            ],

            'uncontrolledchronological' => [
                'ind' => 4,
                'fieldsOnly' => [653],
                'detect' => false // extract vocabulary from sub field 2
            ],

            'uncontrolledgeographic' => [
                'ind' => 5,
                'fieldsOnly' => [653],
                'detect' => false // extract vocabulary from sub field 2
            ],

            'uncontrolledgenre' => [
                 'ind' => 6,
                 'fieldsOnly' => [653],
                 'detect' => false // extract vocabulary from sub field 2
            ],
        ];
        $fieldMapping = [
            'a' => 'a',
            '_b' => 'b',
            'c' => 'c',
            'd' => 'd',
            'e' => 'e',
            'f' => 'f',
            'g' => 'g',
            'h' => 'h',
            'p' => 'p',
            't' => 't',
            '_v' => 'v',
            '_x' => 'x',
            '_y' => 'y',
            '_z' => 'z'
        ];

        // Add control fields to mapping list
        if (!$ignoreControlFields) {
            $fieldMapping += [
                '0' => '0',
                '2' => '2'
            ];
        }

        // Iterate over all indexes to check the available fields
        foreach ($fieldIndexes as $fieldIndex) {
            $indexFields = $this->getMarcFields($fieldIndex);

            // iterate over all fields found for the current index
            foreach ($indexFields as $indexField) {
                // check all vocabularies for matching
                foreach ($vocabConfigs as $vocabKey => $vocabConfig) {
                    $fieldData = false;
                    $useAsVocabKey = $vocabKey;

                    // Are limited fields set in config
                    if (isset($vocabConfig['fieldsOnly'])
                        && is_array($vocabConfig['fieldsOnly'])
                    ) {
                        if (!in_array($fieldIndex, $vocabConfig['fieldsOnly'])) {
                            continue; // Skip vocabulary if field is not in list
                        }
                    }

                    $indexFieldIndicator2 = $indexField->getIndicator(2);
                    if (isset($vocabConfig['ind'])
                        && $indexFieldIndicator2 == (string)$vocabConfig['ind']
                    ) {
                        // is there a field check required?
                        if (isset($vocabConfig['field'])) {
                            $subField2 = $indexField->getSubfield('2');
                            if ($subField2
                                && $subField2->getData() === $vocabConfig['field']
                            ) { // Check field
                                // sub field 2 matches the config
                                $fieldData = $this->getMappedFieldData(
                                    $indexField, $fieldMapping, false
                                );
                            }
                        } else { // only indicator required, add data
                            $fieldData = $this->getMappedFieldData(
                                $indexField, $fieldMapping, false
                            );
                        }
                    }

                    // Found something? Add to list, stop vocab check
                    // and proceed with next field
                    if ($fieldData) {
                        // Is detect option set, replace vocab key with value
                        // from sub field 2 if present
                        if (isset($vocabConfig['detect'])
                            && $vocabConfig['detect']
                        ) {
                            $subField2 = $indexField->getSubfield('2');
                            if ($subField2) {
                                $useAsVocabKey = $subField2->getData();
                            }
                        }

                        $subjectVocabularies[$useAsVocabKey][$fieldIndex][]
                            = $fieldData;
                        break; // Found vocabulary, stop search
                    }
                }
            }
        }

        return $subjectVocabularies;
    }

    /**
     * Get host item entry
     *
     * @return Array
     */
    public function getHostItemEntry()
    {
        return $this->getMarcSubFieldMaps(
            773, [
            'd' => 'place',
            't' => 'title',
            'g' => 'related',
            'q' => 'enumeration',
            'x' => 'issn',
            ]
        );
    }

    /**
     * Get the publishers of the record.
     *
     * @return array
     */
    public function getPublishers()
    {
        return $this->getPublicationInfo('b');
    }

    /**
     * Get human readable publication dates for display purposes (may not be suitable
     * for computer processing -- use getPublicationDates() for that).
     * still using coded data for display in swissbib, reason: consistency
     *
     * @return array
     */
    public function getHumanReadablePublicationDates()
    {
        $codeddata = $this->getPublicationDates();

        if (!is_array($codeddata) || sizeof($codeddata) == 0) {
            return '';
        }

        if (is_array($codeddata) && sizeof($codeddata) == 1) {
            return $codeddata[0];
        }

        $retVal = [];

        $dateType = $codeddata[0];
        $year1    = $codeddata[1];
        $year2    = $codeddata[2];

        switch ($dateType) {
        case 's':
        case 't':
        case 'n':
        case 'e':
            $retVal[0] = $year1;
            break;

        case 'c':
        case 'u':
            $retVal[0] = $year1 . '-';
            break;

        case 'd':
            $retVal[0] = $year1 . '-' . $year2;
            break;

        case 'p':
        case 'r':
            $retVal[0] = $year1 . ' [' . $year2 . ']';
            break;

        case 'q':
            if ($year2 === '9999') {
                $retVal[0] = $year1;
            } elseif ($year2 != '9999') {
                $retVal[0] = $year1 . ' / ' . $year2;
            }
            break;

        case 'm':
            if ($year2 === '9999') {
                $retVal[0] = $year1 . '-';
            } elseif ($year2 != '9999') {
                $retVal[0] = $year1 . '-' . $year2;
            }
            break;

        case 'i':
            if ($year1 === $year2) {
                $retVal[0] = $year1;
            }
            if ($year2 === '9999') {
                $retVal[0] = $year1 . '-';
            } else {
                $retVal[0] = $year1 . '-' . $year2;
            }
            break;
        }
        $retVal[0] = str_replace('u', '?', $retVal);

        return $retVal[0];
    }

    /**
     * Get physical description out of the MARC record
     *
     * @param Boolean $asStrings AsStrings
     *
     * @return Array[]|String[]
     */
    public function getPhysicalDescriptions($asStrings = true)
    {
        $descriptions = $this->getMarcSubFieldMaps(
            300, [
            '_a' => 'extent',
            'b' => 'details',
            '_c' => 'dimensions',
            'd' => 'material_single',
            '_e' => 'material_multiple',
            '_f' => 'type',
            '_g' => 'size',
            '3' => 'appliesTo'
            ]
        );

        if ($asStrings) {
            $strings = [];
            foreach ($descriptions as $description) {
                if (isset($description['extent'])
                    && isset($description['extent'][0])
                ) {
                    $strings[] = $description['extent'][0];
                }
            }
            $descriptions = $strings;
        }

        return $descriptions;
    }

    /**
     * Get Dates of Publication and/or Sequential Designation (field 362)
     *
     * @return array
     */
    public function getDateSpan()
    {
        return $this->getFieldArray('362');
    }

    /**
     * Get unions
     *
     * @return String[]
     */
    public function getUnions()
    {
        return isset($this->fields['union']) ? $this->fields['union'] : [];
    }

    /**
     * Get online status
     *
     * @return Boolean
     */
    public function getOnlineStatus()
    {
        $filter = array_key_exists('filter_str_mv', $this->fields) ?
            $this->fields['filter_str_mv'] : [];
        return in_array('ONL', $filter) ? true : false;
    }

    /**
     * Get short title
     * Override base method to assure a string and not an array
     * as long as title_short is multivalued=true in solr
     * (necessary because of faulty data)
     *
     * @return String
     */
    public function getShortTitle()
    {
        $shortTitle = parent::getShortTitle();

        return is_array($shortTitle) ? reset($shortTitle) : $shortTitle;
    }

    /**
     * Get title
     *
     * @return String
     */
    public function getTitle()
    {
        $title = parent::getTitle();

        return is_array($title) ? reset($title) : $title;
    }

    /**
     * Get holdings data
     *
     * @param String  $institutionCode InstitutionCode
     * @param Boolean $extend          Extend
     *
     * @return Array|Boolean
     */
    public function getInstitutionHoldings($institutionCode, $extend = true)
    {
        return $this->getHoldingsHelper()->getHoldings(
            $this, $institutionCode, $extend
        );
    }

    /**
     * Get holdings structure without item details
     *
     * @return Array[]|bool
     */
    public function getHoldingsStructure()
    {
        return $this->getHoldingsHelper()->getHoldingsStructure();
    }

    /**
     * Return an array of all values extracted from the specified field/subfield
     * combination.  If multiple subfields are specified and $concat is true, they
     * will be concatenated together in the order listed -- each entry in the array
     * will correspond with a single MARC field.  If $concat is false, the return
     * array will contain separate entries for separate subfields.
     *
     * @param string $field     The MARC field number to read
     * @param array  $subfields The MARC subfield codes to read
     * @param bool   $concat    Should we concatenate subfields?
     * @param string $separator Separator string (used only when $concat === true)
     *
     * @return array
     */
    public function getField(
        $field,
        $subfields = null,
        $concat = true,
        $separator = ' '
    ) {
        return $this->getFieldArray($field, $subfields, $concat, $separator);
    }

    /**
     * Returns institutions which we won't show by its name
     *
     * @return array
     */
    public function get949b()
    {
        $r = [];
        $institutionCodes = $this->getFieldArray('949', ['b']);
        foreach ($institutionCodes as $institutionCode) {
            $i['institution'] = $institutionCode;
            array_push($r, $i);
        }
        return $r;
    }

    /**
     * Returns an array with all system numbers of all unions/network
     *
     * @return array
     */
    public function getAll035Idls()
    {
        $allIdls = [];
        $field035Array = $this->getFieldArray('035');
        foreach ($field035Array as $field035) {
            if (strpos($field035, '(') === false
                || strpos($field035, ')') === false
            ) {
                continue;
            }
            $idls = substr($field035, 1, strpos($field035, ')') - 1);
            $sysNr = explode(')', $field035)[1];
            $allIdls[$field035] = ['idls' => $idls, 'sysNr' => $sysNr];
        }
        return $allIdls;
    }

    /**
     * Get array of institutionCodes with availability value
     *
     * @param String $idls  network/union
     * @param String $sysNr system number
     *
     * @return array
     */
    public function getAvailabilityIconFromServer($idls, $sysNr)
    {
        $r = $r3 = [];
        $userLocale = $this->translator->getLocale();
        if ($idls == 'RERO') {
            $hh = $this->getHoldingsHelper();
            $hs = $hh->getHoldingsStructure();
            if (isset($hs[$idls])) {
                $institutions = $hh->getHoldingsStructure()[$idls]['institutions'];
                foreach ($institutions as $institution) {
                    $institutionCode = $institution['label'];
                    $hh->resetHolding();
                    $barcodes = $hh->getAllBarcodes($this, $institutionCode);
                    $r2 = $this->availabilityHelper
                        ->getAvailability($sysNr, $barcodes, $idls, $userLocale);
                    if (is_array($r2)) {
                        foreach ($r2 as $key => $val) {
                            if ($val['statusfield'] == 'lendable_available') {
                                $r3[$institutionCode] = '0';
                                break;
                            } elseif ($val['statusfield'] == 'lendable_borrowed') {
                                $r3[$institutionCode] = '1';
                            }
                        }
                    }
                }
                $r = array_merge($r, $r3);
            }
        } else {
            $konkordanzTabelle = $this->libraryNetworkLookup;
            if (array_key_exists($idls, $konkordanzTabelle)) {
                $idls = $konkordanzTabelle[$idls];
                $r = $this->availabilityHelper
                    ->getAvailabilityByLibraryNetwork(
                        $sysNr, $idls, $userLocale
                    );
            }
        }
        if (!$r) {
            $r = [];
        }
        return $r;
    }

    /**
     * Get the Hierarchy Type (default if none)
     *
     * @return string
     */
    public function getHierarchyType()
    {
        if (isset($this->fields['hierarchy_top_id'])
            || isset($this->fields['hierarchytype'])
        ) {
            $hierarchyType = isset($this->fields['hierarchytype'])
                ? $this->fields['hierarchytype'] : false;
            if (!$hierarchyType) {
                $hierarchyType = isset($this->mainConfig->Hierarchy->driver)
                    ? $this->mainConfig->Hierarchy->driver : false;
            }
            return $hierarchyType;
        }
        return $this->mainConfig->Hierarchy->driver;
    }

    /**
     * Get marc field
     *
     * @param Integer $index Index
     *
     * @return \File_MARC_Data_Field|Boolean
     */
    protected function getMarcField($index)
    {
        $index = sprintf('%03d', $index);

        return $this->getMarcRecord()->getField($index);
    }

    /**
     * Get marc fields
     * Multiple values are possible for the field
     *
     * @param Integer $index Index
     *
     * @return \File_MARC_Data_Field[]|\File_MARC_List
     */
    protected function getMarcFields($index)
    {
        $index = sprintf('%03d', $index);

        return $this->getMarcRecord()->getFields($index);
    }

    /**
     * Get items of a field as named map (array)
     * Use this method if the field is (N)ot(R)epeatable
     *
     * @param Integer $index    Index
     * @param array   $fieldMap FieldMap
     *
     * @return array
     */
    protected function getMarcSubFieldMap($index, array $fieldMap)
    {
        $index = sprintf('%03d', $index);
        $subFieldValues = [];
        $field = $this->getMarcRecord()->getField($index);

        if ($field) {
            $subFieldValues = $this->getMappedFieldData($field, $fieldMap);
        }

        return $subFieldValues;
    }

    /**
     * Get items of a field (which exists multiple times) as named map (array)
     * Use this method if the field is (R)epeatable
     *
     * @param Integer $index             Index
     * @param Array   $fieldMap          FieldMap
     * @param Boolean $includeIndicators IncludeIndicators
     *
     * @return Array[]
     */
    protected function getMarcSubFieldMaps($index, array $fieldMap,
        $includeIndicators = true
    ) {
        $subFieldsValues = [];
        $fields = $this->getMarcRecord()->getFields($index);

        foreach ($fields as $field) {
            $subFieldsValues[] = $this->getMappedFieldData(
                $field, $fieldMap, $includeIndicators
            );
        }

        return $subFieldsValues;
    }

    /**
     * Convert sub fields to array map
     *
     * @param \File_MARC_Data_Field $field             Field
     * @param Array                 $fieldMap          FieldMap
     * @param Boolean               $includeIndicators Add the two indicators to the
     *                                                 field list
     *
     * @return Array
     */
    protected function getMappedFieldData($field, array $fieldMap,
        $includeIndicators = true
    ) {
        $subFieldValues = [];

        if ($includeIndicators) {
            $subFieldValues['@ind1'] = $field->getIndicator(1);
            $subFieldValues['@ind2'] = $field->getIndicator(2);
        }

        foreach ($fieldMap as $code => $name) {
            if (substr($code, 0, 1) === '_') { // Underscore means repeatable
                $code = substr($code, 1); // Remove underscore
                /**
                 * Subfields
                 *
                 * @var \File_MARC_Subfield[] $subFields
                 */
                $subFields = $field->getSubfields((string)$code);

                if (sizeof($subFields)) {
                    //$subFieldValues[$name] = array();
                    $i = 1;
                    foreach ($subFields as $subField) {
                        $subFieldValues[$i . $name] = $subField->getData();
                        $i++;
                    }
                }
            } else { // Normal single field
                $subField = $field->getSubfield((string)$code);

                if ($subField) {
                    $subFieldValues[$name] = $subField->getData();
                }
            }
        }

        return $subFieldValues;
    }

    /**
     * Get fields data without mapping. Keep original order of subfields
     *
     * @param Integer $index Index
     *
     * @return Array[]
     */
    protected function getMarcSubfieldsRaw($index)
    {
        /**
         * Fields
         *
         * @var \File_MARC_Data_Field[] $fields
         */
        $fields = $this->getMarcRecord()->getFields($index);
        $fieldsData = [];

        foreach ($fields as $field) {
            $tempFieldData = [];

            /**
             * Subfields
             *
             * @var \File_MARC_Subfield[] $subfields
             */
            $subfields = $field->getSubfields();

            foreach ($subfields as $subfield) {
                $tempFieldData[] = [
                    'tag' => $subfield->getCode(),
                    'data' => $subfield->getData()
                ];
            }

            $fieldsData[] = $tempFieldData;
        }

        return $fieldsData;
    }

    /**
     * Get value of a sub field
     *
     * @param Integer $index        Index
     * @param String  $subFieldCode SubFieldCode
     *
     * @return String|Boolean
     */
    protected function getSimpleMarcSubFieldValue($index, $subFieldCode)
    {
        $field = $this->getMarcField($index);

        if ($field) {
            $subField = $field->getSubfield($subFieldCode);

            if ($subField) {
                return $subField->getData();
            }
        }

        return false;
    }

    /**
     * Get value of a field
     *
     * @param Integer $index Index
     *
     * @return String|Boolean
     */
    protected function getSimpleMarcFieldValue($index)
    {
        /**
         * Field
         *
         * @var \File_MARC_Control_Field $field
         */
        $field = $this->getMarcField($index);

        return $field ? $field->getData() : false;
    }

    /**
     * Get initialized holdings helper
     *
     * @return HoldingsHelper
     */
    protected function getHoldingsHelper()
    {
        if ($this->holdingsHelper->getItemId() == null) {
            $holdingsData = isset($this->fields['holdings']) ?
                $this->fields['holdings'] : '';
            $this->holdingsHelper->setData($this->getUniqueID(), $holdingsData);
        }
        return $this->holdingsHelper;
    }

    /**
     * Get translator
     *
     * @return Translator
     */
    /*
    protected function getTranslator()
    {
        return $this->getServiceLocator()->get('VuFind/Translator');
    }
    */

    /**
     * Get stop words from 909 fields
     *
     * @return String[]
     */
    public function getLocalCodes()
    {
        $localCodes = [];
        $fieldsValues = $this->getMarcSubFieldMaps(
            909, [
            'a' => 'a',
            'b' => 'b',
            'c' => 'c',
            'd' => 'd',
            'e' => 'e',
            'f' => 'f',
            'g' => 'g',
            'h' => 'h',
            ]
        );

        foreach ($fieldsValues as $fieldValues) {
            foreach ($fieldValues as $fieldName => $fieldValue) {
                if (strpos($fieldName, '@') !== 0) {
                    $localCodes[] = $fieldValue;
                }
            }
        }

        return $localCodes;
    }

    /**
     * Get highlighted fulltext
     *
     * @return String
     */
    public function getHighlightedFulltext()
    {
        // Don't check for highlighted values if highlighting is disabled:
        if (!$this->highlight) {
            return '';
        }

        return (isset($this->highlightDetails['fulltext'][0])) ?
            trim($this->highlightDetails['fulltext'][0]) : '';
    }

    /**
     * Get table of content
     * This method is used to check whether data for tab is available and the
     * tab should be displayed
     * Differs functionally from parent as we display more information in toc.phtml
     *
     * @return String[]
     */
    public function getTOC()
    {
        return $this->getTableOfContent() + $this->getContentSummary();
    }

    /**
     * Get table of content
     * From fields 505.g.r.t
     * The combination of the lines of defined by the order of the fields
     * Possible combinations:
     * - $g. $t / $r
     * - $g. $t
     * - $g. $r
     * - $t. $r
     * - $t
     * - $r
     *
     * Use the content of the $debugLog if something seems wrong
     *
     * @return String[]
     */
    public function getTableOfContent()
    {
        $lines = [];
        $fieldsData = $this->getMarcSubfieldsRaw(505);
        $debugLog = [];

        foreach ($fieldsData as $fieldIndex => $field) {
            $maxIndex = sizeof($field) - 1;
            $index = 0;

            while ($index <= $maxIndex) {
                $hasNext = isset($field[$index + 1]);
                $hasTwoNext = isset($field[$index + 2]);
                $currentTag = $field[$index]['tag'];
                $currentData = $field[$index]['data'];
                $nextTag = $hasNext ? $field[$index + 1]['tag'] : null;
                $nextData = $hasNext ? $field[$index + 1]['data'] : null;
                $twoNextTag = $hasTwoNext ? $field[$index + 2]['tag'] : null;
                $twoNextData = $hasTwoNext ? $field[$index + 2]['data'] : null;

                if ($currentTag === 'g') {
                    if ($hasNext) {
                        if ($nextTag === 't') {
                            if ($hasTwoNext && $twoNextTag === 'r') { // $g. $t / $r
                                $lines[] = $currentData . '. ' . $nextData . ' / ' .
                                    $twoNextData;
                                $debugLog[$fieldIndex][] = $index . ' | $g. $t / $r';
                                $index += 3;
                            } else { // $g. $t
                                $lines[] = $currentData . '. ' . $nextData;
                                $debugLog[$fieldIndex][] = $index . ' | $g. $t';
                                $index += 2;
                            }
                        } elseif ($nextTag === 'r') { // $g. $r
                            $lines[] = $currentData . '. ' . $nextData;
                            $debugLog[$fieldIndex][] = $index . ' | $g. $r';
                            $index += 2;
                        } else {
                            // unknown order
                            $debugLog[$fieldIndex][] = $index . ' | unknown order';
                            $index += 1;
                        }
                    }
                    $index++;
                } elseif ($currentTag === 't') {
                    if ($hasNext) {
                        if ($nextTag === 'r') { // $t / $r
                            $lines[] = $currentData . ' / ' . $nextData;
                            $debugLog[$fieldIndex][] = $index . ' | $t / $r';
                            $index += 2;
                        } else { // $t
                            $lines[] = $currentData;
                            $debugLog[$fieldIndex][] = $index . ' | $t';
                            $index += 1;
                        }
                    } else { // $t
                        $lines[] = $currentData;
                        $debugLog[$fieldIndex][] = $index . ' | $t';
                        $index += 1;
                    }
                } elseif ($currentTag === 'r') { // $r
                    $lines[] = $currentData;
                    $debugLog[$fieldIndex][] = $index . ' | $r';
                    $index += 1;
                } elseif ($currentTag === 'a') { // $a
                    $lines[] = $currentData;
                    $index += 1;
                } else {
                    // unknown order
                    $debugLog[$fieldIndex][] = $index . ' | unknown order';
                    $index += 1;
                }
            }
        }

        return $lines;
    }

    /**
     * Get content summary
     * From fields 520
     *
     * @return String[]
     */
    public function getContentSummary()
    {
        $lines = [];
        $summary = $this->getMarcSubFieldMaps(
            520, [
            'a' => 'summary',
            'b' => 'expansion',
            ], false
        );

        // Copy into simple list
        foreach ($summary as $item) {
            if (isset($item['expansion'])) {
                $lines[] = $item['summary'] . '. ' . $item['expansion'];
            } else {
                $lines[] = $item['summary'];
            }
        }

        return $lines;
    }

    /**
     * Get last indexed date string for sorting
     *
     * @return String
     */
    public function getLastIndexed()
    {
        return isset($this->fields['time_indexed']) ?
            $this->fields['time_indexed'] : '';
    }

    /**
     * Returns the array element for the 'getAllRecordLinks' method
     *
     * @param File_MARC_Data_Field $field Field to examine
     *
     * @return array|bool                 Array on success, boolean false if no
     * valid link could be found in the data.
     */
    protected function getFieldData($field)
    {
        // Make sure that there is a t field to be displayed:
        if ($title = $field->getSubfield('t')) {
            $title = $title->getData();
        } else {
            return false;
        }

        $linkTypeSetting = isset($this->mainConfig->Record->marc_links_link_types)
            ? $this->mainConfig->Record->marc_links_link_types
            : 'id,oclc,dlc,isbn,issn,title';
        $linkTypes = explode(',', $linkTypeSetting);
        $linkFields = $field->getSubfields('w');

        // Run through the link types specified in the config.
        // For each type, check field for reference
        // If reference found, exit loop and go straight to end
        // If no reference found, check the next link type instead
        foreach ($linkTypes as $linkType) {
            switch (trim($linkType)) {
            case 'oclc':
                foreach ($linkFields as $current) {
                    if ($oclc = $this->getIdFromLinkingField($current, 'OCoLC')) {
                        $link = ['type' => 'oclc', 'value' => $oclc];
                    }
                }
                break;
            case 'dlc':
                foreach ($linkFields as $current) {
                    if ($dlc = $this->getIdFromLinkingField($current, 'DLC', true)) {
                        $link = ['type' => 'dlc', 'value' => $dlc];
                    }
                }
                break;
            // id : swissbib specific case of swissbib ID in subfield 9
            case 'id':
                if ($bibID = $field->getSubfield('9')) {
                    $link = [
                        'type'  => 'bib',
                        'value' => trim($bibID->getData()),
                    ];
                }
                break;
            // ctrlnum : swissbib specific case of local system numbers in
            // subfield w, remove enclosing parentheses of source code
            case 'ctrlnum':
                foreach ($linkFields as $current) {
                    if (preg_match(
                        '/\(([^)]+)\)(.+)/',
                        $current->getData(),
                        $matches
                    )
                    ) {
                        $link = [
                            'type' => 'ctrlnum',
                            'value' => $matches[1] . $matches[2],
                            ];
                    }
                }
                break;
            case 'isbn':
                if ($isbn = $field->getSubfield('z')) {
                    $link = [
                        'type' => 'isn', 'value' => trim($isbn->getData()),
                        'exclude' => $this->getUniqueId()
                    ];
                }
                break;
            case 'issn':
                if ($issn = $field->getSubfield('x')) {
                    $link = [
                        'type' => 'isn', 'value' => trim($issn->getData()),
                        'exclude' => $this->getUniqueId()
                    ];
                }
                break;
            case 'title':
                $link = ['type' => 'title', 'value' => $title];
                break;
            }
            // Exit loop if we have a link
            if (isset($link)) {
                break;
            }
        }
        // Make sure we have something to display:
        return !isset($link) ? false : [
            'title' => $this->getRecordLinkNote($field),
            'value' => $title,
            'link'  => $link
        ];
    }

    /**
     * Get HierarchyPositionsInParents
     *
     * @inheritDoc
     *
     * @note Prevent php error for invalid index data. parent_id and sequence should
     *       contain the same amount of values which correspond
     *
     * @return Array
     */
    public function getHierarchyPositionsInParents()
    {
        if (isset($this->fields['hierarchy_parent_id'])
            && isset($this->fields['hierarchy_sequence'])
        ) {
            if (sizeof($this->fields['hierarchy_parent_id']) > sizeof(
                $this->fields['hierarchy_sequence']
            )
            ) {
                $this->fields['hierarchy_parent_id'] = array_slice(
                    $this->fields['hierarchy_parent_id'],
                    0,
                    sizeof($this->fields['hierarchy_sequence'])
                );
            }
        }

        return parent::getHierarchyPositionsInParents();
    }

    /**
     * Get UseMostSpecificFormat
     *
     * @return Boolean
     */
    public function getUseMostSpecificFormat()
    {
        return $this->useMostSpecificFormat;
    }

    /**
     * Set UseMostSpecificFormat
     *
     * @param Boolean $useMostSpecificFormat UseMostSpecificFormat
     *
     * @return void
     */
    public function setUseMostSpecificFormat($useMostSpecificFormat)
    {
        $this->useMostSpecificFormat = (bool)$useMostSpecificFormat;
    }

    /**
     * Has Description
     *
     * @return Boolean
     */
    public function hasDescription()
    {
        foreach ($this->partsOfDescription as $descriptionElement) {
            $method = 'get' . $descriptionElement;
            if (method_exists($this, $method)) {
                $result = $this->$method();
                if (!empty($result)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns true if the record supports real-time AJAX status lookups.
     *
     * @return Boolean
     */
    public function supportsAjaxStatus()
    {
        return false;
    }

    /**
     * Get CitationFormat
     *
     * @override
     *
     * @return array Strings representing citation formats.
     */
    public function getCitationFormats()
    {
        return $this->solrDefaultAdapter->getCitationFormats();
    }

    /**
     * DisplayHoldings
     *
     * @return boolean
     */
    public function displayHoldings()
    {
        return true;
    }

    /**
     * DisplayLinks
     *
     * @return Boolean
     */
    public function displayLinks()
    {
        return false;
    }
}
