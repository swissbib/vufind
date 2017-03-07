<?php
/**
 * Translate View Helper
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 9/12/13
 * Time: 11:46 AM
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
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\VuFind\View\Helper\Root;

use Zend\I18n\Exception\RuntimeException,
    Zend\I18n\View\Helper\AbstractTranslatorHelper;
use VuFind\View\Helper\Root\Translate as VFTranslate;

/**
 * Translate view helper
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_View_Helper_Root
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
class Translate extends VFTranslate
{
    /**
     * Translate a string
     *
     * @param string $str     String to translate
     * @param array  $tokens  Tokens to inject into the translated string
     * @param string $default Default value to use if no translation is found (null
     * for no default).
     *
     * @return string
     */
    public function __invoke($str, $tokens = [], $default = null)
    {
        $msg = $this->processTranslation($str, $default);

        // Do we need to perform substitutions?
        if (!empty($tokens)) {
            $in = $out = [];
            foreach ($tokens as $key => $value) {
                $in[] = $key;
                $out[] = $value;
            }
            $msg = str_replace($in, $out, $msg);
        }

        return $msg;
    }

    /**
     * Extract text-domain from label. Use text-domain "default" if none given.
     *
     * Pattern is textDomain::labelKey
     *
     * @param String $str String to detect the text-domain from
     *
     * @return array
     */
    protected function extractTextDomain($str)
    {
        $parts = explode('::', $str);

        if (count($parts) === 2) {
            return $parts;
        }

        return ['default', $str];
    }

    /**
     * TranslateFacet
     *
     * @param String $facetName  FacetName
     * @param String $facetValue FacetValue
     *
     * @return null|string
     */
    public function translateFacet($facetName, $facetValue)
    {
        if (in_array($facetName, $this->translatedFacets)) {
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
                    $facetValue . '::' .
                    substr($fieldToEvaluate, strpos($fieldToEvaluate, ':') + 1)
                ) : $facetValue;
        } else {
            return $facetValue;
        }
    }

    /**
     * ProcessTranslation
     *
     * @param String $str     String to translate
     * @param String $default Default value if translation failes
     *
     * @return string
     */
    protected function processTranslation($str, $default = null)
    {
        try {
            $translator = $this->getTranslator();
            if (!is_object($translator)) {
                throw new RuntimeException();
            }

            list($textDomain, $str) = $this->extractTextDomain($str);

            $msg = $translator->translate($str, $textDomain);
        } catch (RuntimeException $e) {
            // If we get called before the translator is set up, it will throw an
            // exception, but we should still try to display some text!
            $msg = $str;
        }

        // Did the translation fail to change anything?  If so, use default:
        if (!is_null($default) && $msg == $str) {
            $msg = $default;
        }

        return $msg;
    }
}
