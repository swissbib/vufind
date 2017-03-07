<?php
/**
 * Multiple Backend Driver : Swissbib extensions
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

use VuFind\ILS\Driver\MultiBackend as VFMultiBackend,
    VuFind\Exception\ILS as ILSException,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Log\LoggerInterface;

/**
 * MultiBackend
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Auth
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class MultiBackend extends VFMultiBackend
{
    /**
     * GetBookings
     *
     * @param String $id Id
     *
     * @return array
     */
    public function getBookings($id)
    {
        $source = $this->getSource($id);
        $driver = $this->getDriver($source);
        if ($driver) {
            return $driver->getBookings($this->getLocalId($id));
        }
        return [];
    }

    /**
     * GetPhotoCopies
     *
     * @param String $id Id
     *
     * @return array
     */
    public function getPhotoCopies($id)
    {
        $source = $this->getSource($id);
        $driver = $this->getDriver($source);
        if ($driver) {
            return $driver->getPhotoCopies($this->getLocalId($id));
        }
        return [];
    }

    /**
     * GetAllowedActionsForItem
     *
     * @param String $patronId PatronId
     * @param String $id       Id
     * @param String $group    Group
     * @param String $bib      Bib
     *
     * @return array
     */
    public function getAllowedActionsForItem($patronId, $id, $group, $bib)
    {
        $source = $this->getSource($patronId);
        $driver = $this->getDriver($source);

        if ($driver) {
            return $driver->getAllowedActionsForItem(
                $this->getLocalId($patronId), $id, $group, $bib
            );
        }
        return [];
    }

    /**
     * GetRequiredDate
     *
     * @param array $patron   Patron
     * @param array $holdInfo HoldInfo
     *
     * @return array
     */
    public function getRequiredDate($patron, $holdInfo = null)
    {
        $id = $patron['id'];
        $source = $this->getSource($id);
        $driver = $this->getDriver($source);

        if ($driver) {
            return $driver->getRequiredDate($patron, $holdInfo);
        }

        return [];
    }

    /**
     * GetPickUpLocations
     *
     * @param array|Boolean $patron      Patron
     * @param array         $holdDetails HoldDetails
     *
     * @throws ILSException
     *
     * @return mixed
     */
    public function getPickUpLocations($patron = false, $holdDetails = null)
    {
        $source = $this->getSource($patron['cat_username'], 'login');
        $driver = $this->getDriver($source);
        if ($driver) {
            if ($holdDetails) {
                $locations = $driver->getPickUpLocations(
                    $this->stripIdPrefixes($patron, $source),
                    $this->stripIdPrefixes($holdDetails, $source)
                );
                return $this->addIdPrefixes($locations, $source);
            }
            throw new ILSException('No suitable backend driver found');
        }
    }

    /**
     * PlaceHold
     *
     * @param array $holdDetails HoldDetails
     *
     * @throws ILSException
     *
     * @return mixed
     */
    public function placeHold($holdDetails)
    {
        $source = $this->getSource($holdDetails['patron']['cat_username'], 'login');
        $driver = $this->getDriver($source);

        if ($driver) {
            $holdDetails = $this->stripIdPrefixes($holdDetails, $source);

            return $driver->placeHold($holdDetails);
        }

        throw new ILSException('No suitable backend driver found');
    }

    /**
     * The following functions are implementations of a "Basel Bern"
     * functionality, display of journal volumes to order
     *
     * @param string $resourceId      ResourceId
     * @param string $institutionCode InstitutionCode
     * @param int    $offset          Offset
     * @param int    $year            Year
     * @param int    $volume          Volume
     * @param int    $numItems        NumItems
     * @param array  $extraRestParams ExtraRestParams
     *
     * @return mixed
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
        $source = $this->getSource($resourceId);
        $driver = $this->getDriver($source);
        if ($driver) {
            return $driver->getHoldingHoldingItems(
                $resourceId,
                $institutionCode,
                $offset,
                $year,
                $volume,
                $numItems,
                $extraRestParams
            );
        }
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
     * @throws ILSException
     *
     * @return mixed
     */
    public function getHoldingItemCount($resourceId, $institutionCode = '',
        $offset = 0, $year = 0, $volume = 0
    ) {
        $source = $this->getSource($resourceId);
        $driver = $this->getDriver($source);

        if ($driver) {
            return $driver->getHoldingItemCount(
                $resourceId, $institutionCode, $offset, $year, $volume
            );
        }

        throw new ILSException('No suitable backend driver found');
    }

    /**
     * GetResourceFilters
     *
     * @param string $resourceId ResourceId
     *
     * @return mixed
     */
    public function getResourceFilters($resourceId)
    {
        $source = $this->getSource($resourceId);
        $driver = $this->getDriver($source);
        if ($driver) {
            return $driver->getResourceFilters($resourceId);
        }
    }

    /**
     * Extract source from the given ID
     * Circumvent the private declaration in parent class
     *
     * @param string $id        The id to be split
     * @param string $delimiter The delimiter to be used from $this->delimiters
     *
     * @return string Source
     */
    public function getSource($id, $delimiter = '')
    {
        return parent::getSource($id, $delimiter = '');
    }

    /**
     * Get configuration for the ILS driver.  We will load an .ini file named
     * after the driver class and number if it exists;
     * otherwise we will return an empty array.
     *
     * @param string $source The source id to use for determining the
     * configuration file
     *
     * @return array   The configuration of the driver
     *
     * Circumvent the private declaration in parent class
     */
    public function getDriverConfig($source)
    {
        return parent::getDriverConfig($source);
    }

    /**
     * GetMyAddress
     *
     * @param array $patron Patron
     *
     * @return array
     */
    public function getMyAddress(array $patron)
    {
        $source = $this->getSource($patron['cat_username'], 'login');
        $driver = $this->getDriver($source);

        if ($driver && $this->methodSupported($driver, 'getMyAddress')) {
            return $driver->getMyAddress($this->stripIdPrefixes($patron, $source));
        }

        return [];
    }

    /**
     * ChangeMyAddress
     *
     * @param array $patron     Patron
     * @param array $newAddress NewAddress
     *
     * @return mixed
     */
    public function changeMyAddress(array $patron, array $newAddress)
    {
        $source = $this->getSource($patron['cat_username'], 'login');
        $driver = $this->getDriver($source);

        if ($driver && $this->methodSupported($driver, 'changeMyAddress')) {
            return $driver->changeMyAddress(
                $this->stripIdPrefixes($patron, $source), $newAddress
            );
        }

        return [];
    }

    /**
     * GetPickupLocations
     *
     * @param array  $patron Patron
     * @param string $id     Id
     * @param string $group  Group
     *
     * @return array
     */
    public function getCopyPickUpLocations(array $patron, $id, $group)
    {
        $source = $this->getSource($patron['cat_username'], 'login');
        $driver = $this->getDriver($source);

        if ($driver && $this->methodSupported($driver, 'getCopyPickUpLocations')) {
            return $driver->getCopyPickUpLocations(
                $this->stripIdPrefixes($patron, $source), $id, $group
            );
        }

        return [];
    }

    /**
     * PutCopy
     *
     * @param array  $patron      Patron
     * @param string $id          Id
     * @param string $group       Group
     * @param array  $copyRequest CopyRequest
     *
     * @return array
     */
    public function putCopy(array $patron, $id, $group, array $copyRequest)
    {
        $source = $this->getSource($patron['cat_username'], 'login');
        $driver = $this->getDriver($source);

        if ($driver && $this->methodSupported($driver, 'changeMyAddress')) {
            return $driver->putCopy(
                $this->stripIdPrefixes($patron, $source), $id, $group, $copyRequest
            );
        }

        return [];
    }
}
