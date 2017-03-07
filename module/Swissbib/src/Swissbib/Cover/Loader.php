<?php
/**
 * Base class for loading images (shared by Cover\Loader and QRCode\Loader)
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2007.
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
 * @category VuFind2
 * @package  Cover_Generator
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/use_of_external_content Wiki
 */
namespace Swissbib\Cover;

use VuFind\Cover\Loader as VFLoader;
use VuFindCode\ISBN, VuFind\Content\Covers\PluginManager as ApiManager;

/**
 * Base class for loading images (shared by Cover\Loader and QRCode\Loader)
 *
 * @category VuFind2
 * @package  Cover_Generator
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/use_of_external_content Wiki
 */
class Loader extends VFLoader
{
    /**
     * Support method for loadImage() -- sanitize and store some key values.
     *
     * @param array $settings Settings from loadImage (with missing defaults
     * already filled in).
     *
     * @return void
     */
    protected function storeSanitizedSettings($settings)
    {
        $this->isbn = new ISBN($settings['isbn']);
        if (!empty($settings['issn'])) {
            $rawissn = preg_replace('/[^0-9X]/', '', strtoupper($settings['issn']));
            $this->issn = substr($rawissn, 0, 8);
        } else {
            $this->issn = null;
        }
        $this->oclc = $settings['oclc'];
        $this->upc = $settings['upc'];
        $this->type = $settings['type'];
        $this->size = $settings['size'];
    }

}
