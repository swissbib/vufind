<?php

/**
 * Search runner factory.
 *
 * PHP version 7
 *
 * Copyright (C) Villanova University 2018.
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
 * @category VuFind
 * @package  Search
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
namespace Swissbib\VuFind\Search;

use Interop\Container\ContainerInterface;
use VuFind\Search\SearchRunner as VFSearchRunner;
use Zend\EventManager\EventManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * SearchRunnerFactory
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 31.05.18
 * Time: 08:49
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
 * @package  ${PACKAGE}
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */

/**
 * SearchRunnerFactory
 *
 * @category Swissbib_VuFind2
 * @package  ${PACKAGE}
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 * @link     http://www.swissbib.ch
 */
class SearchRunnerFactory implements FactoryInterface
{
    protected $extendedTargets;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        //if (!isset($this->extendedTargets)) {
        //    $mainConfig = $container->get('VuFind\Config\PluginManager')
        //        ->get('config');
        //    $extendedTargetsSearchClassList
        //        = $mainConfig->SwissbibSearchExtensions->extendedTargets;

        //    $this->extendedTargets = array_map(
        //        'trim', explode(',', $extendedTargetsSearchClassList)
        //    );
        //}

        //if (in_array($this->searchClassId, $this->extendedTargets)) {
        //    return new SearchRunner($container->get('VuFind\SearchResultsPluginManager'),
        //        new EventManager($container->get('SharedEventManager')));

        //$this->serviceLocator
        //->get('VuFind\SearchResultsPluginManager');
        //}

        //@Matthias: hier wie besprochen einen "whitelist" Mechanismus über module-config?

        //return new VFSearchRunner($container->get('VuFind\Search\Results\PluginManager'),
        //    new EventManager($container->get('SharedEventManager')));

        //@matthias: hat VuFind jetzt unterschiedliche SearchRunner, so dass sie die Instanz
        //über $requestedName erstellen??

        //@Matthias: Die Factory von VuFind benutzt jetzt diesen namespace
        //VuFind\Search\Results\PluginManager
        // - können wir das auch machen?
        // - brauchen wir unseren alten?
        //ich überblicke das im Moment noch nicht alles...
        return new $requestedName(
            $container->get('VuFind\Search\Results\PluginManager'),
            new EventManager($container->get('SharedEventManager'))
        );
    }
}
