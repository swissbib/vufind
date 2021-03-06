<?php
/**
 * Processor
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  XSLT
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\XSLT;

use DOMDocument;
use VuFind\XSLT\Processor as VFProcessor;
use XSLTProcessor;

/**
 * Processor
 *
 * @category Swissbib_VuFind
 * @package  XSLT
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
class Processor extends VFProcessor
{
    /**
     * Perform an XSLT transformation and return the results.
     *
     * @param string $xslt   Name of stylesheet (in application/xsl directory)
     * @param string $xml    XML to transform with stylesheet
     * @param array  $params Associative array of XSLT parameters
     *
     * @return string      Transformed XML
     */
    public static function process($xslt, $xml, $params = [])
    {
        if ($xslt != 'record-marc.xsl') {
            return parent::process($xslt, $xml, $params);
        }

        $style = new DOMDocument();
        $style->load(APPLICATION_PATH . '/module/Swissbib/xsl/' . $xslt);
        $xsl = new XSLTProcessor();
        $xsl->registerPHPFunctions();
        $xsl->importStyleSheet($style);
        $doc = new DOMDocument();
        if ($doc->loadXML($xml)) {
            foreach ($params as $key => $value) {
                $xsl->setParameter('', $key, $value);
            }

            return $xsl->transformToXML($doc);
        }

        return '';
    }
}
