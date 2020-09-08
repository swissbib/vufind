<?php
/**
 * AutoSuggestConfig
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  View_Helper
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\View\Helper;

use Swissbib\Util\Config\FlatArrayConverter;
use Swissbib\Util\Config\ValueConverter;
use Laminas\Config\Config as LaminasConfig;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Helper\AbstractHelper;

/**
 * AutoSuggestConfig
 *
 * @category Swissbib_VuFind
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
     * LaminasConfig
     *
     * @var LaminasConfig
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
     * @param String $searchClassId searchClassId
     *
     * @return LaminasConfig
     */
    protected function getConfig(String $searchClassId = '')
    {
        if (!$this->config) {
            $this->_loadAutoSuggestConfig($searchClassId);
        }

        return $this->config;
    }

    /**
     * Get Config
     *
     * @param String $searchClassId searchClassId
     *
     * @return LaminasConfig
     */
    public function __invoke(String $searchClassId = '')
    {
        return $this->getConfig($searchClassId);
    }

    /**
     * Loads the auto suggest config
     *
     * @param String $searchClassId searchClassId
     *
     * @return void
     */
    private function _loadAutoSuggestConfig(String $searchClassId)
    {
        $flatArrayConverter = new FlatArrayConverter();
        $valueConverter = new ValueConverter();

        $searchesConfig
            = $this->serviceLocator->get('VuFind\Config')->get(
                'searches'
            );
        $autoSuggestEnabled
            = $this->_isAutoSuggestEnabled($searchesConfig, $valueConverter);

        if ($searchClassId == 'Summon') {
            $summonConfig = $this->serviceLocator->get('VuFind\Config')->get(
                'Summon'
            );
            if (isset($summonConfig->AutoSuggest->enabled)) {
                $autoSuggestEnabled = $summonConfig->AutoSuggest->enabled;
            }
        }

        $autoSuggestConfig
            = $flatArrayConverter->fromConfigSections(
                $searchesConfig, 'AutoSuggest'
            );
        $autoSuggestConfig = $autoSuggestConfig->get('AutoSuggest')->toArray();
        $autoSuggestConfig['enabled'] = $autoSuggestEnabled;

        $this->config = $valueConverter->convert(
            new LaminasConfig($autoSuggestConfig)
        );
    }

    /**
     * Is auto suggest enabled in config?
     *
     * @param LaminasConfig     $searchesConfig Config
     * @param ValueConverter $converter      Converter
     *
     * @return bool
     */
    private function _isAutoSuggestEnabled(
        \Laminas\Config\Config $searchesConfig, ValueConverter $converter
    ) {
        // Note: VuFind autocomplete already provides an enabled state information,
        // but unfortunately switching it on
        // results in client-side errors in autocomplete.js,
        // so we separated enabled state validation into this method
        // to be able to include it, once the error's source has been encountered.
        // The separate enabled configuration in
        // the AutoSuggest section in the searches.ini is then no longer
        // required and enabled state can be merged from Autocomplete section.
        $autocompleteEnabled = false;
        // $autocompleteEnabled = isset($searchesConfig->Autocomplete->enabled)
        // ? $converter->isTruthy($searchesConfig->Autocomplete->enabled)
        // : false;
        $autoSuggestEnabled = isset($searchesConfig->AutoSuggest)
        && isset($searchesConfig->AutoSuggest->enabled) ? $converter->isTruthy(
            $searchesConfig->AutoSuggest->enabled
        ) : false;

        return $autocompleteEnabled || $autoSuggestEnabled;
    }
}
