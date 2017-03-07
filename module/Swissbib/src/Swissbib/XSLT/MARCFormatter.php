<?php
/**
 * MARCFormatter
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 8/19/13
 * Time: 10:21 PM
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
 * @package  XSLT
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\XSLT;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

/**
 * MARCFormatter
 *
 * @category Swissbib_VuFind2
 * @package  XSLT
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
class MARCFormatter implements ServiceManagerAwareInterface
{
    /**
     * Service Manager
     *
     * @var array
     */
    private static $_sM;

    /**
     * InstitutionUrls
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    protected static $institutionURLs = [
        "ABN" => "http://aleph.ag.ch/F/?local_base=ABN01&con_lng=GER&func=find-b&find_code=SYS&request=%s",
        "ALEX" => "http://www.alexandria.ch/primo_library/libweb/action/dlSearch.do?institution=BIG&vid=ALEX&scope=default_scope&query=any,contains,%s",
        "BGR" => "http://aleph.gr.ch/F/?local_base=BGR01&con_lng=GER&func=find-b&find_code=SYS&request=%s",
        "BORIS" => "http://boris.unibe.ch/cgi/oai2?verb=GetRecord&identifier=%s&metadataPrefix=oai_dc",
        "CCSA" => "http://permalink.snl.ch/bib/chccsa%s",
        "CHARCH" => "http://www.helveticarchives.ch/detail.aspx?ID=%s",
        "DDB" => "http://d-nb.info/%s",
        "ECOD" => "http://www.e-codices.unifr.ch/oai/oai.php?verb=GetRecord&metadataPrefix=oai_dc&identifier=oai:e-codices.unifr.ch:http://www.e-codices.unifr.ch/en/list/one/%s",
        "HAN" => "http://aleph.unibas.ch/F/?local_base=DSV05&con_lng=GER&func=find-b&find_code=SYS&request=%s",
        "IDSBB" => "http://aleph.unibas.ch/F/?local_base=DSV01&con_lng=GER&func=find-b&find_code=SYS&request=%s",
        "IDSSG2" => "http://aleph.unisg.ch/F?local_base=HSB02&con_lng=GER&func=direct&doc_number=%s",
        "IDSSG" => "http://aleph.unisg.ch/F?local_base=HSB01&con_lng=GER&func=direct&doc_number=%s",
        "IDSLU" => "http://ilu.zhbluzern.ch/F/?local_base=ILU01&con_lng=GER&func=find-b&find_code=SYS&request=%s",
        "LIBIB" => "http://aleph.lbfl.li/F/?local_base=LLB01&con_lng=GER&func=find-b&find_code=SYS&request=%s",
        "NEBIS" => "http://opac.nebis.ch/F/?local_base=EBI01&con_lng=GER&func=find-b&find_code=SYS&request=%s",
        "OCoLC" => "http://www.worldcat.org/oclc/%s",
        "RERO" => "http://opac.rero.ch/gateway?beginsrch=1&lng=de&inst=consortium&search=KEYWORD&function=CARDSCR&t1=%s&u1=12",
        "RETROS" => "http://www.e-periodica.ch/oai/dataprovider?verb=GetRecord&metadataPrefix=oai_dc&identifier=%s",
        "SBT" => "http://aleph.sbt.ti.ch/F?local_base=SBT01&con_lng=ITA&func=find-b&find_code=SYS&request=%s",
        "SERVAL" => "http://serval.unil.ch/oaiprovider?verb=GetRecord&metadataPrefix=mods&identifier=oai:serval.unil.ch:%s",
        "SGBN" => "http://aleph.sg.ch/F/?local_base=SGB01&con_lng=GER&func=find-b&find_code=SYS&request=%s",
        "SNL" => "http://permalink.snl.ch/bib/sz%s",
        "VAUD" => "http://renouvaud.hosted.exlibrisgroup.com/primo_library/libweb/action/dlSearch.do?&institution=41BCULIB&vid=41BCULIB_VU1&search_scope=41BCULIB_ALMA_ALL&query=any,contains,%s",
        "ZORA" => "http://www.zora.uzh.ch/cgi/oai2?verb=GetRecord&metadataPrefix=oai_dc&identifier=%s",
    ];
    // @codingStandardsIgnoreEnd

    /**
     * TrimPrefixes
     *
     * @var array
     */
    protected static $trimPrefixes = [
        "vtls",
        "on",
        "ocn",
        "ocm",
        "cha"
    ];

    /**
     * CompileSubfield
     *
     * @param array $domArray DomArray
     *
     * @return mixed
     */
    public static function compileSubfield(array $domArray)
    {
        $domNode = $domArray[0];
        if ($domNode->parentNode !== null
            && $domNode->parentNode->getAttribute('tag') != '035'
        ) {
            return $domNode; //return before trying to find institution
        }

        $nodeValue = preg_replace('/\s+/', '', $domNode->textContent);
        $institution = self::getInstitutionFromNodeText($nodeValue);

        if ($domNode->getAttribute('code') != 'a' || empty($institution)) {
            return $domNode;
        } else {
            $request = substr($nodeValue, strlen($institution) + 2);
            $request = str_replace(self::$trimPrefixes, '', $request);
            $url = str_replace('%s', $request, self::$institutionURLs[$institution]);

            $pW =  static::$_sM->get("Swissbib\Services\RedirectProtocolWrapper");

            return '<a href="' . $pW->getWrappedURL($url) . '" target="_blank">' .
                htmlentities('(' . $institution . ')' . $request) . '</a>';
        }
    }

    /**
     * GetInstitutionFromNodeText
     *
     * @param String $nodeText NodeText
     *
     * @return String
     */
    protected static function getInstitutionFromNodeText($nodeText)
    {
        preg_match('/\(([a-zA-Z0-9]+)\)/', $nodeText, $matches);

        if (count($matches) == 0) {
            return '';
        }
        $match = $matches[1];
        if (!empty($match)) {
            foreach (self::$institutionURLs as $key => $value) {
                if ($match === $key) {
                    return $key;
                }
            }
        }

        return '';
    }

    /**
     * Set service manager
     *
     * @param ServiceManager $serviceManager ServiceManager
     *
     * @return void
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        static::$_sM = $serviceManager;
    }
}
