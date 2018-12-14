<?php
/**
 * MARCFormatter
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  XSLT
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\XSLT;

/**
 * MARCFormatter
 *
 * @category Swissbib_VuFind
 * @package  XSLT
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
class MARCFormatter
{
    /**
     * InstitutionUrls
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    protected static $institutionURLs = [
        "ABN" => "http://aleph.ag.ch/F/?local_base=ABN01&con_lng=GER&func=find-b&find_code=SYS&request=%s",
        "ALEX" => "https://www.alexandria.ch/primo-explore/search?query=any,contains,%s&sortby=rank&vid=ALEX",
        "ALEXREPO" => "http://alexandria.unisg.ch/cgi/oai2?verb=GetRecord&identifier=%s&metadataPrefix=oai_dc",
        "BGR" => "http://aleph.gr.ch/F/?local_base=BGR01&con_lng=GER&func=find-b&find_code=SYS&request=%s",
        "BISCH" => "https://webopac.bibliotheken-schaffhausen.ch/TouchPoint_touchpoint/perma.do?q=0%3D%22%s%22+IN+%5B2%5D&v=extern&l=de",
        "BORIS" => "http://boris.unibe.ch/cgi/oai2?verb=GetRecord&identifier=%s&metadataPrefix=oai_dc",
        "CCSA" => "https://nb-posters.primo.exlibrisgroup.com/discovery/fulldisplay?docid=alma%s&context=L&vid=41SNL_53_INST:posters&search_scope=MyInstitution&tab=LibraryCatalog",
        "CEO" => "https://library.olympic.org/Default/doc/SYRACUSE/%s/",
        "CHARCH" => "http://www.helveticarchives.ch/detail.aspx?ID=%s",
        "DDB" => "http://d-nb.info/%s",
        "ECOD" => "http://www.e-codices.unifr.ch/oai/oai.php?verb=GetRecord&metadataPrefix=oai_dc&identifier=https://www.e-codices.unifr.ch/en/list/one/%s",
        "EDOC" => "http://edoc.unibas.ch/cgi/oai2?verb=GetRecord&identifier=%s&metadataPrefix=oai_dc",
        "ETHRESEARCH" => "http://research-collection.ethz.ch/oai/request?verb=GetRecord&identifier=%s&metadataPrefix=qdc",
        "HAN" => "http://aleph.unibas.ch/F/?local_base=DSV05&con_lng=GER&func=find-b&find_code=SYS&request=%s",
        "HEMU" => "http://opacbiblio.hemu-cl.ch/cgi-bin/koha/opac-detail.pl?biblionumber=%s",
        "IDSBB" => "http://aleph.unibas.ch/F/?local_base=DSV01&con_lng=GER&func=find-b&find_code=SYS&request=%s",
        "IDSSG2" => "http://aleph.unisg.ch/F?local_base=HSB02&con_lng=GER&func=direct&doc_number=%s",
        "IDSSG" => "http://aleph.unisg.ch/F?local_base=HSB01&con_lng=GER&func=direct&doc_number=%s",
        "IDSLU" => "http://ilu.zhbluzern.ch/F/?local_base=ILU01&con_lng=GER&func=find-b&find_code=SYS&request=%s",
        "KBTG" => "http://netbiblio.tg.ch/kbtg/search/notice?noticeID=%s",
        "LIBIB" => "http://aleph.lbfl.li/F/?local_base=LLB01&con_lng=GER&func=find-b&find_code=SYS&request=%s",
        "NEBIS" => "http://opac.nebis.ch/F/?local_base=EBI01&con_lng=GER&func=find-b&find_code=SYS&request=%s",
        "OCoLC" => "http://www.worldcat.org/oclc/%s",
        "RERO" => "http://data.rero.ch/01-%s/html",
        "RETROS" => "http://www.e-periodica.ch/oai/dataprovider?verb=GetRecord&metadataPrefix=oai_dc&identifier=%s",
        "SBT" => "http://aleph.sbt.ti.ch/F?local_base=SBT01&con_lng=ITA&func=find-b&find_code=SYS&request=%s",
        "SERVAL" => "http://serval.unil.ch/oaiprovider?verb=GetRecord&metadataPrefix=mods&identifier=oai:serval.unil.ch:%s",
        "SGBN" => "http://aleph.sg.ch/F/?local_base=SGB01&con_lng=GER&func=find-b&find_code=SYS&request=%s",
        "SNL" => "https://nb-helveticat.primo.exlibrisgroup.com/discovery/fulldisplay?docid=alma%s&context=L&vid=41SNL_51_INST:helveticat&search_scope=MyInstitution&tab=LibraryCatalog",
        "VAUD" => "https://renouvaud.hosted.exlibrisgroup.com/primo-explore/search?vid=41BCULIB_VU2&search_scope=41BCULIB_ALMA_ALL&query=any,contains,%s",
        "VAUDS" => "https://renouvaud.hosted.exlibrisgroup.com/primo-explore/search?vid=41BCULIB_VU2&search_scope=41BCULIB_ALMA_ALL&query=any,contains,%s",
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

            return '<a href="' . $url . '" target="_blank">' .
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
}
