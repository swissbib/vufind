<?php
/**
 * Aleph
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
 * @package  VuFind_ILS_Driver
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\VuFind\ILS\Driver;

use VuFind\ILS\Driver\Aleph as VuFindDriver;
use SimpleXMLElement;
use VuFind\ILS\Driver\AlephRestfulException;
use VuFind\Exception\ILS as ILSException;
use DateTime;
use Zend\Http\Request;

/**
 * Aleph
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Auth
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Aleph extends VuFindDriver
{
    /**
     * ItemLinks
     *
     * @var array
     */
    protected $itemLinks;

    /**
     * Perform an XServer request.
     *
     * @param string $op     Operation
     * @param array  $params Parameters
     * @param bool   $auth   Include authentication?
     *
     * @return SimpleXMLElement
     */
    protected function doXRequest($op, $params, $auth = false)
    {
        if (!$this->xserver_enabled) {
            throw new \Exception(
                'Call to doXRequest without X-Server configuration in Aleph.ini'
            );
        }
        $url = "http://$this->host/X?op=$op";
        if (isset($params['verification'])) {
            $params['verification']
                = mb_strtoupper($params['verification'], 'UTF-8');
        }
        $url = $this->appendQueryString($url, $params);
        if ($auth) {
            $url = $this->appendQueryString(
                $url, [
                    'user_name' => $this->wwwuser,
                    'user_password' => $this->wwwpasswd
                ]
            );
        }
        $result = $this->doHTTPRequest($url);
        if ($result->error) {
            if ($this->debug_enabled) {
                $this->debug(
                    "XServer error, URL is $url, error message: $result->error."
                );
            }
            throw new ILSException("XServer error: $result->error.");
        }
        return $result;
    }

    /**
     * Get data for photo copies
     *
     * @param Integer $idPatron IdPatron
     *
     * @return Array
     */
    public function getPhotocopies($idPatron)
    {
        $photoCopyRequests = $this->getPhotoCopyRequests($idPatron);

        $dataMap = [
            'title'          => 'z13-title',
            'title2'         => 'z38-title',
            'dateOpen'       => 'z38-open-date',
            'dateUpdate'     => 'z30-update-date',
            'author'         => 'z38-author',
            'pages'          => 'z38-pages',
            'note1'          => 'z38-note-1',
            'note2'          => 'z38-note-2',
            'status'         => 'z38-status',
            'printStatus'    => 'z38-print-status',
            'pickup'         => 'z38-pickup-location',
            'library'        => 'z30-sub-library',
            'description'    => 'z30-description',
            'callNumber'     => 'z30-call-no',
            'callNumberKey'  => 'z30-call-no-key',
            'additionalInfo' => 'z38-additional-info',
            'requesterName'  => 'z38-requester-name',
            'sequence'       => 'z38-sequence',
            'itemSequence'   => 'z38-item-sequence',
            'id'             => 'z38-id',
            'number'         => 'z38-number',
            'alpha'          => 'z38-alpha'
        ];

        $photoCopiesData = [];

        foreach ($photoCopyRequests as $photoCopyRequest) {
            $photoCopyData = $this->extractResponseData($photoCopyRequest, $dataMap);

            // Process data
            $photoCopyData['dateOpen'] = DateTime::createFromFormat(
                'Ymd', $photoCopyData['dateOpen']
            )->format('d.m.Y');

            $photoCopyData['dateUpdate'] = DateTime::createFromFormat(
                'Ymd', $photoCopyData['dateUpdate']
            )->format('d.m.Y');

            $photoCopiesData[] = $photoCopyData;
        }

        return $photoCopiesData;
    }

    /**
     * GetBookings
     *
     * @param Integer $idPatron IdPatron
     *
     * @return Array
     */
    public function getBookings($idPatron)
    {
        $bookingRequests = $this->getBookingRequests($idPatron);
        $dataMap         = [
            'sequence'          => 'z37-sequence',
            'title'             => 'z13-title',
            'author'            => 'z13-author',
            'dateStart'         => 'z37-booking-orig-start-time',
            'dateEnd'           => 'z37-booking-orig-end-time',
            'pickupLocation'    => 'z37-pickup-location',
            'pickupSubLocation' => 'z37-delivery-sub-location',
            'itemStatus'        => 'z30-item-status',
            'callNumber'        => 'z30-call-no',
            'library'           => 'z30-sub-library',
            'note1'             => 'z37-note-1',
            'note2'             => 'z37-note-2',
            'barcode'           => 'z30-barcode',
            'collection'        => 'z30-collection',
            'description'       => 'z30-description'
        ];

        $bookingsData = [];

        foreach ($bookingRequests as $bookingRequest) {
            $bookingData = $this->extractResponseData($bookingRequest, $dataMap);

            // Process data
            $bookingData['dateStart'] = DateTime::createFromFormat(
                'YmdHi', $bookingData['dateStart']
            )->getTimestamp();

            $bookingData['dateEnd']   = DateTime::createFromFormat(
                'YmdHi', $bookingData['dateEnd']
            )->getTimestamp();

            $bookingsData[] = $bookingData;
        }

        return $bookingsData;
    }

    /**
     * Get allowed actions for current user for holding item
     * Actions: hold, shortLoan, photocopyRequest, bookingRequest
     *
     * @param String $patronId Catalog user id
     * @param String $id       Item id
     * @param String $group    Group id
     * @param String $bib      Bib
     *
     * @return Array List with flags for actions
     */
    public function getAllowedActionsForItem($patronId, $id, $group, $bib)
    {
        $resource = $bib . $id;
        $xml      = $this->doRestDLFRequest(
            ['patron', $patronId, 'record', $resource, 'items', $group]
        );

        $result    = [];
        $functions = [
            'hold'             => 'HoldRequest',
            'shortLoan'        => 'ShortLoan',
            'photorequest'     => 'PhotoRequest',
            'bookingrequest'   => 'BookingRequest'
        ];

        // Check flags for each info node
        foreach ($functions as $key => $type) {
            $typeInfoNodes = $xml->xpath('//info[@type="' . $type . '"]');
            $result[$key]  = (string)$typeInfoNodes[0]['allowed'] === 'Y';
        }

        return $result;
    }

    /**
     * Get all circulation status infos for item
     *
     * @param String $sysNumber SysNumber
     * @param String $library   Library
     *
     * @return Array[]
     */
    public function getCirculationStatus($sysNumber, $library = 'DSV01')
    {
        $xml = $this->doXRequest(
            'circ-status', [
                'sys_no'  => $sysNumber,
                'library' => $library
            ]
        );

        $itemDataNodes = $xml->xpath('item-data');
        $data          = [];

        foreach ($itemDataNodes as $itemDataNode) {
            $itemData = [];

            foreach ($itemDataNode as $fieldName => $fieldValue) {
                $itemData[$fieldName] = (string)$fieldValue;
            }

            $data[] = $itemData;
        }

        return $data;
    }

    /**
     * GetHoldingHoldingsLinkList
     *
     * @param string $resourceId      ResourceId
     * @param string $institutionCode InstitutionCode
     * @param int    $offset          Offset
     * @param int    $year            Year
     * @param int    $volume          Volume
     * @param array  $extraRestParams ExtraRestParams
     *
     * @return array
     */
    protected function getHoldingHoldingsLinkList(
        $resourceId,
        $institutionCode = '',
        $offset = 0,
        $year = 0,
        $volume = 0,
        array $extraRestParams = []
    ) {
        if (!is_array($this->itemLinks) || true) {
            $pathElements    = ['record', $resourceId, 'items'];
            $parameters        = $extraRestParams;

            if ($institutionCode) {
                $parameters['sublibrary'] = $institutionCode;
            }
            if ($offset) {
                $parameters['startPos'] = intval($offset) + 1;
            }
            if ($year) {
                $parameters['year'] = intval($year);
            }
            if ($volume) {
                $parameters['volume'] = intval($volume);
            }

            $xmlResponse = $this->doRestDLFRequest($pathElements, $parameters);

            /**
             * Items
             *
             * @var SimpleXMLElement[] $items
             */
            $items = $xmlResponse->xpath('//item');
            $links = [];

            foreach ($items as $item) {
                $links[] = (string)$item->attributes()->href;
            }

            $this->itemLinks = $links;
        }

        return $this->itemLinks;
    }

    /**
     * GetHoldingHoldingItems
     *
     * @param string $resourceId      ResourceId
     * @param string $institutionCode InstitutionCode
     * @param int    $offset          Offset
     * @param int    $year            Year
     * @param int    $volume          Volume
     * @param int    $numItems        NumItems
     * @param array  $extraRestParams ExtraRestParams
     *
     * @return array
     */
    public function getHoldingHoldingItems(
        $resourceId,
        $institutionCode = '',
        $offset = 0,
        $year = 0,
        $volume = 0,
        $numItems = 10,
        array $extraRestParams = []
    ) {
        $links   = $this->getHoldingHoldingsLinkList(
            $resourceId, $institutionCode, $offset, $year, $volume, $extraRestParams
        );
        $items   = [];
        $dataMap = [
            'title'                 => 'z13-title',
            'author'                => 'z13-author',
            'itemStatus'            => 'z30-item-status',
            'signature'             => 'z30-call-no',
            'library'               => 'z30-sub-library',
            'barcode'               => 'z30-barcode',
            'location_expanded'     => 'z30-collection',
            'location_code'         => 'z30-collection-code',
            'description'           => 'z30-description',
            'raw-sequence-number'   => 'z30-item-sequence',
            'localid'               => 'z30-doc-number',
            'opac_note'             => 'z30-note-opac',
        ];

        $linksToExtend = array_slice($links, 0, $numItems);

        foreach ($linksToExtend as $link) {
            $itemResponseData = $this->doHTTPRequest($link);

            $item = $this->extractResponseData($itemResponseData->item, $dataMap);

            if (isset($item['raw-sequence-number'])) {
                $item['sequencenumber'] = sprintf(
                    '%06d', trim(str_replace('.', '', $item['raw-sequence-number']))
                );
            }

            $items[] = $item;
        }

        return $items;
    }

    /**
     * GetResourceFilters
     *
     * @param string $resourceId ResourceId
     *
     * @return Array[]
     */
    public function getResourceFilters($resourceId)
    {
        $pathElements = ['record', $resourceId, 'filters'];
        $xmlResponse  = $this->doRestDLFRequest($pathElements);

        $yearNodes    = $xmlResponse->{'record-filters'}->xpath('//year');
        $years        = array_map('trim', $yearNodes);
        sort($years);

        $volumeNodes  = $xmlResponse->{'record-filters'}->xpath('//volume');
        $volumes      = array_map('trim', $volumeNodes);
        sort($volumes);

        return [
            'years'   => $years,
            'volumes' => $volumes
        ];
    }

    /**
     * GetHoldingItemCount
     *
     * @param string $resourceId      ResourceId
     * @param string $institutionCode InstitutionCode
     * @param int    $offset          Offset
     * @param int    $year            Year
     * @param int    $volume          Volume
     *
     * @return int
     */
    public function getHoldingItemCount($resourceId, $institutionCode = '',
        $offset = 0, $year = 0, $volume = 0
    ) {
        $links = $this->getHoldingHoldingsLinkList(
            $resourceId, $institutionCode, 0, $year, $volume
        );

        return sizeof($links);
    }

    /**
     * Public Function which retrieves renew, hold and cancel settings from the
     * driver ini file.
     *
     * @param string $function The name of the feature to be checked
     * @param array  $params   Parameters
     *
     * @return array An array with key-value pairs.
     */
    public function getConfig($function, $params = null)
    {
        if (isset($this->config[$function])) {
            $functionConfig = $this->config[$function];
        } else {
            $functionConfig = false;
        }

        return $functionConfig;
    }

    /**
     * Get booking requests
     *
     * @param Integer $idPatron IdPatron
     *
     * @return \SimpleXMLElement[]
     */
    protected function getBookingRequests($idPatron)
    {
        $xmlResponse = $this->doRestDLFRequest(
            ['patron', $idPatron, 'circulationActions', 'requests', 'bookings'],
            ['view' => 'full']
        );

        return $xmlResponse->xpath('//booking-request');
    }

    /**
     * Get photo copy requests
     *
     * @param Integer $idPatron IdPatron
     *
     * @return \SimpleXMLElement[]
     */
    protected function getPhotoCopyRequests($idPatron)
    {
        $xmlResponse = $this->doRestDLFRequest(
            [
                'patron', $idPatron, 'circulationActions', 'requests', 'photocopies'
            ],
            ['view' => 'full']
        );

        return $xmlResponse->xpath('//photocopy-request');
    }

    /**
     * Extract a list of values out of the XML response
     *
     * @param \SimpleXMLElement $xmlResponse Response
     * @param Array             $map         Map
     *
     * @return Array
     */
    protected function extractResponseData(SimpleXMLElement $xmlResponse, array $map)
    {
        $data = [];

        foreach ($map as $resultField => $path) {
            if (isset($xmlResponse->$path)) {
                $data[$resultField] = (string)$xmlResponse->$path;
            } elseif (strpos($path, '-')) {
                list($group, $field) = explode('-', $path, 2);

                $data[$resultField] = (string)$xmlResponse->$group->$path;
            }
        }

        return $data;
    }

    /**
     * Get my transactions response items
     *
     * @param Array   $user    User
     * @param Boolean $history History
     *
     * @return \SimpleXMLElement[]
     */
    protected function getMyTransactionsResponse(array $user, $history = false)
    {
        $userId = $user['id'];
        $params = ["view" => "full"];

        if ($history) {
            $params["type"] = "history";
        }

        $xml = $this->doRestDLFRequest(
            ['patron', $userId, 'circulationActions', 'loans'], $params
        );

        return $xml->xpath('//loan');
    }

    /**
     * Get Patron Profile
     * This is responsible for retrieving the profile for a specific patron.
     *
     * @param array $user The patron array
     *
     * @throws ILSException
     *
     * @return array Array of the patron's profile data on success.
     */
    public function getMyProfile($user)
    {
        if ($this->xserver_enabled) {
            return $this->getMyProfileX($user);
        } else {
            return $this->getMyProfileDLF($user);
        }
    }

    /**
     * Get profile information using X-server.
     *
     * @param array $user The patron array
     *
     * @throws ILSException
     *
     * @return array Array of the patron's profile data on success.
     *               Angepasste Funktion im Bereich Ausgabe der Adresse (z304)
     */
    public function getMyProfileX($user)
    {
        $recordList = [];
        if (!isset($user['college'])) {
            $user['college'] = $this->useradm;
        }
        if (!isset($user['cat_password'])) {
            //because getMyProfile gets also called without a password in VuFind
            $user['cat_password'] = '';
        }
        $xml = $this->doXRequest(
            "bor-auth",
            //array(
            //    'loans' => 'N', 'cash' => 'N', 'hold' => 'N',
            //    'library' => $user['college'], 'bor_id' => $user['id']
            [
                'library' => $user['college'], 'bor_id' => $user['id'],
                'verification' => $user['cat_password']
            ], true
        );
        $id = (string) $xml->z303->{'z303-id'};
        $delinq_1 = (string) $xml->z303->{'z303-delinq-1'};
        $delinq_n_1 = (string) $xml->z303->{'z303-delinq-n-1'};
        $delinq_2 = (string) $xml->z303->{'z303-delinq-2'};
        $delinq_n_2 = (string) $xml->z303->{'z303-delinq-n-2'};
        $delinq_3 = (string) $xml->z303->{'z303-delinq-3'};
        $delinq_n_3 = (string) $xml->z303->{'z303-delinq-n-3'};
        $address1 = (string) $xml->z304->{'z304-address-0'};
        $address2 = (string) $xml->z304->{'z304-address-1'};
        $address3 = (string) $xml->z304->{'z304-address-2'};
        $address4 = (string) $xml->z304->{'z304-address-3'};
        $address5 = (string) $xml->z304->{'z304-address-4'};
        $zip = (string) $xml->z304->{'z304-zip'};
        $phone = (string) $xml->z304->{'z304-telephone'};
        //$barcode = (string) $xml->z304->{'z304-address-0'};
        $group = (string) $xml->z305->{'z305-bor-status'};
        $expiry = (string) $xml->z305->{'z305-expiry-date'};
        $credit_sum = (string) $xml->z305->{'z305-sum'};
        $credit_sign = (string) $xml->z305->{'z305-credit-debit'};
        $name = (string) $xml->z303->{'z303-name'};
        if (strstr($name, ",")) {
            list($lastname, $firstname) = explode(",", $name);
        } else {
            $lastname = $name;
            $firstname = "";
        }
        if ($credit_sign == null) {
            $credit_sign = "C";
        }
        $recordList['firstname'] = $firstname;
        $recordList['lastname'] = $lastname;
        if (isset($user['email'])) {
            $recordList['email'] = $user['email'];
        }
        $recordList['address1'] = $address1;
        $recordList['address2'] = $address2;
        $recordList['address3'] = $address3;
        $recordList['address4'] = $address4;
        $recordList['address5'] = $address5;
        $recordList['zip'] = $zip;
        $recordList['phone'] = $phone;
        $recordList['group'] = $group;
        //$recordList['barcode'] = $barcode;
        $recordList['expire'] = $this->parseDate($expiry);
        $recordList['credit'] = $expiry;
        $recordList['credit_sum'] = $credit_sum;
        $recordList['credit_sign'] = $credit_sign;
        $recordList['id'] = $id;
        $recordList['delinq-1'] = $delinq_1;
        $recordList['delinq-n-1'] = $delinq_n_1;
        $recordList['delinq-2'] = $delinq_2;
        $recordList['delinq-n-2'] = $delinq_n_2;
        $recordList['delinq-3'] = $delinq_3;
        $recordList['delinq-n-3'] = $delinq_n_3;
        return $recordList;
    }

    /**
     * Get profile information using DLF service.
     *
     * @param array $user The patron array
     *
     * @throws ILSException
     *
     * @return array      Array of the patron's profile data on success.
     */
    public function getMyProfileDLF($user)
    {
        $xml = $this->doRestDLFRequest(
            ['patron', $user['id'], 'patronInformation', 'address']
        );
        $address = $xml->xpath('//address-information');
        $address = $address[0];
        $address1 = (string)$address->{'z304-address-1'};
        $address2 = (string)$address->{'z304-address-2'};
        $address3 = (string)$address->{'z304-address-3'};
        $address4 = (string)$address->{'z304-address-4'};
        $address5 = (string)$address->{'z304-address-5'};
        $zip = (string)$address->{'z304-zip'};
        $phone = (string)$address->{'z304-telephone-1'};
        $email = (string)$address->{'z404-email-address'};
        $dateFrom = (string)$address->{'z304-date-from'};
        $dateTo = (string)$address->{'z304-date-to'};
        if (strpos($address2, ",") === false) {
            $recordList['lastname'] = $address2;
            $recordList['firstname'] = "";
        } else {
            list($recordList['lastname'], $recordList['firstname'])
                = explode(",", $address2);
        }
        $recordList['address1'] = $address1;
        $recordList['address2'] = $address2;
        $recordList['address3'] = $address3;
        $recordList['address4'] = $address4;
        $recordList['address5'] = $address5;
        $recordList['zip'] = $zip;
        $recordList['phone'] = $phone;
        $recordList['email'] = $email;
        $recordList['dateFrom'] = $dateFrom;
        $recordList['dateTo'] = $dateTo;
        $recordList['id'] = $user['id'];
        $xml = $this->doRestDLFRequest(
            ['patron', $user['id'], 'patronStatus', 'registration']
        );
        $status = $xml->xpath("//institution/z305-bor-status");
        $expiry = $xml->xpath("//institution/z305-expiry-date");
        $recordList['expire'] = $this->parseDate($expiry[0]);
        $recordList['group'] = $status[0];
        return $recordList;
    }

    /**
     * Get Patron Transactions
     *
     * This is responsible for retrieving all transactions (i.e. checked out items)
     * by a specific patron.
     *
     * @param array $user    The patron array from patronLogin
     * @param bool  $history Include history of transactions (true) or just get
     *                       current ones (false).
     *
     * @throws \VuFind\Exception\Date
     * @throws ILSException
     *
     * @return array        Array of the patron's transactions on success.
     */
    public function getMyTransactions($user, $history = false)
    {
        $transactionsResponseItems = $this->getMyTransactionsResponse(
            $user, $history
        );
        $dataMap = [
            'barcode'        => 'z30-barcode',
            'title'            => 'z13-title',
            'doc-number'    => 'z36-doc-number',
            'item-sequence'    => 'z36-item-sequence',
            'sequence'        => 'z36-sequence',
            'loaned'        => 'z36-loan-date',
            'due'            => 'z36-due-date',
            'status'        => 'z36-status',
            'return'        => 'z36-returned-date',
            'renewals'        => 'z36-no-renewal',
            'library'        => 'z30-sub-library',
            'librarycode'    => 'z36-sub-library-code',
            'callnum'        => 'z30-call-no',
            'renew_info'    => 'renew-info'
        ];
        $transactionsData    = [];

        foreach ($transactionsResponseItems as $transactionsResponseItem) {
            $itemData = $this->extractResponseData(
                $transactionsResponseItem, $dataMap
            );
            $group = $transactionsResponseItem->xpath('@href');
            $itemURL = (string) $group[0];

            // get renew-Information for every Item. ALEPH-logic forces to
            // iterate, info on resultlist is always true
            $response  = $this->doHTTPRequest($itemURL);
            $renewable = (string) $response->loan->attributes()->renew;
            $renewable = $renewable === 'Y' ? true : false;

                // Add special data
            try {
                $itemData['id'] = ($history) ? null : $this->barcodeToID(
                    $itemData['barcode']
                );
                $itemData['item_id'] = substr(strrchr($group[0], "/"), 1);
                $itemData['reqnum'] = $itemData['doc-number'] .
                    $itemData['item-sequence'] . $itemData['sequence'];
                $itemData['loandate'] = DateTime::createFromFormat(
                    'Ymd', $itemData['loaned']
                )->format('d.m.Y');
                $itemData['duedate'] = DateTime::createFromFormat(
                    'Ymd', $itemData['due']
                )->format('d.m.Y');
                $itemData['returned'] = DateTime::createFromFormat(
                    'Ymd', $itemData['return']
                )->format('d.m.Y');
                $itemData['renewable'] = $renewable;

                $transactionsData[] = $itemData;
            } catch (\Exception $ex) {
                $this->logger->err(
                    "error while trying to fetch loaned item from ILS system", [
                        'barcode' => $itemData['barcode'],
                        'doc-number' => $itemData['doc-number'],
                        'item-sequence' => $itemData['item-sequence'],
                        'callnum' => $itemData['callnum']
                    ]
                );
            }
        }

        return $transactionsData;
    }

    /**
     * Get Required Date

     * @param array $patron   Patron
     * @param array $holdInfo HoldInfo
     *
     * @return string
     */
    public function getRequiredDate($patron, $holdInfo = null)
    {
        if ($holdInfo != null) {
            $details = $this->getHoldingInfoForItem(
                $patron['id'], $holdInfo['id'], $holdInfo['item_id']
            );
            $requiredDate = $details['last-interest-date'];

            return $requiredDate;
        }
    }

    /**
     * Get my holds
     *
     * @param Array $user User
     *
     * @return Array[]
     */
    public function getMyHolds($user)
    {
        $userId = $user['id'];
        $holdList = [];
        $xml = $this->doRestDLFRequest(
            ['patron', $userId, 'circulationActions', 'requests', 'holds'],
            ['view' => 'full']
        );
        $dataMap         = [
            'location'        => 'z37-pickup-location',
            'title'            => 'z13-title',
            'author'        => 'z13-author',
            'isbn-raw'        => 'z13-isbn-issn',
            'reqnum'        => 'z37-doc-number',
            'barcode'        => 'z30-barcode',
            'expire'        => 'z37-end-request-date',
            'holddate'        => 'z37-hold-date',
            'create'        => 'z37-open-date',
            'status'        => 'status',
            'sequence'        => 'z37-sequence',
            'institution'    => 'z30-sub-library-code',
            'signature'        => 'z30-call-no',
            'description'    => 'z30-description'
        ];

        foreach ($xml->xpath('//hold-request') as $item) {
            $itemData    = $this->extractResponseData($item, $dataMap);
            $href         = $item->xpath('@href');
            $delete        = $item->xpath('@delete');

                // Special fields which require calculation
            $itemData['type'] = 'hold';
            $itemData['item_id'] = substr($href[0], strrpos($href[0], '/') + 1);
            //$itemData['isbn']        = array($itemData['isbn-raw']);
            $itemData['id'] = $this->barcodeToID($itemData['barcode']);
            $itemData['expire'] = DateTime::createFromFormat(
                'Ymd', $itemData['expire']
            )->format('d.m.Y');
            $itemData['create'] = DateTime::createFromFormat(
                'Ymd', $itemData['create']
            )->format('d.m.Y');
            $itemData['delete'] = (string)($delete[0]) === 'Y';

            // Auslesen Reservationsstatus
            if (preg_match('/due date/', $itemData['status'])) {
                $itemData['position'] = preg_replace(
                    '/^Waiting in position[\s]+([\d]+).*$/',
                    '$1',
                    $itemData['status']
                );
                $itemData['duedate'] = DateTime::createFromFormat(
                    'd/m/y', preg_replace(
                        '/^.* due date ([0-3][0-9]\/[0-2][0-9]\/[0-9][0-9])$/',
                        '$1',
                        $itemData['status']
                    )
                )->format('d.m.Y');
            }

            if (preg_match('/queue$/', $itemData['status'])) {
                $itemData['position'] = preg_replace(
                    '/^Waiting in position[\s]+([\d]+).*$/',
                    '$1',
                    $itemData['status']
                );
            }

            $holdList[] = $itemData;
        }

        return $holdList;
    }

    /**
     * Get fine data as xml nodes from server
     *
     * @param String $userId UserId
     *
     * @return \SimpleXMLElement[]
     */
    protected function getMyFinesResponse($userId)
    {
        $xml = $this->doRestDLFRequest(
            ['patron', $userId, 'circulationActions', 'cash'],
            ["view" => "full"]
        );

        return $xml->xpath('//cash');
    }

    /**
     * Get fines list
     *
     * @param Array $user User
     *
     * @return Array[]
     *
     * @todo Fetch solr ID to create a link?
     */
    public function getMyFines($user)
    {
        $fineResponseItems    = $this->getMyFinesResponse($user['id']);
        $fines                = [];
        $dataMap         = [
            'sum'            => 'z31-sum',
            'date'            => 'z31-date',
            'type'            => 'z31-type',
            'description'    => 'z31-description',
            'credittype'    => 'z31-credit-debit',
            'checkout'        => 'z31-date',
            'sequence'        => 'z31-sequence',
            'status'        => 'z31-status',
            'signature'        => 'z30-call-no'
        ];

        foreach ($fineResponseItems as $fineResponseItem) {
            $itemData    = $this->extractResponseData($fineResponseItem, $dataMap);

            $itemData['title'] = (string) $fineResponseItem->{'z13'}->{'z13-title'};

            $itemData['amount'] = (float) preg_replace(
                '/[\(\)]/', '', $itemData['sum']
            );

            $itemData['checkout'] = DateTime::createFromFormat(
                'Ymd', $itemData['checkout']
            )->format('d.m.Y');

            $itemData['institution']
                = (string) $fineResponseItem->{'z30-sub-library-code'};

            $sortKey    = $itemData['sequence'];

            $fines[$sortKey] = $itemData;
        }

            // Sort fines by sequence
        ksort($fines);

            // Sum up balance
        $balance    = 0;

        foreach ($fines as $index => $fine) {
            $balance += $fine['amount'];

            $fines[$index]['balance'] = $balance;
        }

            // Return list without sort keys
        return array_values($fines);
    }

    /**
     * Get Pick Up Locations
     *
     * @param array  $patron Patron
     * @param string $id     Id
     * @param string $group  Group
     *
     * @throws ILSException
     *
     * @return array An array of associative arrays with locationID and
     *               locationDisplay keys
     */
    public function getCopyPickUpLocations(array $patron, $id, $group)
    {
        list($bib, $sys_no) = $this->parseId($id);
        $resource = $bib . $sys_no;
        $xml = $this->doRestDLFRequest(
            ['patron', $patron['id'], 'record', $resource, 'items', $group, 'photo']
        );

        $locations = [];
        $part = $xml->xpath('//pickup-locations');
        if ($part) {
            foreach ($part[0]->children() as $node) {
                $arr = $node->attributes();
                $code = (string) $arr['code'];
                $loc_name = (string) $node;
                $locations[$code] = $loc_name;
            }
        } else {
            throw new ILSException('No pickup locations');
        }

        return $locations;
    }

    /**
     * Change Password
     *
     * Attempts to change patron password (PIN code)
     *
     * @param array $details An array of patron id and old and new password:
     *
     * 'patron'      The patron array from patronLogin
     * 'oldPassword' Old password
     * 'newPassword' New password
     *
     * @return array An array of data on the request including
     * whether or not it was successful and a system message (if available)
     */
    public function changePassword($details)
    {
        $patron = $details['patron'];

        $oldPIN = rawurlencode(
            htmlspecialchars(
                mb_strtoupper($details['oldPassword'], 'UTF-8'), ENT_COMPAT, 'UTF-8'
            )
        );

        $newPIN = rawurlencode(
            htmlspecialchars(
                mb_strtoupper($details['newPassword'], 'UTF-8'), ENT_COMPAT, 'UTF-8'
            )
        );

        $xml =  <<<EOT
post_xml=<?xml version = "1.0" encoding = "UTF-8"?>
<get-pat-pswd>
    <password_parameters>
        <old-password>$oldPIN</old-password>
        <new-password>$newPIN</new-password>
    </password_parameters>
</get-pat-pswd>
EOT;

        $this->doRestDLFRequest(
            [
                'patron', $patron['id'], 'patronInformation', 'password'
            ],
            null, 'POST', $xml
        );

        return ['success' => true, 'status' => 'change_password_ok'];
    }

    /**
     * GetMyAddress
     *
     * @param array $patron Patron
     *
     * @return array
     *
     * @throws AlephRestfulException
     */
    public function getMyAddress(array $patron)
    {
        $result = $this->doRestDLFRequest(
            [
                'patron', $patron['id'], 'patronInformation', 'address'
            ],
            null, 'GET'
        );

        $addressInformation = $result->{'address-information'};

        return [
            'z304-address-1' => (string) $addressInformation->{'z304-address-1'},
            'z304-address-2' => (string) $addressInformation->{'z304-address-2'},
            'z304-address-3' => (string) $addressInformation->{'z304-address-3'},
            'z304-address-4' => (string) $addressInformation->{'z304-address-4'},
            'z304-address-5' => (string) $addressInformation->{'z304-address-5'},
            'z304-email-address' =>
                (string) $addressInformation->{'z304-email-address'},
            'z304-telephone-1' => (string) $addressInformation->{'z304-telephone-1'},
            'z304-telephone-2' => (string) $addressInformation->{'z304-telephone-2'},
            'z304-telephone-3' => (string) $addressInformation->{'z304-telephone-3'},
            'z304-telephone-4' => (string) $addressInformation->{'z304-telephone-4'},
            'z304-date-from' => (string) $addressInformation->{'z304-date-from'},
            'z304-date-to' => (string) $addressInformation->{'z304-date-to'},
        ];
    }

    /**
     * ChangeMyAddress
     *
     * @param array $patron     Patron
     * @param array $newAddress NewAddress
     *
     * @return SimpleXMLElement
     *
     * @throws AlephRestfulException
     */
    public function changeMyAddress(array $patron, array $newAddress)
    {
        $z304_address_1 = $this->maskXmlString($newAddress['z304-address-1']);
        $z304_address_2 = $this->maskXmlString($newAddress['z304-address-2']);
        $z304_address_3 = $this->maskXmlString($newAddress['z304-address-3']);
        $z304_address_4 = $this->maskXmlString($newAddress['z304-address-4']);
        $z304_address_5 = $this->maskXmlString($newAddress['z304-address-5']);
        $z304_email_address = $this->maskXmlString(
            $newAddress['z304-email-address']
        );
        $z304_telephone_1 = $this->maskXmlString($newAddress['z304-telephone-1']);
        $z304_telephone_2 = $this->maskXmlString($newAddress['z304-telephone-2']);
        $z304_telephone_3 = $this->maskXmlString($newAddress['z304-telephone-3']);
        $z304_telephone_4 = $this->maskXmlString($newAddress['z304-telephone-4']);
        $z304_date_from = $this->maskXmlString($newAddress['z304-date-from']);
        $z304_date_to = $this->maskXmlString($newAddress['z304-date-to']);

        $xml =  <<<EOT
post_xml=<?xml version = "1.0" encoding = "UTF-8"?>
<get-pat-adrs>
  <address-information>
    <z304-address-1>{$z304_address_1}</z304-address-1>
    <z304-address-2>{$z304_address_2}</z304-address-2>
    <z304-address-3>{$z304_address_3}</z304-address-3>
    <z304-address-4>{$z304_address_4}</z304-address-4>
    <z304-address-5>{$z304_address_5}</z304-address-5>
    <z304-email-address>{$z304_email_address}</z304-email-address>
    <z304-telephone-1>{$z304_telephone_1}</z304-telephone-1>
    <z304-telephone-2>{$z304_telephone_2}</z304-telephone-2>
    <z304-telephone-3>{$z304_telephone_3}</z304-telephone-3>
    <z304-telephone-4>{$z304_telephone_4}</z304-telephone-4>
    <z304-date-from>{$z304_date_from}</z304-date-from>
    <z304-date-to>{$z304_date_to}</z304-date-to>
  </address-information>
</get-pat-adrs>
EOT;

        return $this->doRestDLFRequest(
            [
                'patron', $patron['id'], 'patronInformation', 'address'
            ],
            null, 'POST', $xml
        );
    }

    /**
     * PutCopy
     *
     * @param array  $patron      Patron
     * @param string $id          Id
     * @param string $group       Group
     * @param array  $copyRequest CopyRequest
     *
     * @return \VuFind\ILS\Driver\SimpleXMLElement
     *
     * @throws AlephRestfulException
     */
    public function putCopy(array $patron, $id, $group, array $copyRequest)
    {
        list($bib, $sys_no) = $this->parseId($id);
        $resource = $bib . $sys_no;

        $pickup_location = $this->maskXmlString($copyRequest['pickup-location']);
        $sub_author = $this->maskXmlString($copyRequest['sub-author']);
        $sub_title = $this->maskXmlString($copyRequest['sub-title']);
        $pages = $this->maskXmlString($copyRequest['pages']);
        $note1 = $this->maskXmlString($copyRequest['note1']);
        $note2 = $this->maskXmlString($copyRequest['note2']);

        $xml =  <<<EOT
post_xml=<?xml version="1.0"?>
<photo-request-parameters>
    <pickup-location>{$pickup_location}</pickup-location>
    <sub-author>{$sub_author}</sub-author>
    <sub-title>{$sub_title}</sub-title>
    <pages>{$pages}</pages>
    <note-1>{$note1}</note-1>
    <note-2>{$note2}</note-2>
</photo-request-parameters>
EOT;

        return $this->doRestDLFRequest(
            ['patron', $patron['id'], 'record', $resource, 'items', $group, 'photo'],
            null, 'PUT', $xml
        );
    }

    /**
     * Masking Xml Strings
     *
     * @param string $content Content
     *
     * @return string
     */
    protected function maskXmlString($content)
    {
        return rawurlencode(htmlspecialchars($content, ENT_COMPAT, 'UTF-8'));
    }

    /**
     * Perform an HTTP request.
     *
     * @param string $url    URL of request
     * @param string $method HTTP method
     * @param string $body   HTTP body (null for none)
     *
     * @return SimpleXMLElement
     */
    protected function doHTTPRequest($url, $method = 'GET', $body = null)
    {
        //GHI should be removed once timeout is part of core
        if ($this->debug_enabled) {
            $this->debug("URL: '$url'");
        }

        $result = null;
        try {
            $timeout = isset($this->config['Catalog']['timeout']) ?
                $this->config['Catalog']['timeout'] : null;
            $client = $this->httpService->createClient(
                $url, Request::METHOD_GET, $timeout
            );
            $client->setMethod($method);
            if ($body != null) {
                $client->setRawBody($body);
            }
            $result = $client->send();
        } catch (\Exception $e) {
            throw new ILSException($e->getMessage());
        }
        if (!$result->isSuccess()) {
            throw new ILSException('HTTP error');
        }
        $answer = $result->getBody();
        if ($this->debug_enabled) {
            $this->debug("url: $url response: $answer");
        }
        $answer = str_replace('xmlns=', 'ns=', $answer);
        $result = simplexml_load_string($answer);
        if (!$result) {
            if ($this->debug_enabled) {
                $this->debug("XML is not valid, URL: $url");
            }
            throw new ILSException(
                "XML is not valid, URL: $url method: $method answer: $answer."
            );
        }
        return $result;
    }

}
