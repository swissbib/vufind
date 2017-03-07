<?php
/**
 * EbooksOnDemand
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

use Swissbib\RecordDriver\SolrMarc;

/**
 * Build ebook links depending on institution configuration
 * Config in config_base.ini[eBooksOnDemand]
 *
 * @category Swissbib_VuFind2
 * @package  RecordDriver_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class EbooksOnDemand extends EbooksOnDemandBase
{
    /**
     * Check whether A100 item is valid for EOD link
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isValidForLinkA100(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        $institutionCode    = $item['institution_chb'];
        $publishYear        = $recordDriver->getPublicationDates();
        $itemFormats        = $recordDriver->getMostSpecificFormat();

        return $this->isYearInRange($institutionCode, $publishYear)
                && $this->isSupportedInstitution($institutionCode)
                && $this->isSupportedFormat($institutionCode, $itemFormats)
                && $this->hasStopWords(
                    $institutionCode, $recordDriver->getLocalCodes()
                ) === false;
    }

    /**
     * Build EOD link for A100 item
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return String
     */
    protected function buildLinkA100(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        $linkPattern = $this->getLinkPattern($item['institution_chb']);
        if ($item['network'] === 'HAN') :
                $data = [
                'SID' => 'dsv05',
                'SYSID' => str_replace('HAN', '', $item['bibsysnumber']),
                'INSTITUTION' => urlencode(
                    $item['institution_chb'] . $item['signature']
                ),
                'LANGUAGE' => $this->getConvertedLanguage()
                ];
            else:
                $data = [
                    'SID' => 'chb',
                'SYSID' => $item['bibsysnumber'],
                'INSTITUTION' => urlencode(
                    $item['institution_chb'] . $item['signature']
                ),
                'LANGUAGE' => $this->getConvertedLanguage()
                ];
            endif;
            return $this->templateString($linkPattern, $data);
    }

    /**
     * Check whether B400 item is valid for EOD link
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isValidForLinkB400(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        // Works the same way, just forward to A100. But use B400 as institution code
        return $this->isValidForLinkA100($item, $recordDriver, $holdingsHelper);
    }

    /**
     * Build EOD link for B400 item
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return String
     */
    protected function buildLinkB400(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        // Works the same way, just forward to A100. But use B400 as institution code
        return $this->buildLinkA100($item, $recordDriver, $holdingsHelper);
    }

    /**
     * Check whether B404 item is valid for EOD link
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isValidForLinkB404(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        // Works the same way, just forward to A100. But use B404 as institution code
        return $this->isValidForLinkA100($item, $recordDriver, $holdingsHelper);
    }

    /**
     * Build EOD link for B404 item
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return String
     */
    protected function buildLinkB404(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        $linkPattern = $this->getLinkPattern($item['institution_chb']);
        $data = [
            'SID' => 'chb',
            'SYSID' => $item['bibsysnumber'],
            'CALLNUM' => urlencode($item['signature']),
            'LANGUAGE' => $this->getConvertedLanguage()
        ];
        return $this->templateString($linkPattern, $data);
    }

    /**
     * Check whether B415 item is valid for EOD link
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isValidForLinkB415(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        // Works the same way, just forward to A100. But use B415 as institution code
        return $this->isValidForLinkA100($item, $recordDriver, $holdingsHelper);
    }

    /**
     * Build EOD link for B415 item
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return String
     */
    protected function buildLinkB415(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        // Works the same way, just forward to B404. But use B415 as institution code
        return $this->buildLinkB404($item, $recordDriver, $holdingsHelper);
    }

    /**
     * Check whether Z01 item is valid for EOD link
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isValidForLinkZ01(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        return $this->isValidForLinkA100($item, $recordDriver, $holdingsHelper);
    }

    /**
     * Build EOD link for Z01 item
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return String
     */
    protected function buildLinkZ01(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        $linkPattern    = $this->getLinkPattern($item['institution_chb']);
        $data    = [
            'SYSID'        => $item['bibsysnumber'],
            'CALLNUM'    => urlencode($item['signature'])
        ];

        return $this->templateString($linkPattern, $data);
    }

    /**
     * Check whether Z01 item is valid for EOD link
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isValidForLinkZ07(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        return $this->isValidForLinkA100($item, $recordDriver, $holdingsHelper);
    }

    /**
     * Build EOD link for Z07 item
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return String
     */
    protected function buildLinkZ07(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        $linkPattern = $this->getLinkPattern($item['institution_chb']);
        $data = [
            'SYSID' => $item['bibsysnumber'],
            'CALLNUM' => urlencode($item['signature'])
        ];

        return $this->templateString($linkPattern, $data);
    }

    /**
     * Check whether AX005 item is valid for EOD link
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isValidForLinkAX5(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        $institutionCode    = $item['institution_chb'];
        $publishYear        = $recordDriver->getPublicationDates();
        $itemFormats        = $recordDriver->getFormatsRaw();

        return $this->isYearInRange($institutionCode, $publishYear)
               && $this->isSupportedInstitution($institutionCode)
               && $this->isSupportedFormat($institutionCode, $itemFormats)
               && $this->hasStopWords(
                   $institutionCode, $recordDriver->getLocalCodes()
               ) === false; // no stop words
    }

    /**
     * Build EOD link for AX005 item
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return String
     */
    protected function buildLinkAX5(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        $linkPattern = $this->getLinkPattern($item['institution_chb']);
        $data = [
            'SYSID' => str_replace('vtls', '', $item['bibsysnumber']),
            'CALLNUM' => urlencode($item['signature']),
        ];

        return $this->templateString($linkPattern, $data);
    }

    /**
     * Check whether A125 item is valid for ordering
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return Boolean
     */
    protected function isValidForLinkA125(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        $institutionCode    = $item['institution_chb'];
        $publishYear        = $recordDriver->getPublicationDates();
        $itemFormats        = $recordDriver->getMostSpecificFormat();

        return $this->isYearInRange($institutionCode, $publishYear)
        && $this->isSupportedInstitution($institutionCode)
        && $this->isSupportedFormat($institutionCode, $itemFormats)
        && $this->hasStopWords(
            $institutionCode, $recordDriver->getLocalCodes()
        ) === false;
    }
    /**
     * Build order link for A125 item
     *
     * @param Array    $item           Item
     * @param SolrMarc $recordDriver   RecordDriver
     * @param Holdings $holdingsHelper HoldingsHelper
     *
     * @return String
     */
    protected function buildLinkA125(array $item, SolrMarc $recordDriver,
        Holdings $holdingsHelper
    ) {
        $linkPattern    = $this->getLinkPattern($item['institution_chb']);
        $form = $recordDriver->getLocalCodes();
        if (in_array('doksF', $form)) {
            $form = 'FV';
        } elseif (in_array('doksS', $form) || in_array('doksA', $form)) {
            $form = 'SA';
        } else {
            $form = 'PV';
        }
        $data    = [
            'FORM'       => $form,
            'CALLNUM'    => urlencode($item['signature']),
            'TITLE'      => urlencode(
                $recordDriver->getShortTitle() . '. ' .
                $recordDriver->getTitleSection()
            ),
        ];

        return $this->templateString($linkPattern, $data);
    }
}
