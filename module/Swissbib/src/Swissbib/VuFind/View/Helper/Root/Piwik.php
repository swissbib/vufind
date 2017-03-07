<?php
/**
 * Piwik
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 22/10/15
 * Time: 18:51
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
 * @package  VuFind_View_Helper_Root
 * @author   Markus Mächler <markus.maechler@bithost.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\VuFind\View\Helper\Root;

use VuFind\View\Helper\Root\Piwik as VuFindPiwik;

/**
 * Piwik
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_View_Helper_Root
 * @author   Markus Mächler <markus.maechler@bithost.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class Piwik extends VuFindPiwik
{
    /**
     * Get the Initialization Part of the Tracking Code
     *
     * @return string JavaScript Code Fragment
     */
    protected function getOpeningTrackingCode()
    {
        return <<<EOT

window.piwikAsyncInit = function() {
    try {
        var VuFindPiwikTracker = Piwik.getTracker();

        VuFindPiwikTracker.setSiteId({$this->siteId});
        VuFindPiwikTracker.setTrackerUrl('{$this->url}piwik.php');
        VuFindPiwikTracker.setCustomUrl(location.protocol + '//'
            + location.host + location.pathname);

EOT;
    }

    /**
     * Get the Finalization Part of the Tracking Code
     *
     * @return string JavaScript Code Fragment
     */
    protected function getClosingTrackingCode()
    {
        return <<<EOT

        VuFindPiwikTracker.enableLinkTracking();
    } catch (e) {}
};
(function(){
var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.defer=true; g.async=true;
    g.src='{$this->url}piwik.js';
s.parentNode.insertBefore(g,s); })();
EOT;
    }
}
