<?php
/**
 * Swissbib / VuFind swissbib enhancements
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 23.04.2015

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
 * @package  View_Helper
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @author   Oliver Schihin <oliver.schihin@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:record_drivers Wiki
 * @link     http://www.swissbib.org Project Wiki
 */
namespace Swissbib\View\Helper;

use VuFind\View\Helper\Root\Record as VuFindRecord;

/**
 * Build record links
 * Override related method to support ctrlnum type
 *
 * @category Swissbib_VuFind2
 * @package  View_Helper
 * @author   Nicolas Karrer <nkarrer@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Record extends VuFindRecord
{
    /**
     * Url Filter Configuration per theme
     *
     * Usage:
     * 'select' => [Field] => [
     *      'url' => List of subfields to extract url from (first none empty value)
     *      'desc' => List uf subfields to extract description from (url as fallback)
     *      'conditions' => 'Subfield | Regex', select if regex matches with subfield
     * ]
     * 'exclude' => [Field] => [
     *      'Subfield | Regex', exclude if regex matches with subfield, overwrites
     *                          selected urls
     * ]
     * 'mergeLinksByDescription', merges multiple links to one link when the
     *                            description matches the specified regex
     *
     * @var array
     */
    protected $urlFilter = [
        'sbvfrdsingle' => [
            'select' => [
                '950' => [
                    'url' => ['u'],
                    'desc' => ['z', '3'],
                    'conditions' => [
                        'P|^856$'
                    ]
                ],
                '956' => [
                    'url' => ['u'],
                    'desc' => ['y'],
                    'conditions' => []
                ]
            ],
            'exclude' => [
                '956' => [
                    'x|^VIEW && y|^Porträt',
                    'B|^ECOD',
                    'B|^SGBN && u|aleph.sg.ch',
                ],
                '950' => [
                    // we don't display doi links from National Licences,
                    // only the special url's with authentication
                    'B|^NATIONALLICENCE$',
                    'P|^856$ && u|stub.unibe.ch',
                ]
            ],
            'mergeLinksByDescription' => [
                '^Titelblatt und Inhaltsverzeichnis$',
                '^Inhaltsverzeichnis',
                '^Inhaltstext',
                '^download \(pdf\)',
                'opac.admin.ch'
            ]
        ],
        'sbvfrdmulti' => [
            'select' => [
                '950' => [
                    'url' => ['u'],
                    'desc' => ['z', '3'],
                    'conditions' => [
                        'B|^IDSBB$ && P|^856$',
                        'B|^SNL$ && P|^856$',
                        'B|^RETROS$ && P|^856$',
                        'B|^BORIS && P|^856$',
                        'B|^FREE && P|^856$',
                        'B|^HAN$ && P|^856$',
                        'B|^IDSSG$ && P|^856$ && z|^download \(pdf\)',
                        'B|^IDSSG$ && P|^856$ && u|edis.nsf',
                        'B|^NEBIS$ && P|^856$ && z|^Inhaltsverzeichnis',
                        'B|^NEBIS$ && P|^856$ && u|e-collection.ethbib.ethz.ch',
                        'P|^856$ && u|zora.uzh.ch'
                    ]
                ],
                '956' => [
                    'url' => ['u'],
                    'desc' => ['y'],
                    'conditions' => [
                        'B|^IDSBB$',
                        'B|^SNL$',
                        'B|^NEBIS$ && y|Inhaltsverzeichnis'
                    ]
                ]
            ],
            'exclude' => [
                '950' => [
                    'P|^856$ && u|stub.unibe.ch'
                ],
                '956' => [
                    'x|^VIEW && y|^Porträt',
                    'x|^VIEW && y|^Vorschau zum Bild'
                ]
            ],
            'mergeLinksByDescription' => [
                '^Inhaltsverzeichnis',
                '^Titelblatt und Inhaltsverzeichnis$',
                '^Inhaltstext',
                '^download \(pdf\)',
                'opac.admin.ch'
            ]
        ],
        'sbvfrdjus' => [
            'select' => [
                '950' => [
                    'url' => ['u'],
                    'desc' => ['z', '3'],
                    'conditions' => [
                        'P|^856$'
                    ]
                ],
                '956' => [
                    'url' => ['u'],
                    'desc' => ['y'],
                    'conditions' => []
                ]
            ],
            'exclude' => [
                '950' => [
                    'P|^856$ && u|stub.unibe.ch'
                ],
                '956' => [
                    'x|^VIEW && y|^Porträt',
                    'B|^ECOD',
                ]
            ],
            'mergeLinksByDescription' => [
                '^Titelblatt und Inhaltsverzeichnis$',
                '^Inhaltsverzeichnis',
                '^Inhaltstext',
                '^download \(pdf\)',
                'opac.admin.ch'
            ]
        ],
    ];

    /**
     * GetExtendedLinkDetails
     * get links for display according to view based configuration in $urlFilter
     *
     * @return array|null
     */
    public function getExtendedLinkDetails()
    {
        if (!isset($this->urlFilter[$this->config->Site->theme])
            || !($this->driver instanceof \VuFind\RecordDriver\SolrMarc)
        ) {
            return null;
        }

        $select = $this->urlFilter[$this->config->Site->theme]['select'];
        $exclude = $this->urlFilter[$this->config->Site->theme]['exclude'];
        $filteredLinks = [];

        foreach ($select as $field => $selectFieldConfig) {
            $driverFields = $this->driver->getMarcRecord()->getFields($field);

            if (!empty($driverFields)) {
                /**
                 * File MARC Data Field
                 *
                 * @var \File_MARC_Data_Field $marcDataField
                 */
                foreach ($driverFields as $marcDataField) {
                    $url = $this->getFirstSubfieldMatch(
                        $selectFieldConfig['url'], $marcDataField
                    );

                    if ($url === null) {
                        continue;
                    }

                    if (!$this->matchesConditions(
                        $selectFieldConfig['conditions'], $marcDataField
                    )
                        || isset($exclude[$field])
                        && $this->matchesConditions($exclude[$field], $marcDataField)
                    ) {
                        continue;
                    }

                    $desc = $this->getFirstSubfieldMatch(
                        $selectFieldConfig['desc'], $marcDataField
                    );

                    if ($desc === null) {
                        $desc = $url;
                    };

                    $filteredLinks[] = ['url' => $url, 'desc' => $desc];
                }
            }
        }

        return $this->mergeLinksByDescription(
            $this->createUniqueLinks($filteredLinks)
        );
    }

    /**
     * CreateUniqueLinks
     * Default: when $urlArray contains multiple links with identical URL strings
     * only the first will be kept.
     * Overwrite configuration in local config.ini if you want to display all URLs
     *
     * @param Array $urlArray Array of urls
     *
     * @return array
     */
    protected function createUniqueLinks($urlArray)
    {
        $urlArray = $this->getCorrectedURLS($urlArray);

        $config = $this->config->get('Record')->get('display_identical_urls');
        if ($config) {

            return $urlArray;
        } else {
            $uniqueURLs = [];
            $collectedArrays = [];
            foreach ($urlArray as $url) {
                if (!array_key_exists($url['url'], $uniqueURLs)) {
                    $uniqueURLs[$url['url']] = "";
                    $collectedArrays[] = $url;
                }
            }

            return $collectedArrays;
        }
    }

    /**
     * Get corrected URLs
     * changes content in URL, at the moment, just one case from helveticarchives
     *
     * @param Array $urlArray Array uf urls
     *
     * @return array
     */
    protected function getCorrectedURLS($urlArray)
    {
        $newUrlArray = [];

        foreach ($urlArray as $url) {
            $url['url'] = preg_replace(
                '/www\.helveticarchives\.ch\/getimage/',
                'www.helveticarchives.ch/bild',
                $url['url']
            );
            $newUrlArray[] = $url;
        }
        return $newUrlArray;
    }

    /**
     * Merges Links by their description
     *
     * @param Array $links Links
     *
     * @return array
     */
    protected function mergeLinksByDescription(array $links)
    {
        $urlFilterByTheme = $this->urlFilter[$this->config->Site->theme];

        if (empty($urlFilterByTheme['mergeLinksByDescription'])) {
            return $links;
        }

        $mergeLinksByDescription = $urlFilterByTheme['mergeLinksByDescription'];
        $filteredLinks = [];
        $preferredLinks = [];

        foreach ($links as $link) {
            $isPreferredLink = false;

            foreach ($mergeLinksByDescription as $index => $description) {
                if (preg_match('/' . $description . '/', $link['desc'])) {
                    $preferredLinks[$index] = $link;
                    $isPreferredLink = true;
                }
            }

            if (!$isPreferredLink) {
                $filteredLinks[] = $link;
            }
        }

        if (!empty($preferredLinks)) {
            ksort($preferredLinks);
            $filteredLinks[] = reset($preferredLinks);
        }

        return $filteredLinks;
    }

    /**
     * GetFirstSubfieldMatch
     *
     * @param array                 $fields        Fields
     * @param \File_MARC_Data_Field $marcDataField MarcDataField
     *
     * @return null|string
     */
    protected function getFirstSubfieldMatch(array $fields,
        \File_MARC_Data_Field $marcDataField
    ) {
        foreach ($fields as $field) {
            if ($marcDataField->getSubfield($field)) {
                return $marcDataField->getSubfield($field)->getData();
            }
        }

        return null;
    }

    /**
     * MatchesConditions
     *
     * @param array                 $conditions Conditions
     * @param \File_MARC_Data_Field $marcRecord MarcRecord
     *
     * @return bool
     */
    protected function matchesConditions(array $conditions,
        \File_MARC_Data_Field $marcRecord
    ) {
        if (empty($conditions)) {
            return true;
        }

        $matchesOr = false;
        $orConditionsCount = count($conditions);
        $i = 0;

        while (!$matchesOr && $i < $orConditionsCount) {
            $j = 0;
            $matchesAnd = true;
            $andConditions = explode('&&', $conditions[$i]);
            $andConditionsCount = count($andConditions);

            while ($matchesAnd && $j < $andConditionsCount) {
                list($subfieldKey, $subfieldValue)
                    = explode('|', $andConditions[$j]);
                $subfield = $marcRecord->getSubfield(trim($subfieldKey));

                $matchesAnd = $subfield && preg_match(
                    '/' . trim($subfieldValue) . '/', $subfield->getData()
                );

                $j++;
            }

            $matchesOr = $matchesOr || $matchesAnd;

            $i++;
        }

        return $matchesOr;
    }

    /**
     * GetSubtitle
     *
     * @param String $titleStatement TitleStatement
     *
     * @return string
     */
    public function getSubtitle($titleStatement)
    {
        $parts = $parts_amount = $parts_name = $title_remainder = null;

        if (isset($titleStatement['1parts_name'])) {
            $keys = array_keys($titleStatement);
            foreach ($keys as $key) {
                if (preg_match('/^[0-9]parts_name/', $key)) {
                    $parts_name[] = $titleStatement[$key];
                }
            }
            $parts_name = implode('. ', $parts_name);
        }

        if (isset($titleStatement['1parts_amount'])) {
            $keys = array_keys($titleStatement);
            foreach ($keys as $key) {
                if (preg_match('/^[0-9]parts_amount/', $key)) {
                    $parts_amount[] = $titleStatement[$key];
                }
            }
            $parts_amount = implode('. ', $parts_amount);
        }

        if ($parts_amount || $parts_name) {
            if ($parts_amount && $parts_name) {
                $parts = $parts_amount . '. ' . $parts_name;
            } elseif ($parts_amount) {
                $parts = $parts_amount;
            } elseif ($parts_name) {
                $parts = $parts_name;
            }
        }

        if (isset($titleStatement['title_remainder'])) {
            $title_remainder      = $titleStatement['title_remainder'];
        }

        if (!empty($title_remainder) && empty($parts)) {
            return $title_remainder;
        } elseif (!empty($title_remainder) && !empty($parts)) {
            return $parts . '. ' . $title_remainder;
        } elseif (empty($title_remainder) && !empty($parts)) {
            return $parts;
        }
    }

    /**
     * GetResponsible
     *
     * @param String                           $titleStatement TitleStatement
     * @param \VuFind\RecordDriver\SolrDefault $record         RecordDriver
     *
     * @return string
     */
    public function getResponsible($titleStatement, $record)
    {
        if ($record instanceof \VuFind\RecordDriver\Summon) {
            if ($record->getAuthor()) {

                return $record->getAuthor();
            }
        } else {
            if (isset($titleStatement['statement_responsibility'])) {

                return $titleStatement['statement_responsibility'];
            } elseif (($record->getPrimaryAuthor(true))
                && ($record->getSecondaryAuthors(true))
            ) {
                $primaryAuthor = $record->getPrimaryAuthor();
                $secondaryAuthors = implode('; ', $record->getSecondaryAuthors());
                return $primaryAuthor . '; ' . $secondaryAuthors;
            } elseif ($record->getPrimaryAuthor(true)) {

                return $record->getPrimaryAuthor();
            } elseif ($record->getSecondaryAuthors(true)) {

                return implode('; ', $record->getSecondaryAuthors());
            } elseif ($record->getCorporationNames(true)) {

                return implode('; ', $record->getCorporationNames());
            } else {

                return '';
            }
        }
    }

    /**
     * Generate a thumbnail URL (return false if unsupported).
     *
     * @param string $size Size of thumbnail (small, medium or large -- small is
     *                     default).
     *
     * @return string|bool
     */
    public function getThumbnail($size = 'small')
    {
        // Try to build thumbnail:
        $thumb = $this->driver->tryMethod('getThumbnail', [$size]);

        // Array?  It's parameters to send to the cover generator:
        if (is_array($thumb)) {

            if (!empty($this->config->Content->externalResourcesServer)) {
                $urlHelper = $this->getView()->plugin('url');
                $urlSrc = $urlHelper('cover-show');
                //sometimes our app is not the root domain
                $position =  strpos($urlSrc, '/Cover');

                return  $this->config->Content->externalResourcesServer .
                    substr($urlSrc, $position) . '?' . http_build_query($thumb);
            } else {
                $urlHelper = $this->getView()->plugin('url');

                return $urlHelper('cover-show') . '?' . http_build_query($thumb);
            }

        }

        // Default case -- return fixed string:
        return $thumb;
    }

    /**
     * GetTabVisibility
     *
     * @param string $tab Tab
     *
     * @return string
     */
    public function getTabVisibility($tab)
    {
        if (isset($this->config->RecordTabVisiblity->$tab)) {
            return $this->config->RecordTabVisiblity->$tab;
        };

        return '';
    }

    /**
     * GetOpenUrl
     *
     * @return string|null
     */
    public function getOpenUrl()
    {
        return $this->driver instanceof \VuFind\RecordDriver\Summon ?
            $this->driver->getOpenURL() : null;
    }

    /**
     * GetLink360
     *
     * @return string|null
     */
    public function getLink360()
    {
        return $this->driver instanceof \Swissbib\RecordDriver\Summon ?
            $this->driver->getLink() : null;
    }

    /**
     * GetLinkSFX
     *
     * @return string|null
     */
    public function getLinkSFX()
    {
        if (!($this->driver instanceof \VuFind\RecordDriver\Summon)) {
            return null;
        }

        $linkSFX = $this->view->openUrl($this->driver, 'results');

        $linkSFX_param = 'title = "' . $this->view->transEsc('articles.linkSFX') .
            '" target="_blank"';

        $renderedLink = str_replace(
            $this->view->transEsc('Get full text'), "SFX Services",
            $linkSFX->renderTemplate()
        );

        $renderedLink = str_replace(
            "<a ",
            "<a $linkSFX_param ",
            $renderedLink
        );

        $renderedLink = str_replace(
            'class="openUrl"', 'class="openUrl hidden"',
            $renderedLink
        );

        return $renderedLink;
    }
}
