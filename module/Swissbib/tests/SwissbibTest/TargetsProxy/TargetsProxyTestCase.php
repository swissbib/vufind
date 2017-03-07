<?php
/**
 * TargetsProxyTestCase
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
 * @package  SwissbibTest_TargetsProxy
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace SwissbibTest\TargetsProxy;

use VuFindTest\Unit\TestCase as VuFindTestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\Log\Logger;
use Zend\Http\PhpEnvironment\Request;
use Zend\Config\Config;

use Swissbib\TargetsProxy\TargetsProxy;

/**
 * TargetsProxyTestCase
 *
 * @category Swissbib_VuFind2
 * @package  SwissbibTest_TargetsProxy
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class TargetsProxyTestCase extends VuFindTestCase
{
    /**
     * TargetsProxy
     *
     * @var TargetsProxy
     */
    protected $targetsProxy;

    /**
     * Initialize targets proxy
     *
     * @param String $configFile ConfigFile
     *
     * @return void
     */
    public function initialize($configFile)
    {
        if (!$this->targetsProxy) {
            $_SERVER['REMOTE_ADDR'] = '1.1.1.1';
            $iniReader = new \Zend\Config\Reader\Ini();
            $config = new Config($iniReader->fromFile($configFile));
            $serviceLocator = new ServiceManager();
            $serviceLocator->setService('VuFind\Config', $config);
            $this->targetsProxy = new TargetsProxy($config, new Logger(), new Request());
            $this->targetsProxy->setSearchClass('Summon');
            $this->targetsProxy->setServiceLocator($serviceLocator);
        }
    }
}
