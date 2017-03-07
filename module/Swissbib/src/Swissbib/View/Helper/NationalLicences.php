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

use Zend\View\Helper\AbstractHelper;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Swissbib\TargetsProxy\IpMatcher;
use VuFind\RecordDriver\SolrDefault;
use Zend\ServiceManager\ServiceManager;

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
        $this->config = $sm->getServiceLocator()->get('VuFind\Config')
            ->get("NationalLicences");
        $this->helperManager =  $sm->getServiceLocator()->get('viewhelpermanager');
        $this->ipMatcher = new IpMatcher();

        $sectionPresent = !empty(
            $sm->getServiceLocator()
                ->get('VuFind\Config')->get('config')->SwissAcademicLibraries
        );
        if ($sectionPresent) {
            $this->validIps = explode(
                ",", $sm->getServiceLocator()->get('VuFind\Config')
                    ->get('config')->SwissAcademicLibraries->patterns_ip
            );
        }
        $this->remoteAddress = new RemoteAddress();
        $this->remoteAddress->setUseProxy();
        $trustedProxies = explode(
            ',', $sm->getServiceLocator()->get('VuFind\Config')
                ->get('TargetsProxy')->get('TrustedProxy')->get('loadbalancer')
        );
        $this->remoteAddress->setTrustedProxies($trustedProxies);
        $this->nationalLicenceService = $this->sm->getServiceLocator()
            ->get('Swissbib\NationalLicenceService');

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

        $issn = $this->marcFields[3];
        $enumeration = $this->marcFields[2];
        if (strpos($enumeration, ':') !== false) {
            $splitted = explode(":", $enumeration);
            $volume = $splitted[0];
            $issuePage = explode("<", $splitted[1]);
            $issue = $issuePage[0];
            $page = $issuePage[1];
        } else {
            $volumeIssue = explode("<", $enumeration);
            $volume = $volumeIssue[0];
            $issue = "";
            $page = $volumeIssue[1];
        }
        $doi = $record->getDOIs()[0];
        $journalCode = $this->marcFields[4];
        $pii = $this->marcFields[5];

        $message = "";
        $userIsAuthorized = false;
        $userInIpRange = $this->isUserInIpRange();
        if ($userInIpRange) {
            $userIsAuthorized = true;
        } else if ($this->isAuthenticatedWithSwissEduId()) {
            $user = $this->nationalLicenceService
                ->getOrCreateNationalLicenceUserIfNotExists(
                    $_SERVER['persistent-id']
                );
            $userIsAuthorized = $this->nationalLicenceService
                ->hasAccessToNationalLicenceContent($user);
            if (!$userIsAuthorized) {
                $urlhelper = $this->getView()->plugin("url");
                $url = $urlhelper('national-licences');
                return ['url' => $url , 'message' => ""];
            }
        } else if ($this->getView()->auth()->getManager()->isLoggedIn()) {
            // we send them to info page asking them to use VPN
            $urlhelper = $this->getView()->plugin("url");
            $url = $urlhelper('national-licences');
            return ['url' => $url , 'message' => ""];
        }

        $url = $this->buildUrl(
            $userInIpRange, $issn, $volume,
            $issue, $page, $pii, $doi, $journalCode
        );
        if (!$userIsAuthorized
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

        return ['url' => $url , 'message' => $message];
    }

    /**
     * Build the url.
     *
     * @param String $userAuthorized user authorized?
     * @param String $issn           issn
     * @param String $volume         volume
     * @param String $issue          issue
     * @param String $sPage          start page
     * @param String $pii            publisher article identifier
     * @param String $doi            doi
     * @param String $journalCode    publisher journal code
     *
     * @return null
     */
    protected function buildUrl($userAuthorized, $issn, $volume,
        $issue, $sPage, $pii, $doi, $journalCode
    ) {

        $url = $this->getPublisherBlueprintUrl($userAuthorized);
        $url = str_replace('{ISSN}', $issn, $url);
        $url = str_replace('{VOLUME}', $volume, $url);
        $url = str_replace('{ISSUE}', $issue, $url);
        $url = str_replace('{SPAGE}', $sPage, $url);
        $url = str_replace('{PII}', $pii, $url);
        $url = str_replace('{DOI}', $doi, $url);
        $url = str_replace(
            '{JOURNAL-URL-CODE}',
            $this->getOxfordUrlCode($journalCode), $url
        );
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
        $persistentId = isset($_SERVER['persistent-id']) ?
            $_SERVER['persistent-id'] : "";
        return (isset($idbName) && !empty($_SERVER['persistent-id'])) ?
            count(preg_grep("/$idbName/", [$persistentId]))
            > 0 : false;
    }

}
