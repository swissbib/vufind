<?php
/**
 * EbooksOnDemandBase
 *
 * PHP version 5
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  RecordDriver_Helper
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\RecordDriver\Helper;

use Zend\Config\Config;
use Zend\I18n\Translator\TranslatorInterface as Translator;

use Swissbib\RecordDriver\SolrMarc;

/**
 * Contains all general base methods for ebooks on demand handlings
 *
 * @category Swissbib_VuFind2
 * @package  RecordDriver_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
abstract class EbooksOnDemandBase extends CustomizedMethods
{
    /**
     * Translator
     *
     * @var Translator
     */
    protected $translator;

    /**
     * Initialize
     *
     * @param Config     $eBooksOnDemandConfig EbooksConfig
     * @param Translator $translator           Translator
     */
    public function __construct(Config $eBooksOnDemandConfig, Translator $translator)
    {
        parent::__construct($eBooksOnDemandConfig);

        $this->translator    = $translator;
    }

    /**
     * Get an ebooks on demand link for item depending on
     * custom checks and building methods
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return String|Boolean
     */
    public function getEbooksOnDemandLink(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        if ($this->isValidForLink($item, $recordDriver, $holdingsHelper)) {
            return $this->buildLink($item, $recordDriver, $holdingsHelper);
        }

        return false;
    }

    /**
     * Base method for link validity check
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isValidForLink(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        return $this->callMethod(
            'isValidForLink',
            $item['institution_chb'],
            [$item, $recordDriver, $holdingsHelper]
        );
    }

    /**
     * Base method to call custom link build methods for institutions
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return String
     */
    protected function buildLink(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        return $this->callMethod(
            'buildLink',
            $item['institution_chb'],
            [$item, $recordDriver, $holdingsHelper]
        );
    }

    /**
     * Check whether link is active for institution without custom implementation
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isValidForLinkBase(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        return false;
    }

    /**
     * Build link for all institutions which don't have a custom implementation
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean|String
     */
    protected function buildLinkBase(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        return false;
    }

    /**
     * Check whether year is in configured range
     * Support full and half ranges
     * Ex: 1500-1900, 1500-, -1900
     *
     * @param String $institutionCode InstitutionCode
     * @param Array  $yearArray       YearArray
     *
     * @return Boolean
     */
    protected function isYearInRange($institutionCode, $yearArray)
    {
        $dateType = array_shift($yearArray);
        preg_replace('/\D/', '9', $yearArray);
        $year1                        = intval($yearArray[0]);
        $year2                        = intval($yearArray[1]);
        $noSecondYear = 'se';

        if (stripos($noSecondYear, $dateType) !== false) {
            $year = $year1;
        } elseif ($year1 > $year2) {
            $year = $year1;
        } elseif ($year2 > $year1) {
            $year = $year2;
        } else {
            $year = $year1;
        }

        $customConfigKey = $institutionCode . '_range';

        if ($this->hasConfigValue($customConfigKey)) {
            $range = $this->config->get($customConfigKey);
        } elseif ($this->hasConfigValue('range')) {
            $range = $this->config->get('range');
        } else {
            $range = false;
        }

        if ($range !== false) {
            list($rangeStart, $rangeEnd) = array_map(
                'intval', explode('-', trim($range))
            );

            if ($rangeStart && $rangeStart > $year) {
                return false;
            }
            if ($rangeEnd && $rangeEnd < $year) {
                return false;
            }

            return true; // No check failed
        }

        return true; // No range at all
    }

    /**
     * Is institution supported
     * Check whether a link pattern is defined
     *
     * @param String $institutionCode InstitutionCode
     *
     * @return Boolean
     */
    protected function isSupportedInstitution($institutionCode)
    {
        $configKey = $institutionCode . '_link';

        return $this->hasConfigValue($configKey);
    }

    /**
     * Check whether formats are supported
     * The config only needs to contain the starting part of the format
     *
     * @param String   $institutionCode InstitutionCode
     * @param String[] $itemFormats     ItemFormats
     *
     * @return Boolean
     */
    protected function isSupportedFormat($institutionCode, array $itemFormats)
    {
        $customConfigKey = $institutionCode . '_formats';

        if (sizeof($itemFormats) === 0) {
            return false;
        }

        if ($this->hasConfigValue($customConfigKey)) {
            $configFormats  = $this->getConfigList($customConfigKey);
        } elseif ($this->hasConfigValue('formats')) {
            $configFormats = $this->getConfigList('formats');
        } else {
            $configFormats = false;
        }

        if ($configFormats !== false) {
            foreach ($configFormats as $configFormat) {
                foreach ($itemFormats as $itemFormat) {
                    if (stripos($itemFormat, $configFormat) === 0) {
                        return true; // Starts with
                    }
                }
            }

            return false; // None of the formats matched
        }

        return true; // No formats defined
    }

    /**
     * Get converted language
     * Language is current selection of user.
     * Converts are defined in config by lang_de = GER
     *
     * @return String
     */
    protected function getConvertedLanguage()
    {
        $userLanguage    = $this->translator->getLocale();

        return $this->hasConfigValue('lang_' . $userLanguage) ?
            $this->config->get('lang_' . $userLanguage) : 'GER';
    }

    /**
     * Check whether configured stop words are part of the compare string
     *
     * @param String   $institutionCode InstitutionCode
     * @param String[] $itemStopWords   ItemStopWords
     *
     * @return Boolean
     */
    protected function hasStopWords($institutionCode, array $itemStopWords)
    {
        $customConfigKey    = $institutionCode . '_stopwords';

        if ($this->hasConfigValue($customConfigKey)) {
            $configStopWords  = $this->getConfigList($customConfigKey);
        } elseif ($this->hasConfigValue('formats')) {
            $configStopWords = $this->getConfigList('formats');
        } else {
            $configStopWords = false;
        }

        if ($configStopWords !== false) {
            foreach ($configStopWords as $configStopWord) {
                if (in_array($configStopWord, $itemStopWords)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get link pattern
     *
     * @param String $institutionCode InstitutionCode
     *
     * @return String
     */
    protected function getLinkPattern($institutionCode)
    {
        return $this->getConfigValue($institutionCode . '_link');
    }
}
