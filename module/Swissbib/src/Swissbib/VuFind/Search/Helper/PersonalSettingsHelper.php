<?php
/**
 * PersonalSettingsHelper
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
 * @package  VuFind_Search_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\VuFind\Search\Helper;

use VuFind\Auth\Manager;
use Zend\Stdlib\Parameters;

/**
 * Helper to control the application behaviour related to some personal settings
 * up to now:
 * a) length of result list
 * b) sorting of result list
 *
 * @category swissbib / VuFind2
 * @package  VuFind/Search
 * @author   Demian Katz <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 *
 * @codeCoverageIgnore
 */
trait PersonalSettingsHelper
{
    /**
     * HandleLimit
     *
     * @param Manager    $manager      Manager
     * @param Parameters $request      Request
     * @param Integer    $defaultLimit DefaultLimit
     * @param Array      $legalOptions Options
     * @param String     $view         View
     *
     * @return void
     */
    public function handleLimit(Manager $manager, Parameters $request,
        $defaultLimit, $legalOptions, $view
    ) {
        $user = $manager->isLoggedIn();

        $requestParams = $request->toArray();
        if ($user) {
            //in case user changed the the limit with a UI control on the result
            // list or the advanced search page
            //we want to serialize the new value in database
            if (array_key_exists('limitControlElement', $requestParams)
                || array_key_exists('advancedSearchFormRequest', $requestParams)
            ) {
                if (array_key_exists('limit', $requestParams)) {
                    $user->max_hits = (int) $requestParams['limit'];
                    $user->save();
                    $limit =  $requestParams['limit'];
                } else {
                    $limit = $tLimit = $request->get('limit') != $defaultLimit ?
                        $request->get('limit') : $defaultLimit;
                }
            } else {
                //check if there is a stored value in database. If not use the
                // request or default value
                if ($user->max_hits) {
                    $limit = $user->max_hits;
                } else {
                    $limit  =  $tLimit = $request->get('limit') != $defaultLimit ?
                        $request->get('limit') : $defaultLimit;
                }
            }
        } else {
            $limit  =  $tLimit = $request->get('limit') != $defaultLimit ?
                $request->get('limit') : $defaultLimit;
        }

        if (in_array($limit, $legalOptions)
            || ($limit > 0 && $limit < max($legalOptions))
        ) {
            $this->limit = $limit;
            return;
        }

        if ($view == 'rss' && $defaultLimit < 50) {
            $defaultLimit = 50;
        }

        // If we got this far, setting was missing or invalid; load the default
        $this->limit = $defaultLimit;

    }

    /**
     * HandleSort
     *
     * @param Manager    $manager     Manager
     * @param Parameters $request     Parameters
     * @param String     $defaultSort DefaultSort
     * @param String     $target      Target
     *
     * @return mixed
     */
    public function handleSort(Manager $manager, Parameters $request,
        $defaultSort, $target
    ) {
        $user = $manager->isLoggedIn();
        $requestParams = $request->toArray();
        if ($user) {
            //in case user changed the the sort settings on the result list
            // with a specialized UI control
            //we want to serialize the new value in database
            if (array_key_exists('sortControlElement', $requestParams)) {
                if (array_key_exists('sort', $requestParams)) {
                    $sort =  $requestParams['sort'];
                    $dbSort = unserialize($user->default_sort);
                    $dbSort[$target] = $requestParams['sort'];
                    $user->default_sort = serialize($dbSort);
                    $user->save();
                } else {
                    $tSort = $request->get('sort');
                    $sort = !empty($tSort) ? $tSort : $defaultSort;
                }
            } else {
                $tSort = $request->get('sort');
                $sort = !empty($tSort) ? $tSort : $defaultSort;

                //overwrite sort if value is set in database
                if ($user->default_sort) {
                    $userDefaultSort = unserialize($user->default_sort);
                    if (isset($userDefaultSort[$target])) {
                        $sort = $userDefaultSort[$target];
                    }
                }
            }
        } else {
            $sort = $request->get('sort');
        }

        // Check for special parameter only relevant in RSS mode:
        if ($request->get('skip_rss_sort', 'unset') != 'unset') {
            $this->skipRssSort = true;
        }

        return $sort;
    }
}
