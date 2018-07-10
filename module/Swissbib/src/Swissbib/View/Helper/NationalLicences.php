<?php
/**
 * NationalLicences
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 1/2/13
 * Time: 4:09 PM
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
 * @package  RecordDriver_Helper
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\View\Helper;

use Swissbib\Services\NationalLicence;
use Swissbib\TargetsProxy\IpMatcher;
use VuFind\RecordDriver\SolrDefault;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

/**
 * Return URL for NationalLicence online access if applicable. Otherwise 'false'.
 * Config URLs in TargetsProxy.ini.ini[SwissAcademicLibraries]
 *
 * @category Swissbib_VuFind2
 * @package  RecordDriver_Helper
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class NationalLicences extends AbstractHelper
{
    protected $sm;
    protected $config;
    protected $record;
    protected $marcFields;
    protected $ipMatcher;
    protected $validIps;
    protected $oxfordUrlCode;

    /**
     * National Licence Service
     *
     * @var NationalLicence $nationalLicenceService National Licence Service
     */
    protected $nationalLicenceService;
    protected $remoteAddress;

    /**
     * NationalLicences constructor.
     *
     * @param ServiceManager $sm ServiceManager
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
        $this->config = $sm->get('VuFind\Config\PluginManager')
            ->get("NationalLicences");
        $this->helperManager =  $sm->get('ViewHelperManager');
        $this->ipMatcher = new IpMatcher();

        $sectionPresent = !empty(
            $sm->get('VuFind\Config\PluginManager')->get('config')->SwissAcademicLibraries
        );
        if ($sectionPresent) {
            $this->validIps = explode(
                ",", $sm->get('VuFind\Config\PluginManager')
                    ->get('config')->SwissAcademicLibraries->patterns_ip
            );
        }
        $this->remoteAddress = new RemoteAddress();
        $this->remoteAddress->setUseProxy();
        $trustedProxies = explode(
            ',', $sm->get('VuFind\Config\PluginManager')
                ->get('TargetsProxy')->get('TrustedProxy')->get('loadbalancer')
        );
        $this->remoteAddress->setTrustedProxies($trustedProxies);
        $this->nationalLicenceService = $this->sm->get('Swissbib\NationalLicenceService');

        /*
        Based on Oxford mapping:
           http://www.oxfordjournals.org/en/help/tech-info/linking.html
        */
        $this->oxfordUrlCode =  [
            "asjour" => "asj",
            "afrafj" => "afraf",
            "aibsbu" => "aibsbulletin",
            "ahrrev" => "ahr",
            "alecon" => "aler",
            "alhist" => "alh",
            "analys" => "analysis",
            "annbot" => "aob",
            "amtest" => "amt",
            "biosci" => "bioscience",
            "biosts" => "biostatistics",
            "bjaint" => "bja",
            "bjarev" => "bjaed",
            "brainj" => "brain",
            "phisci" => "bjps",
            "aesthj" => "bjaesthetics",
            "crimin" => "bjc",
            "social" => "bjsw",
            "brimed" => "bmb",
            "cameco" => "cje",
            "camquj" => "camqtly",
            "cs" => "cs",
            "cjilaw" => "chinesejil",
            "computer_journal" => "comjnl",
            "conpec" => "cpe",
            "czoolo" => "cz",
            "databa" => "database",
            "litlin" => "dsh",
            "dnares" => "dnaresearch",
            "earlyj" => "em",
            "enghis" => "ehr",
            "entsoc" => "es",
            "eepige" => "eep",
            "humsup" => "eshremonographs",
            "escrit" => "eic",
            "ehjsupp" => "eurheartjsupp",
            "ehjqcc" => "ehjqcco",
            "seujhf" => "eurjhfsupp",
            "ejilaw" => "ejil",
            "eortho" => "ejo",
            "eursoj" => "esr",
            "famprj" => "fampra",
            "foresj" => "forestry",
            "formod" => "fmls",
            "french" => "fh",
            "frestu" => "fs",
            "frebul" => "fsb",
            "gjiarc" => "gsmnras",
            "geront" => "gerontologist",
            "global" => "globalsummitry",
            "hswork" => "hsw",
            "healed" => "her",
            "hiwork" => "hwj",
            "holgen" => "hgs",
            "icsidr" => "icsidreview",
            "imanum" => "imajna",
            "indcor" => "icc",
            "indlaw" => "ilj",
            "innovait" => "rcgp-innovait",
            "ijclaw" => "icon",
            "inttec" => "ijlit",
            "lexico" => "ijl",
            "intpor" => "ijpor",
            "reflaw" => "ijrl",
            "irasia" => "irap",
            "combul" => "itnow",
            "jrlstu" => "jrls",
            "jncmon" => "jncimono",
            "jafeco" => "jae",
            "jahist" => "jah",
            "japres" => "japr",
            "jbchem" => "jb",
            "jconsl" => "jcsl",
            "eccojcc" => "ecco-jcc",
            "eccojs" => "ecco-jccs",
            "cybers" => "cybersecurity",
            "deafed" => "jdsde",
            "design" => "jdh",
            "jnlecg" => "joeg",
            "envlaw" => "jel",
            "exbotj" => "jxb",
            "jfinec" => "jfec",
            "jhuman" => "jhrp",
            "jis" => "jinsectscience",
            "jicjus" => "jicj",
            "jielaw" => "jiel",
            "islamj" => "jis",
            "jlbios" => "jlb",
            "jleorg" => "jleo",
            "jmvmyc" => "jmvm",
            "jmedent" => "jme",
            "jmther" => "jmt",
            "petroj" => "petrology",
            "jporga" => "jpo",
            "jopart" => "jpart",
            "pubmed" => "jpubhealth",
            "refuge" => "jrs",
            "semant" => "jos",
            "semitj" => "jss",
            "jaarel" => "jaar",
            "hiscol" => "jhc",
            "jalsci" => "jhmas",
            "theolj" => "jts",
            "geron" => "biomedgerontology",
            "gerona" => "biomedgerontology",
            "geronb" => "psychsocgerontology",
            "juecol" => "jue",
            "lawprj" => "lpr",
            "lbaeck" => "leobaeck",
            "libraj" => "library",
            "igpl" => "jigpal",
            "mmycol" => "mmy",
            "modjud" => "mj",
            "molbev" => "mbe",
            "mmmcts" => "mmcts",
            "musicj" => "ml",
            "mtspec" => "mts",
            "musict" => "musictherapy",
            "mtpers" => "mtp",
            "musqtl" => "mq",
            "neuonc" => "neuro-oncology",
            "noprac" => "nop",
            "nconsc" => "nc",
            "nictob" => "ntr",
            "notesj" => "nq",
            "narsym" => "nass",
            "ofidis" => "ofid",
            "operaq" => "oq",
            "oxartj" => "oaj",
            "oxjlsj" => "ojls",
            "omcrep" => "omcr",
            "ecopol" => "oxrep",
            "parlij" => "pa",
            "philoq" => "pq",
            "polana" => "pan",
            "pscien" => "ps",
            "ptpsupp" => "ptps",
            "proeng" => "peds",
            "pparep" => "ppar",
            "pasjap" => "pasj",
            "pubjof" => "publius",
            "qjmedj" => "qjmed",
            "qmathj" => "qjmath",
            "qjmamj" => "qjmam",
            "refqtl" => "rsq",
            "regbio" => "rb",
            "revesj" => "res",
            "revfin" => "rfs",
            "brheum" => "rheumatology",
            "sabour" => "sabouraudia",
            "schbul" => "schizophreniabulletin",
            "sochis" => "shm",
            "socpol" => "sp",
            "ssjapj" => "ssjj",
            "sworkj" => "sw",
            "soceco" => "ser",
            "stalaw" => "slr",
            "tlmsoc" => "tlms",
            "tweceb" => "tcbh",
            "vevolu" => "ve"
        ];
    }

    /**
     * Checks if current user is in IP Range as defined in config-file
     *
     * @return bool
     * @throws \Swissbib\TargetsProxy\Exception
     */
    public function isUserInIpRange()
    {
        $ipAddress = $this->remoteAddress->getIpAddress();
        return  $this->ipMatcher->isMatching($ipAddress, $this->validIps);
        //return boolval($isMatchingIp);
    }

    /**
     * Return the url for the record if it's available with NL, otherwise false
     *
     * @param SolrDefault $record the record object
     *
     * @return bool|String
     */
    public function getUrl(SolrDefault $record)
    {
        if (!($record instanceof \Swissbib\RecordDriver\SolrMarc)) {
            return false;
        }
        $this->record = $record;
        $this->marcFields = $record->getNationalLicenceData();
        if ($this->marcFields[0] !== "NATIONALLICENCE") {
            return false;
        }

        $doi = $record->getDOIs()[0];
        $journalCode = $this->marcFields[2];
        $pii = $this->marcFields[3];

        $message = "";
        $userIsAuthorized = false;
        $hasCommonLibTerms = false;
        $userInIpRange = $this->isUserInIpRange();
        if ($userInIpRange) {
            $userIsAuthorized = true;
        } elseif ($this->isAuthenticatedWithSwissEduId()) {
            try {
                $user = $this->nationalLicenceService
                    ->getCurrentNationalLicenceUser($_SERVER['persistent-id']);
                $userIsAuthorized = $this->nationalLicenceService
                    ->hasAccessToNationalLicenceContent($user);
            } catch (\Exception $e) {
                $userIsAuthorized = false;
            }

            // We want to detect Pura Users here, based on
            // eduPersonEntitlement value
            $commonLibTerms = 'urn:mace:dir:entitlement:common-lib-terms';
            if (isset($_SERVER['entitlement'])
                && $_SERVER['entitlement'] == $commonLibTerms
            ) {
                $hasCommonLibTerms = true;
            }

            //not registered for NL and not a pura user -> send to NL registration
            if (!$userIsAuthorized && !$hasCommonLibTerms) {
                $urlhelper = $this->getView()->plugin("url");
                $url = $urlhelper('national-licences');
                return ['url' => $url, 'message' => ""];
            }
        } elseif ($this->getView()->auth()->getManager()->isLoggedIn()) {
            // we send them to info page asking them to use VPN
            $urlhelper = $this->getView()->plugin("url");
            $url = $urlhelper('national-licences');
            return ['url' => $url, 'message' => ""];
        }

        $url = $this->buildUrl(
            $userInIpRange, $doi, $journalCode, $pii
        );
        if (!$userIsAuthorized
            && !$hasCommonLibTerms
            && !empty($this->config['NationaLicensesWorkflow'])
        ) {
            $loginUrl = $this->config->NationaLicensesWorkflow->swissEduIdLoginLink;
            $loginUrl = str_replace(
                '{SERVER_HTTP_HOST}', $_SERVER['HTTP_HOST'], $loginUrl
            );
            $loginUrl = str_replace(
                '{PUBLISHER_URL}', urlencode(urlencode($url)), $loginUrl
            );
            $url = $loginUrl;
        }

        return ['url' => $url, 'message' => $message];
    }

    /**
     * Build the url.
     *
     * @param String $userAuthorized user authorized?
     * @param String $doi            doi
     * @param String $journalCode    publisher journal code
     * @param String $pii            publisher identifier
     *
     * @return null
     */
    protected function buildUrl($userAuthorized, $doi, $journalCode, $pii
    ) {
        $url = $this->getPublisherBlueprintUrl($userAuthorized);
        $url = str_replace('{DOI}', $doi, $url);
        $url = str_replace(
            '{JOURNAL-URL-CODE}',
            $this->getOxfordUrlCode($journalCode), $url
        );
        $url = str_replace('{PII}', $pii, $url);
        return $url;
    }

    /**
     * Return skeleton for url.
     *
     * @param String $userAuthorized user authorized?
     *
     * @return null
     */
    protected function getPublisherBlueprintUrl($userAuthorized)
    {
        $urlBlueprintKey = ($userAuthorized ? "" : "un") . "authorized";
        $publisher = $this->marcFields[1];
        switch ($publisher) {
        case 'NL-gruyter':
            $urlBlueprintKey = 'nl-gruyter-' . $urlBlueprintKey;
            break;
        case 'NL-cambridge':
            $urlBlueprintKey = 'nl-cambridge-' . $urlBlueprintKey;
            break;
        case 'NL-oxford':
            $urlBlueprintKey = 'nl-oxford-' . $urlBlueprintKey;
            break;
        case 'NL-springer':
            $urlBlueprintKey = 'nl-springer-' . $urlBlueprintKey;
            break;
        }

        $blueprintUrl = "";
        if (!empty($this->config['PublisherUrls'])
            && isset($this->config->PublisherUrls->$urlBlueprintKey)
        ) {
            $blueprintUrl = $this->config->PublisherUrls->$urlBlueprintKey;
        }

        return $blueprintUrl;
    }

    /**
     * Return code to be inserted in the url based on the journal-code
     * which is in the metadata (oxford).
     *
     * @param String $journalCode journalCode in the metadata
     *
     * @return null
     */
    protected function getOxfordUrlCode($journalCode)
    {
        if (isset($this->oxfordUrlCode[$journalCode])) {
            return $this->oxfordUrlCode[$journalCode];
        } else {
            return $journalCode;
        }
    }

    /**
     * Checks if current user is authenticated with swiss edu id.
     *
     * @return bool
     */
    public function isAuthenticatedWithSwissEduId()
    {
        if (empty($this->config['NationaLicensesWorkflow'])) {
            return false;
        }
        $idbName = $this->config->NationaLicensesWorkflow->swissEduIdIDP;
        $persistentId = $_SERVER['persistent-id'] ?? "";
        return (isset($idbName) && !empty($_SERVER['persistent-id'])) ?
            count(preg_grep("/$idbName/", [$persistentId]))
            > 0 : false;
    }
}
