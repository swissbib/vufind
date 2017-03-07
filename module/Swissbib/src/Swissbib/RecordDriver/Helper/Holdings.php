<?php
/**
 * Swissbib / VuFind: Helper class for swissbib holdings
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
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
 * @author   Oliver Schihin <oliver.schihin@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\RecordDriver\Helper;

use Zend\Config\Config;
use Zend\I18n\Translator\TranslatorInterface as Translator;

use VuFind\Crypt\HMAC;
use VuFind\ILS\Connection as IlsConnection;
use VuFind\Auth\Manager as AuthManager;
use VuFind\Auth\ILSAuthenticator as IlsAuth;
use VuFind\Config\PluginManager as ConfigManager;

use Swissbib\VuFind\ILS\Driver\Aleph;
use Swissbib\RecordDriver\SolrMarc;
use Swissbib\Log\Logger;

/**
 * Probably Holdings should be a subtype of ZF2 AbstractHelper at first I need a
 * better understanding how things are wired up in this case using means of ZF2
 *
 * @category Swissbib_VuFind2
 * @package  RecordDriver_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Holdings
{
    /**
     * Receive more data from server
     *
     * @var IlsConnection
     */
    protected $ils;

    /**
     * Check login status and info
     *
     * @var AuthManager
     */
    protected $authManager;

    /**
     * IlsAuth
     * 
     * @var IlsAuth
     */
    protected $ilsAuth;

    /**
     * Load configurations
     *
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * Holdings
     *
     * @var \File_MARC_Record
     */
    protected $holdings;

    /**
     * Parent item
     *
     * @var String
     */
    protected $idItem;

    /**
     * HMAC keys for ILS
     *
     * @var Array
     */
    protected $hmacKeys = [];

    /**
     * Map of fields to named params
     *
     * @var Array
     */
    protected $fieldMapping = [
        '0' => 'local_branch_expanded',
        '1' => 'location_expanded',
        '4' => 'holding_status',
        '6' => 'process_status',
        'a' => 'holding_information',
        'B' => 'network',
        'b' => 'institution',
        'C' => 'adm_code',
        'c' => 'location_code',
        'D' => 'bib_library',
        'E' => 'bibsysnumber',
        'F' => 'institution_chb',
        'j' => 'signature',
        'o' => 'staff_note',
        'p' => 'barcode',
        'q' => 'localid',
        'r' => 'sequencenumber',
        's' => 'signature2',
        'u' => 'holding_url',
        'y' => 'opac_note',
        'z' => 'public_note',
    ];

    /**
     * Map fieldId => delimiter, fields listed here get
     * concatenated using the delimiter
     *
     * @var Array
     */
    protected $concatenationMapping = [
        'z' => ' '
    ];

    /**
     * HoldingData
     *
     * @var Array[]|Boolean
     */
    protected $holdingData = false;

    /**
     * Holding structure without data
     *
     * @var Array[]|Boolean
     */
    protected $holdingStructure = false;

    /**
     * List of availabilities per item and barcode
     *
     * @var Array[]
     */
    protected $availabilities = [];

    /**
     * List of network domains and libs
     *
     * @var Array[]
     */
    protected $networks = [];

    /**
     * HoldingsConfig
     *
     * @var Config
     */
    protected $configHoldings;

    /**
     * HMAC
     *
     * @var HMAC
     */
    protected $hmac;

    /**
     * Mapping from institutions to groups
     *
     * @var Array
     */
    protected $institution2group = [];

    /**
     * GroupSorting
     *
     * @var Array
     */
    protected $groupSorting = [];

    /**
     * Translator
     *
     * @var Translator
     */
    protected $translator;

    /**
     * LocationMap
     *
     * @var LocationMap
     */
    protected $locationMap;

    /**
     * EbooksOnDemand
     *
     * @var EbooksOnDemand
     */
    protected $ebooksOnDemand;

    /**
     * Availability
     *
     * @var Availability
     */
    protected $availability;

    /**
     * BibCodeHelper
     *
     * @var BibCode
     */
    protected $bibCodeHelper;

    /**
     * Logger
     *
     * @var Logger
     */
    protected $swissbibLogger;

    /**
     * Initialize helper with dependencies
     *
     * @param IlsConnection  $ilsConnection  IlsConnection
     * @param HMAC           $hmac           Hmac
     * @param AuthManager    $authManager    AuthManager
     * @param IlsAuth        $ilsAuth        IlsAuth
     * @param ConfigManager  $configManager  ConfigManager
     * @param Translator     $translator     Translator
     * @param LocationMap    $locationMap    LocationMap
     * @param EbooksOnDemand $ebooksOnDemand EBooksOnDemand
     * @param Availability   $availability   Availability
     * @param BibCode        $bibCodeHelper  BibCodeHelper
     * @param Logger         $swissbibLogger Logger
     *
     * @throws \Exception
     */
    public function __construct(
        IlsConnection $ilsConnection,
        HMAC $hmac,
        AuthManager $authManager,
        IlsAuth $ilsAuth,
        ConfigManager $configManager,
        Translator $translator,
        LocationMap $locationMap,
        EbooksOnDemand $ebooksOnDemand,
        Availability $availability,
        BibCode $bibCodeHelper,
        Logger $swissbibLogger
    ) {
        $this->ils = $ilsConnection;
        $this->configManager = $configManager;
        $this->configHoldings = $configManager->get('Holdings');
        $this->hmac = $hmac;
        $this->authManager = $authManager;
        $this->ilsAuth = $ilsAuth;
        $this->translator = $translator;
        $this->locationMap = $locationMap;
        $this->ebooksOnDemand = $ebooksOnDemand;
        $this->availability = $availability;
        $this->bibCodeHelper = $bibCodeHelper;
        $this->swissbibLogger = $swissbibLogger;

        /**
         * Config
         *
         * @var Config $relationConfig
         */
        $relationConfig = $configManager->get('libadmin-groups');

        // Just ignore missing config to prevent a crashing frontend
        if ($relationConfig->count() !== null) {
            $this->institution2group = $relationConfig->institutions->toArray();
            $this->groupSorting = $relationConfig->groups->toArray();
        } elseif (APPLICATION_ENV == 'development') {
            throw new \Exception(
                'Missing config file libadmin-groups.ini. Run libadmin sync ' .
                'to solve this problem'
            );
        }

        $holdsIlsConfig = $this->ils->checkFunction('Holds');
        $this->hmacKeys = $holdsIlsConfig['HMACKeys'];

        $this->initNetworks();
    }

    /**
     * Initialize for item
     *
     * @param String $idItem      ItemId
     * @param String $holdingsXml HoldingsXml
     *
     * @return void
     */
    public function setData($idItem, $holdingsXml = '')
    {
        $this->idItem = $idItem;

        $this->setHoldingsContent($holdingsXml);
    }

    /**
     * Get holdings data
     *
     * @param SolrMarc $recordDriver    RecordDriver
     * @param String   $institutionCode InstitutionCode
     * @param Boolean  $extend          Extend
     *
     * @return Array[]|Boolean Contains lists for items and holdings
     *                         {items=>[],holdings=>[]}
     */
    public function getHoldings(SolrMarc $recordDriver, $institutionCode,
        $extend = true
    ) {
        if ($this->holdingData === false) {
            $this->holdingData = [];

            if ($this->hasItems()) {
                $this->holdingData['items'] = $this->getItemsData(
                    $recordDriver, $institutionCode, $extend
                );
            }
            if ($this->hasHoldings()) {
                $this->holdingData['holdings'] = $this->getHoldingData(
                    $recordDriver, $institutionCode, $extend
                );
            }
        }

        return $this->holdingData;
    }

    /**
     * Get holdings structure grouped by group and institution
     *
     * @return Array|\Array[]|bool
     */
    public function getHoldingsStructure()
    {
        if ($this->holdingStructure === false) {
            $structure949 = [];
            $structure852 = [];
            $holdingStructure = [];

            if ($this->hasItems()) {
                //$structure949 = $this->getStructuredHoldingsStructure(949);
                $holdingStructure = $this->getStructuredHoldingsStructure(
                    949, $holdingStructure
                );
            }
            if ($this->hasHoldings()) {
                //$structure852 = $this->getStructuredHoldingsStructure(852);
                $holdingStructure = $this->getStructuredHoldingsStructure(
                    852, $holdingStructure
                );
            }
            //using this method imposes problems in the context of institutions with
            //holdings and items at the same time the final data structure
            //$this->holdingsStructure doubles entries in the array
            //(for example label) which causes problems e.g. in conjunction
            //with the translation mechanism
            //and such datastructure is simply a mess
            //example id:299403270 (SNL)

            //$holdingStructure = array_merge_recursive($structure852,$structure949);

            $this->holdingStructure = $this->sortHoldings($holdingStructure);
        }

        return $this->holdingStructure;
    }

    /**
     * Sort holdings by group based on position in $this->groupSorting
     * Sort institutions based on position in institution2group
     *
     * @param Array $holdings Holdings
     *
     * @return Array
     */
    protected function sortHoldings(array $holdings)
    {
        $sortedHoldings = [];

        // Add holdings in sorted order
        foreach ($this->groupSorting as $groupCode) {
            if (isset($holdings[$groupCode])) {
                if (sizeof($this->institution2group)) {
                    $sortedInstitutions = [];

                    foreach ($this->institution2group as
                        $institutionCode => $groupCodeAgain
                    ) {
                        // @codingStandardsIgnoreStart
                        if (isset($holdings[$groupCode]['institutions'][$institutionCode])) {
                            $sortedInstitutions[$institutionCode] = $holdings[$groupCode]['institutions'][$institutionCode];
                        }
                        // @codingStandardsIgnoreEnd
                    }
                } else {
                    // No sorting available, just use available data
                    $sortedInstitutions = $holdings[$groupCode]['institutions'];
                }

                // Add group to sorted list
                $sortedHoldings[$groupCode] = $holdings[$groupCode];
                // Add sorted institution list
                $sortedHoldings[$groupCode]['institutions'] = $sortedInstitutions;

                // Remove group
                unset($holdings[$groupCode]);
            }
        }

        // Add all the others (missing data because of misconfiguration?)
        foreach ($holdings as $groupCode => $group) {
            $sortedHoldings[$groupCode] = $group;
        }

        return $sortedHoldings;
    }

    /**
     * Merge two arrays. Extend sub arrays or add missing elements,
     * but don't extend existing scalar values (as array_merge_recursive() does)
     *
     * @param Array $resultData ResultData
     * @param Array $newData    NewData
     *
     * @return Array
     */
    protected function mergeHoldings(array $resultData, array $newData)
    {
        foreach ($newData as $newKey => $newValue) {
            if (!isset($resultData[$newKey])) {
                $resultData[$newKey] = $newValue;
            } elseif (is_array($resultData[$newKey])) {
                $resultData[$newKey]
                    = $this->mergeHoldings($resultData[$newKey], $newValue);
            }
            // else = Already existing scalar value => ignore (keep first items data)
        }

        return $resultData;
    }

    /**
     * Initialize networks from config
     * (active only for Aleph)
     *
     * @return void
     */
    protected function initNetworks()
    {
        $networkNames = ['Aleph'];

        foreach ($networkNames as $networkName) {
            $configName = ucfirst($networkName) . 'Networks';

            /**
             * NetworkConfigs
             *
             * @var Config $networkConfigs
             */
            $networkConfigs = $this->configHoldings->get($configName);

            foreach ($networkConfigs as $networkCode => $networkConfig) {
                list($domain, $library) = explode(',', $networkConfig, 2);
                $this->networks[$networkCode] = [
                    'domain' => $domain,
                    'library' => $library,
                    'type' => $networkName,
                ];
            }
        }
    }

    /**
     * Set holdings structure
     *
     * @param String $holdingsXml HoldingsXml
     *
     * @return void
     *
     * @throws \File_MARC_Exception
     */
    protected function setHoldingsContent($holdingsXml)
    {
        if (is_string($holdingsXml) && strlen($holdingsXml) > 30) {
            $holdingsMarcXml = new \File_MARCXML(
                $holdingsXml, \File_MARCXML::SOURCE_STRING
            );
            $marcData = $holdingsMarcXml->next();

            if (!$marcData) {
                throw new \File_MARC_Exception('Cannot Process Holdings Structure');
            }

            $this->holdings = $marcData;
        } else {
            // Invalid input data. Currently just ignore it
            $this->holdings = false;
            $this->holdingData = [];
        }
    }

    /**
     * Get holding items for an institution
     *
     * @param SolrMarc $recordDriver    RecordDriver
     * @param String   $institutionCode InstitutionCode
     * @param Boolean  $extend          Extend
     *
     * @return Array Institution Items
     */
    protected function getItemsData(SolrMarc $recordDriver, $institutionCode,
        $extend = true
    ) {
        $fieldName = 949; // Field code for item information in holdings xml
        $institutionItems = $this->getHoldingsData(
            $fieldName, $this->fieldMapping, $institutionCode
        );

        if ($extend) {
            $allBarcodes = [];
            foreach ($institutionItems as $index => $item) {
                $institutionItems[$index] = $this->extendItem($item, $recordDriver);
                $networkCode = isset($item['network']) ? $item['network'] : '';
                if ($this->isAlephNetwork($networkCode)) {
                    if (!isset($extendingOptions['availability'])
                        || $extendingOptions['availability']
                    ) {
                        array_push($allBarcodes, $item['barcode']);
                    }
                }
            }

            if (sizeof($allBarcodes) > 0) {
                $institutionItems
                    = $this->getAvailabilityInfosArray(
                        $institutionItems,
                        $allBarcodes
                    );
            }

        }

        return $institutionItems;
    }

    /**
     * Add availability-information of multiple items
     *
     * @param Array $items    the (holding-/institution-)items
     * @param Array $barcodes barcodes to check availability for
     *
     * @return Array
     */
    public function getAvailabilityInfosArray($items, $barcodes)
    {
        // get availability info of items:
        $firstItem = $items[0];
        $allAvailabilities = '';
        if (0 < count($barcodes)) {
            $allAvailabilities = $this->getAvailabilityInfos(
                $firstItem['bibsysnumber'], $barcodes, $firstItem['bib_library']
            );
        }

        // write availability-info in items array:
        foreach ($items as $index => $item) {
            if (isset($allAvailabilities[$item['barcode']])) {
                $availabilityArray = [$item['barcode'] =>
                    $allAvailabilities[$item['barcode']]];
                $item['availability'] = $availabilityArray;
            }
            $items[$index] = $item;
        }

        return $items;
    }

    /**
     * Check whether network is supported
     *
     * @param String $networkCode Code of network
     *
     * @return Boolean
     */
    protected function isRestfulNetwork($networkCode)
    {
        return isset($this->configHoldings->Restful->{$networkCode})
            && $this->configHoldings->Restful->{$networkCode} == true ?: false;
    }

    /**
     * Extend item with additional informations
     *
     * @param Array    $item             Item
     * @param SolrMarc $recordDriver     RecordDriver
     * @param Array    $extendingOptions ExtendingOptions
     *
     * @return Array
     */
    public function extendItem(array $item, SolrMarc $recordDriver,
        array $extendingOptions = []
    ) {
        $item = $this->extendItemBasic($item, $recordDriver, $extendingOptions);
        $item = $this->extendItemIlsActions($item, $recordDriver, $extendingOptions);

        return $item;
    }

    /**
     * Extend item with basic infos
     * - Ebooks on Demand Link
     * - Location map
     *
     * @param Array    $item             Item
     * @param SolrMarc $recordDriver     RecordDriver
     * @param Array    $extendingOptions ExtendingOptions
     *
     * @return Array
     */
    protected function extendItemBasic(array $item, SolrMarc $recordDriver = null,
        array $extendingOptions = []
    ) {
        // EOD LINK
        if (!isset($extendingOptions['eod']) || $extendingOptions['eod']) {
            $item['eodlink'] = $this->getEODLink($item, $recordDriver);
        }

        // Location Map Link
        if (!isset($extendingOptions['map']) || $extendingOptions['map']) {
            $item['locationMap'] = $this->getLocationMapLink($item);
        }

        // Location label
        if (!isset($extendingOptions['location']) || $extendingOptions['location']) {
            $item['locationLabel'] = $this->getLocationLabel($item);
        }
        // Defaults to false, maybe ils will add more info
        $item['availability'] = false;

        if (!isset($extendingOptions['backlink']) || $extendingOptions['backlink']) {
            // @todo Hack um Exemplardarstellung von E-Books aus dem ERM zu erhalten
            // sollte auf Ebene der Daten gelöst werden
            if (!empty($item['holding_url']) && $item['network'] === 'IDSBB') {
                $item['network'] = 'SSERM';
            }
            if (isset($item['network']) && !$this->isRestfulNetwork($item['network'])
            ) {
                $item['backlink'] = $this->getBackLink(
                    $item['network'], strtoupper($item['institution']), $item
                );
            }
        }

        if (!isset($extendingOptions['institutionUrl'])
            || $extendingOptions['institutionUrl']
        ) {
            $bibInfoLink = $this->getBibInfoLink($item['institution']);
            $item['institutionUrl'] = $bibInfoLink['url'];
        }

        return $item;
    }

    /**
     * Extend item with action links based on ILS
     *
     * @param Array    $item             Item
     * @param SolrMarc $recordDriver     RecordDriver
     * @param Array    $extendingOptions ExtendingOptions
     *
     * @return Array
     */
    protected function extendItemIlsActions(array $item,
        SolrMarc $recordDriver = null, array $extendingOptions = []
    ) {
        $networkCode = isset($item['network']) ? $item['network'] : '';

        // Only add links for supported networks
        if ($this->isAlephNetwork($networkCode)) {
            if ($this->isRestfulNetwork($networkCode)) {
                // Add hold link for item
                if (!isset($extendingOptions['hold']) || $extendingOptions['hold']) {
                    $item['holdLink'] = $this->getHoldLink($item);
                }

                if (!isset($extendingOptions['actions'])
                    || $extendingOptions['actions']
                ) {
                    if ($this->isLoggedIn()) {
                        $item['userActions'] = $this->getAllowedUserActions($item);
                    } elseif (!$this->isLoggedIn()) {
                        $item['userActions'] = [
                            'login' => 'true',
                        ];
                    }
                }
            }
        }
        return $item;
    }

    /**
     * Extend holding with additional informations
     *
     * @param Array    $holding      Holding
     * @param SolrMarc $recordDriver RecordDriver
     *
     * @return Array
     */
    protected function extendHolding(array $holding, SolrMarc $recordDriver = null)
    {
        $holding = $this->extendHoldingBasic($holding, $recordDriver);
        $holding = $this->extendHoldingIlsActions($holding, $recordDriver);

        return $holding;
    }

    /**
     *  Extend holding with basic infos
     * - Location map
     *
     * @param Array    $holding      Holding
     * @param SolrMarc $recordDriver RecordDriver
     *
     * @return Array
     *
     * @todo Enable restful check after full features are implemented for holdings
     */
    protected function extendHoldingBasic(array $holding,
        SolrMarc $recordDriver = null
    ) {
        // Location Map Link
        $holding['locationMap'] = $this->getLocationMapLink($holding);
        // Location label
        $holding['locationLabel'] = $this->getLocationLabel($holding);

        // Add backlink for not restful networks
        // @note Disabled check until the
        //        if (!$this->isRestfulNetwork($holding['network'])) {
        $holding['backlink'] = $this->getBackLink(
            $holding['network'], strtoupper($holding['institution']), $holding
        );
        //        }

        $bibInfoLink = $this->getBibInfoLink($holding['institution']);
        $holding['institutionUrl'] = $bibInfoLink['url'];

        return $holding;
    }

    /**
     * Add action links to holding item
     *
     * @param Array    $holding      Holding
     * @param SolrMarc $recordDriver RecordDriver
     *
     * @return Array
     */
    protected function extendHoldingIlsActions(array $holding,
        SolrMarc $recordDriver = null
    ) {
        $networkCode = $holding['network'];

        if ($this->isAlephNetwork($networkCode)) {
            if ($this->isRestfulNetwork($networkCode)) {
                // no backlink for Restful enabled networks, either
                // actions are possible, or nothing
                unset($holding['backlink']);
                $bib = $holding['bib_library'];
                $resourceId = $bib . $holding['bibsysnumber'];
                $ilsDriver = $this->ils->getDriver();
                $itemsCount = $ilsDriver->getHoldingItemCount(
                    $resourceId, $holding['institution']
                );

                $holding['itemsLink'] = [
                    'count' => $itemsCount,
                    'resource' => $resourceId,
                    'institution' => $holding['institution'],
                    'url' => [
                        'record' => $this->idItem,
                        'institution' => $holding['institution'],
                        'resource' => $resourceId
                    ]
                ];
            }
        }

        return $holding;
    }

    /**
     * Build an EOD link if possible
     * Return false if item does not support EOD links
     *
     * @param Array    $item         Item
     * @param SolrMarc $recordDriver RecordDriver
     *
     * @return String|Boolean
     */
    protected function getEODLink(array $item, SolrMarc $recordDriver = null)
    {
        return $this->ebooksOnDemand ?
            $this->ebooksOnDemand->getEbooksOnDemandLink(
                $item, $recordDriver, $this
            ) : false;
    }

    /**
     * Build location map link
     * Return false in case institution is not enable for mapping
     *
     * @param Array $item Item
     *
     * @return String|Boolean
     */
    protected function getLocationMapLink(array $item)
    {
        return $this->locationMap ?
            $this->locationMap->getLinkForItem($this, $item) : false;
    }

    /**
     * Get location label
     * Try to translate. Fallback to index data
     *
     * @param Array $item Item
     *
     * @return String
     */
    protected function getLocationLabel(array $item)
    {
        $label = '';

        // Has informations with translation?
        if (isset($item['location_code'])
            && isset($item['institution'])
            && isset($item['network'])
        ) {
            // @todo keep strtolower or fix in tab40.sync
            $labelKey = strtolower(
                $item['institution'] . '_' . $item['location_code']
            );
            $textDomain = 'location-' . strtolower($item['network']);
            $translated = $this->translator->translate($labelKey, $textDomain);

            if ($translated !== $labelKey) {
                $label = $translated;
            }
        }

        // Use expanded label or code as fallback
        if (empty($label)) {
            if (isset($item['location_expanded'])) {
                $label = trim($item['location_expanded']);
            } elseif (isset($item['location_code'])) {
                $label = trim($item['location_code']);
            }
        }

        return $label;
    }

    /**
     * Get list of allowed actions for the current user
     *
     * @param Array $item Item
     *
     * @return Array
     */
    protected function getAllowedUserActions($item)
    {
        /**
         * IlsDriver
         *
         * @var Aleph $ilsDriver
         */
        $ilsDriver = $this->ils->getDriver();
        $patron = $this->getPatron();
        $source = $ilsDriver->getSource($patron['id']);
        $sourceConfiguration = $ilsDriver->getDriverConfig($source);

        $itemId = $item['bibsysnumber'] . $item['sequencenumber'];
        $bib = $item['bib_library'];
        $groupId = $this->buildItemId($item);

        $allowedActions = $ilsDriver->getAllowedActionsForItem(
            $patron['id'], $itemId, $groupId, $bib
        );
        $host = $sourceConfiguration['Catalog']['host'];

        if ($allowedActions['photorequest']) {
            $allowedActions['photoRequestLink']
                = $this->getPhotoRequestLink($host, $item);
        }

        if ($allowedActions['bookingrequest']) {
            $allowedActions['bookingRequestLink']
                = $this->getBookingRequestLink($host, $item);
        }

        return $allowedActions;
    }

    /**
     * Get link for external photocopy request
     *
     * @param String $host Host
     * @param Array  $item Item
     *
     * @return String
     */
    protected function getPhotoRequestLink($host, array $item)
    {
        $queryParams = [
            'func' => 'item-photo-request',
            'doc_library' => $item['adm_code'],
            'adm_doc_number' => $item['localid'],
            'item_sequence' => $item['sequencenumber'],
            'bib_doc_num' => $item['bibsysnumber'],
            'bib_library' => $item['bib_library'],
        ];

        return 'http://' . $host . '/F/?' . http_build_query($queryParams);
    }

    /**
     * Get link for external booking request
     *
     * @param String $host Host
     * @param Array  $item Item
     *
     * @return string
     */
    protected function getBookingRequestLink($host, array $item)
    {
        $queryParams = [
            'func' => 'booking-req-form-itm',
            'adm_library' => $item['adm_code'],
            'adm_doc_number' => $item['localid'],
            'adm_item_sequence' => $item['sequencenumber'],
        ];

        return 'http://' . $host . '/F/?' . http_build_query($queryParams);
    }

    /**
     * Check whether user is logged in
     *
     * @return Boolean
     */
    protected function isLoggedIn()
    {
        return $this->authManager->isLoggedIn() !== false;
    }

    /**
     * Get patron (catalog login data)
     *
     * @return Array
     */
    protected function getPatron()
    {
        return $this->ilsAuth->storedCatalogLogin();
    }

    /**
     * Check whether network uses aleph system
     *
     * @param String $network Network
     *
     * @return Boolean
     */
    public function isAlephNetwork($network)
    {
        return isset($this->networks[$network])
            ? $this->networks[$network]['type'] === 'Aleph' : false;
    }

    /**
     * Get a backlink
     * Check first if a custom type is defined for this network
     * (only Aleph is implemented as a custom type)
     * Fallback to network default
     *
     * @param String $networkCode     Code of network
     * @param String $institutionCode InstitutionCode
     * @param Array  $item            Item
     *
     * @return Boolean
     */
    protected function getBackLink($networkCode, $institutionCode, array $item)
    {
        //return it, if the item has an url included in subfield u
        if (!empty($item['holding_url'])) {
            return $item['holding_url'];
        }

        $method = false;
        $data = [];

        // Check if the network has its own backlink type
        if (isset($this->configHoldings->Backlink->{$networkCode})) {
            $method = 'getBackLink' . ucfirst($networkCode);
            $data = [
                'pattern' => $this->configHoldings->Backlink->{$networkCode}
            ];
            // no custom type for network
        } else {
            // check if network is even configured
            if (isset($this->networks[$networkCode])) {
                $networkType = strtoupper($this->networks[$networkCode]['type']);
                $method = 'getBackLink' . ucfirst($networkType);

                // Has the network type  general link by its
                // system (only Aleph is implemented)
                if (isset($this->configHoldings->Backlink->$networkType)) {
                    $data = [
                        'pattern' => $this->configHoldings->Backlink->$networkType
                    ];
                }
            }
        }

        // Merge in network data if available
        if (isset($this->networks[$networkCode])) {
            $data = array_merge($this->networks[$networkCode], $data);
        }

        // Is a matching method available?
        if ($method && method_exists($this, $method)) {
            return $this->{$method}($networkCode, $institutionCode, $item, $data);
        }

        return false;
    }

    /**
     * Get backlink for aleph
     * (custom method)
     *
     * @param String $networkCode     Code of network
     * @param String $institutionCode Code of institution
     * @param Array  $item            Item
     * @param Array  $data            Data
     *
     * @return String
     */
    protected function getBackLinkAleph($networkCode, $institutionCode, $item,
        array $data
    ) {
        $values = [
            'server' => $data['domain'],
            'bib-library-code' => $data['library'],
            'bib-system-number' => $item['bibsysnumber'],
            'aleph-sublibrary-code' => $institutionCode
        ];
        return $this->compileString($data['pattern'], $values);
    }

    /**
     * Get backlink for IDSBB
     * set link to orange view of swissbib
     *
     * @param String $networkCode     Code of network
     * @param String $institutionCode Code of institution
     * @param Array  $item            Item
     * @param Array  $data            Data
     *
     * @return String
     */
    protected function getBackLinkIDSBB($networkCode, $institutionCode, $item,
        array $data
    ) {
        $values = [
            'id' => $this->idItem,
            'sub-library-code' => $institutionCode,
            'network' => $networkCode,
            ];
        return $this->compileString($data['pattern'], $values);
    }

    /**
     * Get backlink for NEBIS
     * set link to NEBIS Primo View
     *
     * @param String $networkCode     Code of network
     * @param String $institutionCode Code of institution
     * @param Array  $item            Item
     * @param Array  $data            Data
     *
     * @return String
     *
    *protected function getBackLinkNEBIS($networkCode, $institutionCode, $item,
    *    array $data
    *)
    *{
    *    $values = [
    *        'bib-system-number' => $item['bibsysnumber'],
    *    ];
    *    return $this->compileString($data['pattern'], $values);
    *}
    */

    /**
     * Get backlink for IDSLU
     * set link to iluplus Primo View
     *
     * @param String $networkCode     Code of network
     * @param String $institutionCode Code of Institution
     * @param Array  $item            Item
     * @param Array  $data            Data
     *
     * @return String
     */
    protected function getBackLinkIDSLU($networkCode, $institutionCode, $item,
        array $data
    ) {
        $values = [
            'bib-system-number' => $item['bibsysnumber'],
        ];
        return $this->compileString($data['pattern'], $values);
    }

    /**
     * Get backlink for SGBN
     * set link to SGBN Primo View
     *
     * @param String $networkCode     Code of network
     * @param String $institutionCode Code of Institution
     * @param Array  $item            Item
     * @param Array  $data            Data
     *
     * @return String
     */
    protected function getBackLinkSGBN($networkCode, $institutionCode, $item,
        array $data
    ) {
        $values = [
            'bib-system-number' => $item['bibsysnumber'],
        ];
        return $this->compileString($data['pattern'], $values);
    }

    /**
     * Get back link for IDSSG (self-developed-non-aleph-request)
     * Currently only a wrapper for Aleph
     *
     * @param String $networkCode     Code of network
     * @param String $institutionCode Code of Institution
     * @param Array  $item            Item
     * @param Array  $data            Data
     *
     * @return String
     */
    protected function getBackLinkIDSSG($networkCode, $institutionCode, array $item,
        array $data
    ) {
        // differ between FH and PH:
        if ($institutionCode === 'HFHS') {
            $data['pattern'] = $this->configHoldings->Backlink->{'IDSSGFH'};
        } else if ($institutionCode === 'HPHS'
            || 'HPHS' == $institutionCode
            || 'HPHG' == $institutionCode
            || 'HPHRS' == $institutionCode
            || 'HPHRM' == $institutionCode
            || 'HRDZJ' == $institutionCode
            || 'HRDZS' == $institutionCode
            || 'HRDZW' == $institutionCode
            || 'HRPMA' == $institutionCode
            || 'HPHOL' == $institutionCode
        ) {
            $data['pattern'] = $this->configHoldings->Backlink->{'IDSSGPH'};
        }
        return $this->getBackLinkAleph($networkCode, $institutionCode, $item, $data);
    }

    /**
     * Get backlink for RERO
     *
     * @param String $networkCode     Code of network
     * @param String $institutionCode Code of Institution
     * @param Array  $item            Item
     * @param Array  $data            Data
     *
     * @return mixed
     */
    protected function getBackLinkRERO($networkCode, $institutionCode, $item,
        array $data
    ) {
        $values = [
            'language-code' => 'de', // @todo fetch from user,
            // third and fourth character
            'RERO-network-code' => (int)substr($institutionCode, 2, 2),
            'bib-system-number' => $item['bibsysnumber'],
            //removes the RE-characters from the number string
            'sub-library-code' => preg_replace('[\D]', '', $institutionCode)
        ];
        return $this->compileString($data['pattern'], $values);
    }

    /**
     * Get backlink for Alexandria network (Primo on Alma)
     * links only to result list as we have no usable identifier
     *
     * @param String $networkCode     Code of network
     * @param String $institutionCode Code of Institution
     * @param Array  $item            Item
     * @param Array  $data            Data
     *
     * @return String
     */
    protected function getBackLinkAlex($networkCode, $institutionCode, array $item,
        array $data
    ) {
        $values = [
            'bib-system-number' => $item['bibsysnumber']
        ];
        return $this->compileString($data['pattern'], $values);
    }

    /**
     * Get backlink for Renouvaud network (Primo on Alma)
     * links only to result list as we have no usable identifier
     *
     * @param String $networkCode     Code of network
     * @param String $institutionCode Code of Institution
     * @param Array  $item            Item
     * @param Array  $data            Data
     *
     * @return String
     */
    protected function getBackLinkVaud($networkCode, $institutionCode, array $item,
        array $data
    ) {
        $values = [
            'bib-system-number' => $item['bibsysnumber']
        ];
        return $this->compileString($data['pattern'], $values);
    }

    /**
     * Get backlink for SNL (helveticat)
     *
     * @param String $networkCode     Code of network
     * @param String $institutionCode Code of Institution
     * @param Array  $item            Item
     * @param Array  $data            Data
     *
     * @return String
     */
    protected function getBackLinkSNL($networkCode, $institutionCode, $item,
        array $data
    ) {
        $bibsysnumber = preg_replace('/^vtls0*/', '', $item['bibsysnumber']);
        $values = [
            'bib-system-number' => $bibsysnumber,
        ];
        return $this->compileString($data['pattern'], $values);
    }

    /**
     * Get backlink for CCSA (poster collection)
     *
     * @param String $networkCode     Code of network
     * @param String $institutionCode Code of Institution
     * @param Array  $item            Item
     * @param Array  $data            Data
     *
     * @return String
     */
    protected function getBackLinkCCSA($networkCode, $institutionCode, $item,
        array $data
    ) {
        $bibsysnumber = preg_replace('/^vtls0*/', '', $item['bibsysnumber']);
        $values = [
            'bib-system-number' => $bibsysnumber,
        ];
        return $this->compileString($data['pattern'], $values);
    }

    /**
     * Get backlink for Helveticarchives (SNL)
     *
     * @param String $networkCode     Code of network
     * @param String $institutionCode Code of Institution
     * @param Array  $item            Item
     * @param Array  $data            Data
     *
     * @return String
     */
    protected function getBackLinkCHARCH($networkCode, $institutionCode, array $item,
        array $data
    ) {
        $values = [
            'bib-system-number' => $item['bibsysnumber'],
        ];
        return $this->compileString($data['pattern'], $values);
    }

    /**
     * Compile string
     * Replace {varName} pattern with names and data from array
     * creates an URL string for backlinks according to data delivered
     * by methods above
     *
     * @param String $string    String
     * @param Array  $keyValues KeyValues
     *
     * @return String
     */
    protected function compileString($string, array $keyValues)
    {
        $newKeyValues = [];

        foreach ($keyValues as $key => $value) {
            $newKeyValues['{' . $key . '}'] = $value;
        }

        return str_replace(
            array_keys($newKeyValues), array_values($newKeyValues), $string
        );
    }

    /**
     * Get URL for library website (bibinfo)
     * false if not found or scheme not ok
     * Array with only url if scheme is ok
     *
     * @param String $institutionCode InstitutionCode
     *
     * @return Array|Boolean
     */
    protected function getBibInfoLink($institutionCode)
    {
        $bibInfoLink = $this->translator->translate($institutionCode, 'bibinfo');

        if ($bibInfoLink === $institutionCode) {
            $bibInfoLink = false;
        } else {
            $scheme = parse_url($bibInfoLink, PHP_URL_SCHEME);
            if (preg_match('/http/', $scheme)) {
                $bibInfoLink = [
                    'url' => $bibInfoLink,
                ];
            } else {
                $bibInfoLink = false;
            }
        }
        return $bibInfoLink;
    }

    /**
     * Get availability infos for item element
     *
     * @param String $sysNumber SysNumber
     * @param Array  $barcode   Array of BarCode Strings
     * @param String $bib       Bib
     *
     * @return Array|Boolean
     */
    protected function getAvailabilityInfos($sysNumber, $barcode, $bib)
    {
        $userLocale = $this->translator->getLocale();

        return $this->availability->getAvailability(
            $sysNumber, $barcode, $bib, $userLocale
        );
    }

    /**
     * Get circulation statuses for all elements of the item
     *
     * @param String $sysNumber SysNumber
     *
     * @return Array[]
     */
    protected function getItemCirculationStatuses($sysNumber)
    {
        $data = [];
        try {
            $circulationStatuses
                = $this->ils->getDriver()->getCirculationStatus($sysNumber);

            foreach ($circulationStatuses as $circulationStatus) {
                $data[$circulationStatus['barcode']] = $circulationStatus;
            }
        } catch (\Exception $e) {
            //todo: GH get logging service
        }

        return $data;
    }

    /**
     * Get structured data for holdings
     *
     * @param SolrMarc $recordDriver    RecordDriver
     * @param String   $institutionCode InstitutionCode
     * @param Boolean  $extend          Extend
     *
     * @return Array[]
     */
    protected function getHoldingData(SolrMarc $recordDriver, $institutionCode,
        $extend = true
    ) {
        $fieldName = 852; // Field code for item information in holdings xml
        $institutionHoldings = $this->getHoldingsData(
            $fieldName, $this->fieldMapping, $institutionCode
        );

        if ($extend) {
            foreach ($institutionHoldings as $index => $holding) {
                $institutionHoldings[$index]
                    = $this->extendHolding($holding, $recordDriver);
            }
        }

        return $institutionHoldings;
    }

    /**
     * Check whether holding holdings are available
     *
     * @return Boolean
     */
    protected function hasHoldings()
    {
        return $this->holdings && $this->holdings->getField(852) !== false;
    }

    /**
     * Check whether holding items are available
     *
     * @return Boolean
     */
    protected function hasItems()
    {
        return $this->holdings && $this->holdings->getField(949) !== false;
    }

    /**
     * Get structured elements (grouped by group and institution)
     *
     * @param String $fieldName       FieldName
     * @param Array  $mapping         Mapping
     * @param String $institutionCode InstitutionCode
     *
     * @return Array Items or holdings for institution
     */
    protected function getHoldingsData($fieldName, array $mapping, $institutionCode)
    {
        $data = [];
        $fields = $this->holdings ? $this->holdings->getFields($fieldName) : false;

        if (is_array($fields)) {
            foreach ($fields as $index => $field) {
                $item = $this->extractFieldData($field, $mapping);
                $institution = $item['institution'];
                $institution_chb = $item['institution_chb'];

                if ($institution === $institutionCode
                    || $institution_chb === $institutionCode
                ) {
                    $data[] = $item;
                }
            }
        }

        return $data;
    }

    /**
     * Get holdings structure for holdings
     *
     * @param Integer $fieldName Fieldname
     * @param Array   $data      Data
     *
     * @return Array[]
     */
    protected function getStructuredHoldingsStructure($fieldName, $data = [])
    {
        //$data    = array();
        $fields = $this->holdings ? $this->holdings->getFields($fieldName) : false;
        $mapping = [
            'B' => 'network',
            'F' => 'institution_chb',
            'b' => 'institution'
        ];

        if (is_array($fields)) {
            foreach ($fields as $index => $field) {
                $item = $this->extractFieldData($field, $mapping);
                $networkCode = $item['network'];
                $institution = $item['institution_chb'];
                $institution949b = $item['institution'];
                $groupCode = $this->getGroup($institution);

                // Prevent display of untranslated and ungrouped institutions
                $institutionLabel = $this->translator->translate(
                    $institution, 'institution'
                );
                if ($groupCode == 'unknown' || $institutionLabel === $institution) {
                    if ($groupCode === 'unknown') {
                        $this->swissbibLogger->logUngroupedInstitution($institution);
                    }
                    continue;
                }

                // Make sure group is present
                if (!isset($data[$groupCode])) {
                    $data[$groupCode] = [
                        'label' => $groupCode,
                        'networkCode' => $networkCode,
                        'institutions' => [],
                        'institution' => []
                    ];
                }

                // Make sure institution is present
                if (!isset($data[$groupCode]['institutions'][$institution])) {
                    $data[$groupCode]['institutions'][$institution] = [
                        'label' => $institution,
                        'bibinfolink' => $this->getBibInfoLink($institution)
                    ];
                }

                // Make sure institution 949$b is present
                $data[$groupCode]['institution'] = $institution949b;
            }
        }

        return $data;
    }

    /**
     * Get group code for institution based on mapping data
     *
     * @param String $institutionCode InstitutionCode
     *
     * @return String
     */
    public function getGroup($institutionCode)
    {
        $institutionCode = $institutionCode;
        return isset($this->institution2group[$institutionCode]) ?
            $this->institution2group[$institutionCode] : 'unknown';
    }

    /**
     * Build itemId from item properties and the id of the item
     * ItemId is not the id of the item, it's a combination of sub fields
     *
     * @param Array $holdingItem HoldingItem
     *
     * @return String
     *
     * @todo How to handle missing information. Throw exception, ignore?
     */
    protected function buildItemId(array $holdingItem)
    {
        if (isset($holdingItem['adm_code'])
            && isset($holdingItem['localid'])
            && isset($holdingItem['sequencenumber'])
        ) {
            return $holdingItem['adm_code'] . $holdingItem['localid'] .
                $holdingItem['sequencenumber'];
        }

        return 'incompleteItemData';
    }

    /**
     * Get link for holding action
     *
     * @param Array $holdingItem HoldingItem
     *
     * @return Array
     */
    protected function getHoldLink(array $holdingItem)
    {
        if (!isset($holdingItem['bibsysnumber'])) {
            return null;
        }

        $linkValues = [
            'id' => $holdingItem['bib_library'] . '-' . $holdingItem['bibsysnumber'],
            'item_id' => $this->buildItemId($holdingItem),
        ];

        return [
            'action' => 'Hold',
            'record' => $this->idItem, //'id',
            'anchor' => '#tabnav',
            'query' => http_build_query(
                $linkValues + [
                    'hashKey' => $this->hmac->generate($this->hmacKeys, $linkValues)
                ]
            ),
        ];
    }

    /**
     * Extract field data
     *
     * @param \File_MARC_Data_Field $field        Field
     * @param Array                 $fieldMapping Field code=>name mapping
     *
     * @return Array
     */
    protected function extractFieldData(\File_MARC_Data_Field $field,
        array $fieldMapping
    ) {
        $subFields = $field->getSubfields();
        $rawData = [];
        $data = [];

        // Fetch data
        foreach ($subFields as $code => $subdata) {
            if ($this->useConcatenation($code, $rawData)) {
                $rawData[$code] .= $this->concatenationMapping[$code] .
                    $subdata->getData();
            } else {
                $rawData[$code] = $subdata->getData();
            }
        }

        foreach ($fieldMapping as $code => $name) {
            $data[$name] = isset($rawData[$code]) ? $rawData[$code] : '';
        }

        return $data;
    }

    /**
     * UseConcatenation
     *
     * @param String $code    Code
     * @param Array  $rawData RawData
     *
     * @return bool
     */
    protected function useConcatenation($code, array $rawData)
    {
        return array_key_exists($code, $rawData)
            && array_key_exists($code, $this->concatenationMapping);
    }
}
