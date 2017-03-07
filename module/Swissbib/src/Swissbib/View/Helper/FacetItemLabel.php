<?php
/**
 * FacetItemLabel
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
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\View\Helper;

use Zend\I18n\View\Helper\Translate;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\EscapeHtml;

/**
 * Renders a facet item label
 *
 * @category Swissbib_VuFind2
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class FacetItemLabel extends AbstractHelper
{
    /**
     * EscapeHtml
     *
     * @var EscapeHtml
     */
    protected $escaper;

    /**
     * Zend translate view helper
     *
     * @var Translate
     */
    protected $translator;

    /**
     * Number
     *
     * @var Number
     */
    protected $number;

    /**
     * Mapping for special facets to textDomain. Text domains need to be added in
     * bootstrapper initLanguage()
     *
     * @var Array
     */
    protected $customTranslations = [
        'institution' => 'institution',
        'union'       => 'union',
    ];

    /**
     * Invoke FacetItemLabel
     *
     * @param Array  $facet     Facet
     * @param String $facetType FacetType
     *
     * @return String
     */
    public function __invoke(array $facet, $facetType)
    {
        $displayText = trim($facet['displayText']);
        $count       = intval($facet['count']);

        if (!isset($this->escaper)) {
            $this->escaper    = $this->getView()->plugin('escapeHtml');
            $this->number    = $this->getView()->plugin('number');
        }
        $escaper = $this->escaper;
        $number  = $this->number;

        if (isset($this->customTranslations[$facetType])) {
            if (!isset($this->translator)) {
                $this->translator = $this->getView()->plugin('zendTranslate');
            }
            $translator  = $this->translator;
            $textDomain  = $this->customTranslations[$facetType];
            $displayText = $translator($displayText, $textDomain);
        }

        return $escaper($displayText) . '&nbsp;(' . $number($count) . ')';
    }
}
