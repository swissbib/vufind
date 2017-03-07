<?php
/**
 * SolrDefaultAdapter to load
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
 * @package  View_Helper
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\View\Helper;

use Zend\I18n\Translator\TranslatorInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * FormatRelatedEntry
 *
 * @category Swissbib_VuFind2
 * @package  RecordDrivers
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
class FormatRelatedEntries extends AbstractHelper
{
    /**
     * Translator
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator Translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Format relatedEntry element
     *
     * @param array $relatedEntries Related Corporations and Persons
     *                              [
     *                                  'persons' => [..],
     *                                  'corporations110' => [...],
     *                                  'corporations' => [..]
     *                              ]
     *
     * @return array
     */
    public function __invoke(array $relatedEntries)
    {
        $formattedEntries = [];

        foreach ($relatedEntries['persons100'] as $relatedPerson) {
            $formattedEntries[] = $this->formatRelatedPerson($relatedPerson);
        }

        foreach ($relatedEntries['persons'] as $relatedPerson) {
            $formattedEntries[] = $this->formatRelatedPerson($relatedPerson);
        }

        foreach ($relatedEntries['corporations110'] as $relatedCorporation) {
            $formattedEntries[]
                = $this->formatRelatedCorporation($relatedCorporation);
        }

        foreach ($relatedEntries['corporations'] as $relatedCorporation) {
            $formattedEntries[]
                = $this->formatRelatedCorporation($relatedCorporation);
        }

        return $formattedEntries;
    }

    /**
     * Formats a related person entry
     *
     * @param array $relatedPerson RelatedPerson Array
     *
     * @return string
     */
    protected function formatRelatedPerson(array $relatedPerson)
    {
        $formattedEntry = '';
        $translatedRelatorCode = $this->translator->translate(
            'relator_' . $relatedPerson['relator_code'], 'relators'
        );

        if (isset($relatedPerson['name'])) {
            $formattedEntry = $relatedPerson['name'];
        }

        if (isset($relatedPerson['forename'])) {
            $formattedEntry .= ', ' . $relatedPerson['forename'];
        }

        if (isset($relatedPerson['1titles'])) {
            $formattedEntry .= ' ' . $relatedPerson['1titles'];
        }

        if (isset($relatedPerson['dates'])) {
            $formattedEntry .= ' (' . $relatedPerson['dates'] . ')';
        }

        $formattedEntry .= ' (' . $translatedRelatorCode . ')';

        return $formattedEntry;
    }

    /**
     * Formats a related corporation entry
     *
     * @param array $relatedCorporation RelatedCorporation Array
     *
     * @return string
     */
    protected function formatRelatedCorporation(array $relatedCorporation)
    {
        $formattedEntry = '';
        $translatedRelatorCode = $this->translator->translate(
            'relator_' . $relatedCorporation['relator_code'], 'relators'
        );

        if (isset($relatedCorporation['name'])) {
            $formattedEntry = $relatedCorporation['name'];
        }

        if (isset($relatedCorporation['1unit'])) {
                $formattedEntry .= '. ' . $relatedCorporation['1unit'];
        }

        $formattedEntry .= ' (' . $translatedRelatorCode . ')';

        return $formattedEntry;
    }
}
