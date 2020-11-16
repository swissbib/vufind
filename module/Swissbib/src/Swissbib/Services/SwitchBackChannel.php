<?php

/**
 * SwitchBackChannel
 *
 * Update user attributes, using Switch Back Channel
 * Info : http://www.swissbib.org/wiki/index.php?title=Switch_Shibboleth_Backchannel_and_Attribute_Query_Plugin
 *
 * PHP version 7
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 12.02.18
 * Time: 11:10
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
 * @package  Services
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Services;

use Laminas\Http\Client;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Swissbib\VuFind\Db\Row\NationalLicenceUser;

/**
 * SwitchBackChannel
 *
 * @category Swissbib_VuFind
 * @package  Services
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class SwitchBackChannel
{
    /**
     * ServiceLocator.
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Swissbib configuration.
     *
     * @var array
     */
    protected $configNL;

    /**
     * SwitchBackChannel constructor.
     *
     * @param array          $configNL NL configuration
     * @param ServiceManager $sm       Service manager.
     */
    public function __construct($configNL, $sm)
    {
        $this->configNL = $configNL;
        $this->setServiceLocator($sm);
    }

    /**
     * Get updated fields about the national licence user.
     *
     * @param string $nameId       Name id
     * @param string $persistentId Persistent id
     *
     * @return NationalLicenceUser
     * @throws \Exception
     */
    public function getUserUpdatedInformation($nameId, $persistentId)
    {
        $updatedUser
            = (array)$this->getNationalLicenceUserCurrentInformation($nameId);
        $nationalLicenceFieldRelation = [
            'mobile' => 'mobile',
            'persistent_id' => 'persistent-id',
            'swiss_library_person_residence' => 'swissLibraryPersonResidence',
            'home_organization_type' => 'homeOrganizationType',
            'edu_id' => 'uniqueID',
            'home_postal_address' => 'homePostalAddress',
            'affiliation' => 'affiliation',
            'active_last_12_month' => 'swissEduIDUsage1y',
            'assurance_level' => 'swissEduIdAssuranceLevel'
        ];
        $userFieldsRelation = [
            'username' => 'persistent-id',
            'firstname' => 'givenName',
            'lastname' => 'surname',
            'email' => 'mail',
        ];

        //to test the email sending on test server
        /*if ($updatedUser["uniqueID"]=="859735645906@eduid.ch") {
            echo "setting false to swissEduIDUsage1y\r\n";
            $updatedUser["swissEduIDUsage1y"] = "FALSE";
        }*/

        $nationalLicenceField = [];
        $userFields = [];
        foreach ($nationalLicenceFieldRelation as $key => $value) {
            if (array_key_exists($value, $updatedUser)) {
                $nationalLicenceField[$key] = $updatedUser[$value];
            }
        }
        foreach ($userFieldsRelation as $key => $value) {
            if (array_key_exists($value, $updatedUser)) {
                $userFields[$key] = $updatedUser[$value];
            }
        }
        /**
         * National Licence user.
         *
         * @var \Swissbib\VuFind\Db\Table\NationalLicenceUser $userTable
         */
        $userTable
            = $this->getTable('nationallicence');
        /**
         * National licence user.
         *
         * @var NationalLicenceUser $user
         */
        return $userTable->updateRowByPersistentId(
            $persistentId,
            $nationalLicenceField,
            $userFields
        );
    }

    /**
     * Get service locator.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator Service locator.
     *
     * @return void
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get an instance of the HTTP Client with some basic configuration
     * for shibboleth back-channel queries.
     *
     * @return Client
     * @throws \Exception
     */
    protected function getBaseClientBackChannel()
    {
        $client = new Client(
            $this->configNL['back_channel_endpoint_host'] .
            $this->configNL['back_channel_endpoint_path'], [
                'maxredirects' => 0,
                'timeout' => 30,
                'adapter'   => 'Laminas\Http\Client\Adapter\Curl',
                'curloptions' => [
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false
                ]
            ]
        );
        $client->setHeaders(
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        );
        $client->setMethod(Request::METHOD_GET);

        return $client;
    }

    /**
     * Get the update attributes of a the national licence user.
     *
     * @param string $nameId Name id
     *
     * @return NationalLicenceUser
     * @throws \Exception
     */
    protected function getNationalLicenceUserCurrentInformation($nameId)
    {
        // @codingStandardsIgnoreStart
        /*
         * Make http request to retrieve new edu-ID information usign the back-
         * channel api
         * example :
         *
         * (very long line)
         * curl -k 'https://localhost/Shibboleth.sso/AttributeResolver?entityID=https%3A%2F%2Feduid.ch%2Fidp%2Fshibboleth&nameId=u0MO2QCF/pU50JKuivCDYPMToIE=&format=urn%3Aoasis%3Anames%3Atc%3ASAML%3A2.0%3Anameid-format%3Apersistent&encoding=JSON%2FCGI'
         *
         * answer :
         * {
         * "mobile" : "+41 79 200 00 00",
         * "swissLibraryPersonResidence" : "CH",
         * "homeOrganizationType" : "others",
         * "uniqueID" : "859735645906@eduid.ch",
         * "homeOrganization" : "eduid.ch",
         * "mail" : "myemail@test.ch",
         * "persistent-id" : "https://eduid.ch/idp/shibboleth!https://test.swissbib.ch/shibboleth!AaduBHpQXrRs9BJqQcB7aLXgWTI=",
         * "swissEduIdAssuranceLevel" : "mobile:https://eduid.ch/def/loa2;mail:https://eduid.ch/def/loa2;homePostalAddress:https://eduid.ch/def/loa2",
         * "givenName" : "Hans",
         * "surname" : "Mustermann",
         * "homePostalAddress" : "Rue Neuve 5$1222 Geneve$Switzerland",
         * "swissEduIDUsage1y" : "TRUE",
         * "affiliation" : "affiliate",
         * "persistent-id" : "https://eduid.ch/idp/shibboleth!https://test.swissbib.ch/shibboleth!AaduBHpQXrRs9BJqQcB7aLXgWTI="
         * }
         */
        // @codingStandardsIgnoreEnd

        /**
         * Client.
         *
         * @var Client $client
         */
        $client = $this->getBaseClientBackChannel();
        $client->setParameterGet(
            [
                'entityID' => $this->configNL['back_channel_param_entityID'],
                'nameId' => $nameId,
                'format' => "urn:oasis:names:tc:SAML:2.0:nameid-format:persistent",
                'encoding' => "JSON/CGI"
            ]
        );
        /**
         * Response.
         *
         * @var Response $response
         */
        $response = $client->send();
        $statusCode = $response->getStatusCode();
        $body = $response->getBody();
        if ($statusCode !== 200 or $body == "{}") {
            throw new \Exception(
                "There was a problem retrieving data for user " .
                "with name id: $nameId. Status code: $statusCode result: $body"
            );
        }

        return json_decode($body);
    }

    /**
     * Get a database table object.
     *
     * @param string $table Name of table to retrieve
     *
     * @return \VuFind\Db\Table\Gateway
     */
    protected function getTable($table)
    {
        return $this->getServiceLocator()
            ->get('VuFind\DbTablePluginManager')
            ->get($table);
    }
}
