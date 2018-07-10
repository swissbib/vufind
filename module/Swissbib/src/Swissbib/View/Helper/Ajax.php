<?php
/**
 * AbstractHelper.php
 *
 * PHP Version 7
 *
 * Copyright (C) swissbib 2018
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
 *
 * @category VuFind
 * @package  Swissbib\View\Helper
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Class Ajax
 *
 * Abstract view helper that implements some utilities commonly required for
 * several views.
 *
 * @category VuFind
 * @package  Swissbib\View\Helper
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class Ajax extends AbstractHelper
{
    /**
     * Generates an Ajax URL.
     *
     * @param array  $params     The parameters to merge into the URL.
     * @param string $exclusions A string containing characters to exclude from
     *                           URL-encoding.
     *
     * @return string
     */
    public function url(array $params, string $exclusions = null): string
    {
        $exclusions = null === $exclusions ? '' : $exclusions;
        $pattern = $this->getView()->serverUrl('/AJAX/JSON?%s');
        $url = sprintf($pattern, http_build_query($params));

        for ($index = 0; $index < strlen($exclusions); ++$index) {
            $character = $exclusions[$index];
            $url = str_replace(urlencode($character), $character, $url);
        }

        return $url;
    }

    /**
     * Delegates to the url() method in case parameters are passed in to allow for
     * direct helper invocation. Otherwise the helper itself is returned to provide
     * access to all helper methods.
     *
     * @param array|null  $params     The parameters to merge into the URL.
     * @param string|null $exclusions A string containing characters to exclude from
     *                                URL-encoding.
     *
     * @return string|\Swissbib\View\Helper\Ajax
     */
    public function __invoke(array $params = null, string $exclusions = null)
    {
        return null === $params ? $this : $this->url($params, $exclusions);
    }
}
