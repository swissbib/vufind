<?php
/**
 * AutoSuggestConfig
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
 * @package  View_Helper
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\View\Helper;

use Swissbib\Util\Config\FlatArrayConverter;
use Swissbib\Util\Config\ValueConverter;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Config\Config as ZendConfig;

/**
 * AutoSuggestConfig
 *
 * @category Swissbib_VuFind2
 * @package  View_Helper
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class AutoSuggestConfig extends AbstractHelper
{
    /**
     * ServiceLocator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * ZendConfig
     *
     * @var ZendConfig
     */
    protected $config;


    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $serviceLocator Service locator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * GetConfig
     *
     * @return ZendConfig
     */
    protected function getConfig()
    {
        if (!$this->config) {
            $this->loadAutoSuggestConfig();
        }

        return $this->config;
    }

    /**
     * Get Config
     *
     * @return ZendConfig
     */
    public function __invoke()
    {
        return $this->getConfig();
    }


    /**
     * @private
     */
    private function loadAutoSuggestConfig() {
        $searchesConfig = $this->serviceLocator->getServiceLocator()->get('VuFind\Config')->get('searches');
        $autoCompleteConfig = $searchesConfig->get('Autocomplete');
        $flatArrayConverter = new FlatArrayConverter();
        $valueConverter = new ValueConverter();
        var_dump($autoCompleteConfig);

        $autoSuggestConfig = $flatArrayConverter->fromConfigSections($searchesConfig, 'AutoSuggest');
        $autoSuggestConfig = $autoSuggestConfig->get('AutoSuggest')->toArray();
        $autoSuggestConfig['enabled'] = $valueConverter->isTruthy($autoCompleteConfig->get('enabled'));

        $this->config = $valueConverter->convert(new ZendConfig($autoSuggestConfig));
    }
}