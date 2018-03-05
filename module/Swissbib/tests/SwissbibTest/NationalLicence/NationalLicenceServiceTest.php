<?php
/**
 * NationalLicenceServiceTest.
 *
 * PHP version 5
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 * Date: 1/2/13
 * Time: 4:09 PM
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  SwissbibTest_NationalLicence
 * @author   Simone Cogno  <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace SwissbibTest\NationalLicence;

use Swissbib\Services\NationalLicence;
use SwitchSharedAttributesAPIClient\SwitchSharedAttributesAPIClient as SwitchApi;
use Swissbib\VuFind\Db\Row\NationalLicenceUser;
use VuFindTest\Unit\TestCase as VuFindTestCase;
use Zend\ServiceManager\ServiceManager;
use SwissbibTest\Bootstrap;
use ReflectionClass;
use Zend\Config\Config;
use Zend\Config\Reader\Ini as IniReader;

/**
 * Class NationalLicenceServiceTest.
 *
 * @category Swissbib_VuFind2
 * @package  SwissbibTest_NationalLicence
 * @author   Simone Cogno  <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
class NationalLicenceServiceTest extends VuFindTestCase
{
    /**
     * Service manager.
     *
     * @var ServiceManager
     */
    protected $sm;

    /**
     * National Licence.
     *
     * @var NationalLicence
     */
    protected $nationalLicenceService;

    /**
     * Switch API service
     *
     * @var SwitchApi $switchApiService
     */
    protected $switchApiService;

    /*
     * Config of Switch API
     *
     * @var array
     */
    protected $externalIdTest;

    /*
     * Config of NL
     *
     * @var array
     */
    protected $nationalLicenceConfig;

    /**
     * Set up service manager and National Licence Service.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->sm = Bootstrap::getServiceManager();

        $path = SWISSBIB_TESTS_PATH . '/SwissbibTest/NationalLicence/fixtures/';
        $iniReader = new IniReader();

        $configFull = new Config(
            $iniReader->fromFile($path . 'SwitchApi.ini')
        );
        $configSwitchAPI = $configFull['SwitchApi'];

        $configIni = new Config(
            $iniReader->fromFile($path . 'config.ini')
        );
        $credentials = $configIni['SwitchApiCredentials'];

        $config = array_merge($credentials->toArray(), $configSwitchAPI->toArray());

        $this->switchApiService = new SwitchApi($config);

        $this->externalIdTest = $configSwitchAPI['external_id_test'];

        $configNL = new Config(
            $iniReader->fromFile($path . 'NationalLicencesTest.ini')
        );

        $this->nationalLicenceService
            = new NationalLicence(
                $this->switchApiService,
                null,
                null,
                $configNL,
                $this->sm
            );
    }

    /**
     * Test isSwissPhoneNumber method.
     *
     * @return void
     */
    public function testIsSwissPhoneNumber()
    {
        $testPhones = [
            '+41793433434' => true,
            '+41 79 3433434' => true,
            '+41 773433434' => true,
            '+41 763433434' => true,
            '+41 743433434' => false,
            '+39 793433434' => false,
            null => false,
        ];
        foreach ($testPhones as $phone => $expectedResult) {
            $res = $this->nationalLicenceService->isSwissPhoneNumber($phone);
            $this->assertEquals($expectedResult, $res);
        }
    }

    /**
     * Test isAddressInSwitzerland method.
     *
     * @return void
     */
    public function testAddressIsInSwitzerland()
    {
        $testAddresses = [
            'Route de l\'aurore 10$1700 Fribourg$Switzerland' => true,
            'Theobalds Road 29$WC2N London$England' => false,
            'Roswiesenstrasse 100$8051 Zürich$Switzerland' => true,
            null => false,
        ];
        foreach ($testAddresses as $testAddress => $expectedResult) {
            $res = $this->nationalLicenceService
                ->isAddressInSwitzerland($testAddress);
            $this->assertEquals($expectedResult, $res);
        }
    }

    /**
     * Test isTemporaryAccessCurrentlyValid method.
     *
     * @return void
     * @throws \Exception
     */
    public function testIsTemporaryAccessCurrentlyValid()
    {
        /**
         * National licence user.
         *
         * @var NationalLicenceUser $user
         */
        $user = $this->getNationalLicenceUserObjectInstance();
        $user->edu_id = $this->externalIdTest;
        $user->setExpirationDate(new \DateTime());
        $res = $this->nationalLicenceService
            ->isTemporaryAccessCurrentlyValid($user);
        //$this->assertEquals(true, $res);

        $user->setExpirationDate((new \DateTime())->modify('-1 day'));
        $res = $this->nationalLicenceService
            ->isTemporaryAccessCurrentlyValid($user);
        //$this->assertEquals(false, $res);

        $user->setExpirationDate((new \DateTime())->modify('+1 day'));
        $res = $this->nationalLicenceService
            ->isTemporaryAccessCurrentlyValid($user);
        //$this->assertEquals(true, $res);
    }

    /**
     * Get an instance of the national licence user object.
     *
     * @return NationalLicenceUser
     * @throws \Exception
     */
    protected function getNationalLicenceUserObjectInstance()
    {
        /**
         * National licence user.
         *
         * @var \Swissbib\VuFind\Db\Table\NationalLicenceUser $userTable
         */
        $userTable = $this->sm
            ->get('VuFind\DbTablePluginManager')
            ->get('nationallicence');
        /**
         * National licence user.
         *
         * @var NationalLicenceUser $user
         */
        $user = $userTable->createRow();

        return $user;
    }

    /**
     * Test if the user has acess to national licence content.
     *
     * @return void
     * @throws \Exception
     */
    public function testHasAccessToNationalLicenceContent()
    {
        $externalId = $this->externalIdTest;
        $isOnGroup = $this->switchApiService
            ->userIsOnNationalCompliantSwitchGroup($externalId);
        if (!$isOnGroup) {
            $this->switchApiService
                ->setNationalCompliantFlag($externalId);
        }

        $user = $this->getNationalLicenceUserObjectInstance();
        $this->setFieldsToUser(
            $user, [
                'edu_id' => $externalId,
                'condition_accepted' => false,
                'request_temporary_access' => false,
                'request_permanent_access' => false,
                'date_expiration' => null,
                'blocked' => false,
                'last_edu_id_activity' => null,
            ]
        );
        $res = $this->nationalLicenceService
            ->hasAccessToNationalLicenceContent($user);
        $this->assertEquals(false, $res);

        $user = $this->getNationalLicenceUserObjectInstance();
        $this->setFieldsToUser(
            $user,
            [
                'edu_id' => $externalId,
                'condition_accepted' => false,
                'request_temporary_access' => true,
                'request_permanent_access' => false,
                'date_expiration' => (new \DateTime())->modify('+14 days')
                    ->format('Y-m-d H:i:s'),
                'blocked' => false,
                'active_last_12_month' => true,
            ]
        );
        $res = $this->nationalLicenceService
            ->hasAccessToNationalLicenceContent($user);
        $this->assertEquals(false, $res);

        $user = $this->getNationalLicenceUserObjectInstance();
        $this->setFieldsToUser(
            $user,
            [
                'edu_id' => $externalId,
                'condition_accepted' => true,
                'request_temporary_access' => true,
                'request_permanent_access' => false,
                'date_expiration' => (new \DateTime())->modify('+14 days')
                    ->format('Y-m-d H:i:s'),
                'blocked' => false,
                'active_last_12_month' => true,
            ]
        );
        $res = $this->nationalLicenceService
            ->hasAccessToNationalLicenceContent($user);
        $this->assertEquals(true, $res);

        $user = $this->getNationalLicenceUserObjectInstance();
        $this->setFieldsToUser(
            $user,
            [
                'edu_id' => $externalId,
                'condition_accepted' => true,
                'request_temporary_access' => false,
                'request_permanent_access' => true,
                'date_expiration' => (new \DateTime())->modify('+14 days')
                                                      ->format('Y-m-d H:i:s'),
                'blocked' => false,
                'active_last_12_month' => true,
            ]
        );
        $isOnGroup = $this->switchApiService
            ->userIsOnNationalCompliantSwitchGroup($externalId);
        if ($isOnGroup) {
            $this->switchApiService
                ->unsetNationalCompliantFlag($externalId);
        }

        $res = $this->nationalLicenceService
            ->hasAccessToNationalLicenceContent($user);
        $this->assertEquals(false, $res);
    }

    /**
     * Helper method to modify fields to a NationalLicenceUser instance.
     *
     * @param NationalLicenceUser $user   User
     * @param array               $fields Field
     *
     * @return void
     */
    protected function setFieldsToUser($user, $fields)
    {
        foreach ($fields as $key => $value) {
            $user->$key = $value;
        }
    }

    /**
     * Workaround to print in the unit test console.
     *
     * @param mixed $variable Variable
     *
     * @return void
     */
    public function unitPrint($variable)
    {
        fwrite(STDERR, print_r($variable, true));
    }

    /**
     * Get a reflection class for the SwitchApi service. This is used for call
     * several private or protected methods.
     *
     * @param SwitchApi $originalClass Original class
     *
     * @return ReflectionClass
     */
    protected function getReflectedClass($originalClass)
    {
        $class = new ReflectionClass($originalClass);

        return $class;
    }

    /**
     * Callback function for the Vufind\Config\PluginManager Mock
     *
     * @return Config
     */
    public function myCallback()
    {
        $arguments = func_get_args();
        $arg = $arguments[0];

        $path = SWISSBIB_TESTS_PATH . '/SwissbibTest/NationalLicence/fixtures/';
        $iniReader = new IniReader();

        $configNL = new Config(
            $iniReader->fromFile($path . 'NationalLicencesTest.ini')
        );
        $configUserSwitchAPI = new Config(
            $iniReader->fromFile($path . 'config.ini')
        );

        if ($arg == "NationalLicences") {
            return $configNL;
        } else if ($arg == "config") {
            return $configUserSwitchAPI;
        } else {
            return null;
        }
    }
}
