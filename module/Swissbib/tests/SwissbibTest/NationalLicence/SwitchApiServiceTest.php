<?php
/**
 * SwitchApiServiceTest.
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

use ReflectionClass;
use SwissbibTest\Bootstrap;
use SwitchSharedAttributesAPIClient\SwitchSharedAttributesAPIClient as SwitchApi;
use VuFindTest\Unit\TestCase as VuFindTestCase;
use Zend\Config\Config;
use Zend\Config\Reader\Ini as IniReader;
use Zend\ServiceManager\ServiceManager;

/**
 * Class SwitchApiServiceTest.
 *
 * @category Swissbib_VuFind2
 * @package  SwissbibTest_NationalLicence
 * @author   Simone Cogno  <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
class SwitchApiServiceTest extends VuFindTestCase
{
    /**
     * Reflection class.
     *
     * @var ReflectionClass $switchApiService
     */
    protected $switchApiServiceReflected;
    /**
     * Switch api service
     *
     * @var SwitchApi $switchApiService
     */
    protected $switchApiServiceOriginal;
    /**
     * Config.
     *
     * @var array $config
     */
    protected $externalIdTest;
    /**
     * Service manager.
     *
     * @var ServiceManager $sm
     */
    protected $sm;

    /**
     * Set up service manager and National Licence Service.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->sm = Bootstrap::getServiceManager();

        /* create a Mock of VuFind\Config\PluginManager to read dedicated
         * configuration files for testing
         */

        $path = SWISSBIB_TESTS_PATH . '/SwissbibTest/NationalLicence/fixtures/';
        $iniReader = new IniReader();

        $configFull = new Config(
            $iniReader->fromFile($path . 'SwitchApi.ini')
        );
        $configSwitchAPI = $configFull['SwitchApi'];

        //on Travis the credentials for switch api are stored as an
        //environment variable, defined in travis repository settings
        if (getenv('TRAVIS_SWITCH_API_AUTH_USER')) {
            $credentials = new Config(
                [
                    'auth_user' => getenv('TRAVIS_SWITCH_API_AUTH_USER'),
                    'auth_password' => getenv('TRAVIS_SWITCH_API_AUTH_PASSWORD'),
                ]
            );
        } else {
            $configIni = new Config(
                $iniReader->fromFile($path . 'config.ini')
            );
            $credentials = $configIni['SwitchApiCredentials'];
        }

        $config = array_merge($credentials->toArray(), $configSwitchAPI->toArray());

        $this->switchApiServiceOriginal = new SwitchApi($config);
        $this->switchApiServiceReflected = new ReflectionClass(
            $this->switchApiServiceOriginal
        );

        $this->externalIdTest = $configSwitchAPI['external_id_test'];
    }

    /**
     * Test the unsetNationalCompliantFlag method.
     *
     * @return void
     * @throws \Exception
     */
    public function testUnsetNationalCompliantFlag()
    {
        $externalId = $this->externalIdTest;
        $isOnGroup = $this->switchApiServiceOriginal
            ->userIsOnNationalCompliantSwitchGroup($externalId);
        if (!$isOnGroup) {
            $this->switchApiServiceOriginal
                ->setNationalCompliantFlag($externalId);
        }
        self::assertEquals(
            true, $this->switchApiServiceOriginal
                ->userIsOnNationalCompliantSwitchGroup($externalId)
        );
        $this->switchApiServiceOriginal->unsetNationalCompliantFlag($externalId);
        self::assertEquals(
            false, $this->switchApiServiceOriginal
                ->userIsOnNationalCompliantSwitchGroup($externalId)
        );
    }

    /**
     * Test the setNationalCompliantFlag method.
     *
     * @return void
     * @throws \Exception
     */
    public function testSetNationalCompliantFlag()
    {
        $externalId = $this->externalIdTest;
        $isOnGroup = $this->switchApiServiceOriginal
            ->userIsOnNationalCompliantSwitchGroup($externalId);
        if ($isOnGroup) {
            $method = $this->switchApiServiceReflected
                ->getMethod('createSwitchUser');
            $method->setAccessible(true);
            $internalId = $method->invoke(
                $this->switchApiServiceOriginal,
                $externalId
            );

            $method = $this->switchApiServiceReflected
                ->getMethod('removeUserFromNationalCompliantGroup');
            $method->setAccessible(true);
            $method->invoke($this->switchApiServiceOriginal, $internalId);
        }
        self::assertEquals(
            false,
            $this->switchApiServiceOriginal
                ->userIsOnNationalCompliantSwitchGroup($externalId)
        );
        $this->switchApiServiceOriginal->setNationalCompliantFlag($externalId);
        self::assertEquals(
            true,
            $this->switchApiServiceOriginal
                ->userIsOnNationalCompliantSwitchGroup($externalId)
        );
    }

    /**
     * Get a reflection class for the SwitchApi service. This is used for call
     * several private or protected methods.
     *
     * @param SwitchApi $originalClass Original class
     *
     * @return ReflectionClass
     * @throws \ReflectionException
     */
    protected function getReflectedClass($originalClass)
    {
        $class = new ReflectionClass($originalClass);

        return $class;
    }
}
