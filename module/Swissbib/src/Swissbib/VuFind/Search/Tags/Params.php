<?php
/**
 * Tags aspect of the Search Multi-class (Params)
 *
 * PHP version 7
 *
 * Copyright (C) project swissbib, UB Basel
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
 * @category VuFind2
 * @package  VuFind_Search_Tags
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org   Main Site
 */
namespace Swissbib\VuFind\Search\Tags;

/**
 * Search Tags Parameters
 *
 * @category VuFind2
 * @package  VuFind_Search_Tags
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org   Main Site
 */
class Params extends \VuFind\Search\Tags\Params
{
    /**
     * GetTypeLabel
     *
     * @return string
     */
    public function getTypeLabel()
    {
        return $this->getServiceLocator()->get('Swissbib\TypeLabelMappingHelper')
            ->getLabel($this);
    }
}
