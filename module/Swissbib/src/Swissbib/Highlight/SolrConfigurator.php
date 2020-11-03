<?php
/**
 * Swissbib SolrConfigurator
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  Highlight
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Highlight;

use Laminas\Config\Config;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\SharedEventManagerInterface;

use VuFind\Search\Memory as VFMemory;
use VuFindSearch\Backend\Solr\Backend;

/**
 * Allow configuration of solr highlighting mechanism
 *
 * @category Swissbib_VuFind
 * @package  Highlight
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class SolrConfigurator
{
    /**
     * Solr backend
     *
     * @var Backend
     */
    protected $backend;

    /**
     * Config
     *
     * @var Config
     */
    protected $config;

    /**
     * Events Manager
     *
     * @var SharedEventManagerInterface
     */
    protected $eventsManager;

    /**
     * VuFind Memory
     *
     * @var VFMemory
     */
    protected $memory;

    /**
     * Initialize with event manager and highlight config
     *
     * @param SharedEventManagerInterface $eventsManager EventManager
     * @param Config                      $config        Config
     * @param VFMemory                    $memory        Memory
     */
    public function __construct(
        SharedEventManagerInterface $eventsManager, Config $config, VFMemory $memory
    ) {
        $this->eventsManager = $eventsManager;
        $this->config        = $config;
        $this->memory        = $memory;
    }

    /**
     * Attach event for backend
     *
     * @param Backend $backend Solr backend
     *
     * @return void
     */
    public function attach(Backend $backend)
    {
        $this->backend = $backend;

        $this->eventsManager->attach(
            'VuFind\Search', 'pre', [$this, 'onSearchPre'], -100
        );
    }

    /**
     * Handle event. Add config values
     *
     * @param EventInterface $event Search pre event
     *
     * @return EventInterface
     */
    public function onSearchPre(EventInterface $event)
    {
        $backend = $event->getTarget();

        if ($backend === $this->backend) {
            $params = $event->getParam('params');
            if ($params) {
                // Set highlighting parameters unless explicitly disabled:
                $hl = $params->get('hl');
                if (!isset($hl[0]) || $hl[0] != 'false') {

                        // Add hl.q for non query events
                    if (!$event->getParam('query', false)) {
                        $lastSearch = $this->memory->retrieveSearch();
                        if ($lastSearch) {
                            $urlParams = parse_url($lastSearch);

                            if (isset($urlParams['query'])) {
                                parse_str($urlParams['query'], $queryParams);

                                if (isset($queryParams['lookfor'])) {
                                    $params->set(
                                        'hl.q', '*:"' . addslashes(
                                            $queryParams['lookfor']
                                        )
                                        . '"'
                                    );
                                }
                            }
                        }
                    }

                    // All all highlight config fields
                    foreach ($this->config as $key => $value) {
                        $params->set('hl.' . $key, $value);
                    }
                }
            }
        }

        return $event;
    }
}
