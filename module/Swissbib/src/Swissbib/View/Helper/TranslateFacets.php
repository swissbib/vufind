<?php
/**
 * TranslateFacets
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

use Swissbib\VuFind\View\Helper\Root\Translate as SwissbibTranslate;

/**
 * Translate locations
 *
 * @category Swissbib_VuFind2
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class TranslateFacets extends SwissbibTranslate
{
    /**
     * Array of facets to be translated (with optional translation domain
     * facet name:domain name
     *
     * @var array
     */
    protected $translatedFacets = [];

    /**
     * TranslatedFacets
     *
     * @param array $translatedFacets Array of translated facets
     */
    public function __construct($translatedFacets = [])
    {
        $this->translatedFacets = $translatedFacets;
    }

    /**
     * Invoke translateFacets
     *
     * @param array  $str     Must be an array because we need multiple values
     *                        ['facetName' => 'name', 'facetValue' => 'value']
     * @param array  $tokens  Tokens to inject into the translated string
     * @param string $default Default value to use if no translation is found (null
     *                        for no default).
     *
     * @return string
     */
    public function __invoke($str, $tokens = [], $default = null)
    {
        if (!is_array($str)) {
            return '';
        }

        $facetName = $str['facetName'];
        $facetValue = $str['facetValue'];

        $fieldToTranslateInArray =  array_filter(
            $this->translatedFacets, function ($passedValue) use ($facetName) {
                return $passedValue === $facetName
                    || count(
                        preg_grep("/" . $facetName . ":" . "/", [$passedValue])
                    ) > 0;
            }
        );

        $translate = count($fieldToTranslateInArray) > 0;
        $fieldToEvaluate = $translate ? current($fieldToTranslateInArray) : null;

        return $translate ? strstr($fieldToEvaluate, ':') === false ?
            $this->processTranslation($facetValue) :
            $this->processTranslation(
                substr(
                    $fieldToEvaluate,
                    strpos($fieldToEvaluate, ':') + 1
                ) . '::' . $facetValue
            ) : $facetValue;
    }
}
