<?php
namespace Swissbib\Module\Config;

use Swissbib\Command\HierarchyCache\HierarchyCache;
use Swissbib\Command\HierarchyCache\HierarchyCacheFactory;
use Swissbib\Command\Libadmin\LibAdminSync;
use Swissbib\Command\Libadmin\LibAdminSyncFactory;
use Swissbib\Command\Libadmin\LibAdminSyncGeoJson;
use Swissbib\Command\Libadmin\LibAdminSyncGeoJsonFactory;
use Swissbib\Command\Libadmin\LibAdminSyncMapPortal;
use Swissbib\Command\Libadmin\LibAdminSyncMapPortalFactory;
use Swissbib\Command\NationalLicences\SendNationalLicenceUserExport;
use Swissbib\Command\NationalLicences\SendNationalLicenceUserExportFactory;
use Swissbib\Command\NationalLicences\UpdateNationalLicenceUserInfo;
use Swissbib\Command\NationalLicences\UpdateNationalLicenceUserInfoFactory;
use Swissbib\Command\Pura\SendPuraReport;
use Swissbib\Command\Pura\SendPuraReportFactory;
use Swissbib\Command\Pura\UpdatePuraUser;
use Swissbib\Command\Pura\UpdatePuraUserFactory;
use Swissbib\Command\Tab40Import\Tab40Import;
use Swissbib\Command\Tab40Import\Tab40ImportFactory;
use Swissbib\Controller\CoverController;
use Swissbib\Controller\FavoritesController;
use Swissbib\Controller\FeedbackController;
use Swissbib\Controller\HelpPageController;
use Swissbib\Controller\HoldingsController;
use Swissbib\Controller\MyResearchController;
use Swissbib\Controller\MyResearchNationalLicensesController;
use Swissbib\Controller\NationalLicencesController;
use Swissbib\Controller\PuraController;
use Swissbib\Controller\RecordController;
use Swissbib\Controller\SearchController;
use Swissbib\Controller\SummonController;
use Swissbib\RecordDriver\Summon;
use Swissbib\VuFind\Search\SearchRunnerFactory;
use VuFind\Controller\AbstractBaseFactory;
use VuFind\Route\RouteGenerator;

$config = [
    'router' => [
        'routes' => [
            // ILS location, e.g. baselbern
            'accountWithLocation' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/MyResearch/:action/:location',
                    'defaults'    => [
                        'controller' => MyResearchController::class,
                        'action'     => 'Profile',
                        'location'   => 'baselbern'
                    ],
                    'constraints' => [
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'location' => '[a-z]+',
                    ],
                ]
            ],
            // Search results with tab
            'search-results' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/Search/Results[/:tab]',
                    'defaults' => [
                        'controller' => 'Search',
                        'action'     => 'results'
                    ]
                ]
            ],
            // load availability by library network with ajax
            'availability-resultlist-ajax' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/Search/:record/AvailabilityByLibraryNetwork',
                    'defaults' => [
                        'controller' => SearchController::class,
                        'action'     => 'availabilityByLibraryNetwork'
                    ]
                ]
            ],
            // (local) Search User Settings
            'myresearch-settings' => [
                'type'    => 'literal',
                'options' => [
                    'route'    => '/MyResearch/Settings',
                    'defaults' => [
                        'controller' => MyResearchController::class,
                        'action'     => 'settings'
                    ]
                ]
            ],
            // Swiss National Licences
            'national-licences' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/NationalLicences[/:action]',
                    'defaults' => [
                        'controller' => NationalLicencesController::class,
                        'action'     => 'index'
                    ],
                    'constraints' => [
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ],
                ]
            ],
            // Swiss National Licences
            'national-licenses-signpost' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/MyResearchNationalLicenses[/:action]',
                    'defaults' => [
                        'controller' => MyResearchNationalLicensesController::class,
                        'action'     => 'nlsignpost'
                    ],
                    'constraints' => [
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ],
                ]
            ],
            // Pura
            'pura' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/MyResearch/Pura',
                    'defaults' => [
                        'controller' => \Swissbib\Controller\PuraController::class,
                        'action'     => 'index'
                    ]
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'library' => [
                        'type'    => 'segment',
                        'options' => [
                            'route'       => '/library/:libraryCode[/:page]',
                            'defaults'    => [
                                'controller' => \Swissbib\Controller\PuraController::class,
                                'action' => 'library',
                            ],
                            'constraints' => [
                                'libraryCode' => 'Z01|E65',
                                'page' => 'registration|listResources'
                            ],
                        ],
                    ],
                    'barcode' => [
                        'type'    => 'segment',
                        'options' => [
                            'route'       => '/barcode/:token[/:size]',
                            'defaults'    => [
                                'controller' => \Swissbib\Controller\PuraController::class,
                                'action' => 'barcode',
                            ],
                            'constraints' => [
                                'token' => '[A-Z0-9]*',
                                'size' => 'big',
                            ],
                        ],
                    ]
                ]
            ],
            'help-page' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/HelpPage[/:topic]',
                    'defaults' => [
                        'controller' => HelpPageController::class,
                        'action'     => 'index'
                    ]
                ]
            ],
            'holdings-ajax' => [ // load holdings details for record with ajax
                'type'    => 'segment',
                'options' => [
                    'route'    => '/Holdings/:record/:institution',
                    'defaults' => [
                        'controller' => HoldingsController::class,
                        'action'     => 'list'
                    ]
                ]
            ],
            'holdings-holding-items' => [ // load holding holdings details for record with ajax
                'type'    => 'segment',
                'options' => [
                    'route'    => '/Holdings/:record/:institution/items/:resource',
                    'defaults' => [
                        'controller' => HoldingsController::class,
                        'action'     => 'holdingItems'
                    ]
                ]
            ],
            'myresearch-favorite-institutions' => [ // display defined favorite institutions
                'type'    => 'segment',
                'options' => [
                    'route'    => '/MyResearch/Favorites[/:action]',
                    'defaults' => [
                        'controller' => FavoritesController::class,
                        'action'     => 'display'
                    ]
                ]
            ],
            'myresearch-favorites' => [ // Override vufind favorites route. Rename to Lists
                'type'    => 'literal',
                'options' => [
                    'route'    => '/MyResearch/Lists',
                    'defaults' => [
                        'controller' => MyResearchController::class,
                        'action'     => 'favorites'
                    ]
                ]
            ],
            'myresearch-photocopies' => [ // Override vufind favorites route. Rename to Lists
                'type'    => 'literal',
                'options' => [
                    'route'    => '/MyResearch/Photocopies',
                    'defaults' => [
                        'controller' => MyResearchController::class,
                        'action'     => 'photocopies'
                    ]
                ]
            ],
            'myresearch-bookings' => [ // Override vufind favorites route. Rename to Lists
                'type' => 'literal', 'options' => [
                    'route' => '/MyResearch/Bookings', 'defaults' => [
                        'controller' => MyResearchController::class, 'action' => 'bookings'
                    ]
                ]
            ], 'myresearch-changeaddress' => [
                'type' => 'literal', 'options' => [
                    'route' => '/MyResearch/Address', 'defaults' => [
                        'controller' => MyResearchController::class, 'action' => 'changeAddress'
                    ]
                ]
            ], 'record-copy' => [
                'type' => 'segment', 'options' => [
                    'route' => '/Record/:id/Copy', 'defaults' => [
                        'controller' => RecordController::class, 'action' => 'copy'
                    ]
                ]
            ], 'card-knowledge-person' => [
                'type' => 'segment', 'options' => [
                    'route' => '/Card/Knowledge/Person/:id', 'constraints' => [
                        'id' => '[a-fA-F0-9-{}]+',
                    ], 'defaults' => [
                        'controller' => 'person-knowledge-card', 'action' => 'person',
                    ],
                ]
            ], 'card-knowledge-organisation' => [
                'type' => 'segment', 'options' => [
                    'route' => '/Card/Knowledge/Organisation/:id', 'constraints' => [
                        'id' => '[a-fA-F0-9-{}]+',
                    ], 'defaults' => [
                        'controller' => 'organisation-knowledge-card', 'action' => 'organisation',
                    ],
                ]
            ], 'card-knowledge-subject' => [
                'type' => 'segment', 'options' => [
                    'route' => '/Card/Knowledge/Subject/:id', 'constraints' => [
                        'id' => '[0-9A-Z\--{}]+',
                    ], 'defaults' => [
                        'controller' => 'subject-knowledge-card', 'action' => 'subject',
                    ],
                ]
            ], 'page-detail-person' => [
                'type' => 'segment', 'options' => [
                    'route' => '/Page/Detail/Person/:id', 'constraints' => [
                        'id' => '[a-fA-F0-9-{}]+',
                    ], 'defaults' => [
                        'controller' => 'person-detail-page', 'action' => 'person',
                    ],
                ]
            ], 'page-detail-organisation' => [
                'type' => 'segment', 'options' => [
                    'route' => '/Page/Detail/Organisation/:id', 'constraints' => [
                        'id' => '[a-fA-F0-9-{}]+',
                    ], 'defaults' => [
                        'controller' => 'organisation-detail-page', 'action' => 'organisation',
                    ],
                ]
            ], 'page-detail-subject' => [
                'type' => 'segment', 'options' => [
                    'route' => '/Page/Detail/Subject/:id', 'constraints' => [
                        'id' => '[0-9A-Z\--{}]+',
                    ], 'defaults' => [
                        'controller' => 'subject-detail-page', 'action' => 'subject',
                    ],
                ]
            ],
            'persons-search-coauthor' => [
                'type'    => 'literal',
                'options' => [
                    'route'    => '/Search/Persons/CoAuthor',
                    'defaults' => [
                        'controller' => 'person-search',
                        'action'     => 'coauthor'
                    ]
                ]
            ],
            'persons-search-samegenre' => [
                'type'    => 'literal',
                'options' => [
                    'route'    => '/Search/Persons/Genre',
                    'defaults' => [
                        'controller' => 'person-search',
                        'action'     => 'samegenre'
                    ]
                ]
            ],
            'persons-search-samemovement' => [
                'type'    => 'literal',
                'options' => [
                    'route'    => '/Search/Persons/Movement',
                    'defaults' => [
                        'controller' => 'person-search',
                        'action'     => 'samemovement'
                    ]
                ]
            ],
            'persons-search-subject' => [
                'type'    => 'literal',
                'options' => [
                    'route'    => '/Search/Persons/Subject',
                    'defaults' => [
                        'controller' => 'person-search',
                        'action'     => 'subject'
                    ]
                ]
            ],
            'organisations-search-hierarchicalsuperiors' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/Search/Organisations/HierarchicalSuperiors/:id', 'constraints' => [
                        'id' => '[0-9A-Z\--{}]+',
                    ], 'defaults' => [
                        'controller' => 'organisation-search',
                        'action'     => 'hierarchicalsuperiors'
                    ]
                ]
            ],
        ]
    ],
    'controllers' => [
        'factories'  => [
            'Swissbib\Controller\AjaxController' => 'VuFind\Controller\AjaxControllerFactory',
            CoverController::class => 'Swissbib\Controller\Factory::getCoverController',
            FavoritesController::class => AbstractBaseFactory::class,
            FeedbackController::class  => AbstractBaseFactory::class,
            HelpPageController::class => AbstractBaseFactory::class,
            HoldingsController::class => AbstractBaseFactory::class,
            MyResearchController::class => AbstractBaseFactory::class,
            MyResearchNationalLicensesController::class => 'Swissbib\Controller\Factory::getMyResearchNationalLicenceController',
            NationalLicencesController::class => AbstractBaseFactory::class,
            PuraController::class => 'Swissbib\Controller\Factory::getPuraController',
            RecordController::class => 'Swissbib\Controller\Factory::getRecordController',
            SearchController::class => AbstractBaseFactory::class,
            SummonController::class => AbstractBaseFactory::class,

            //TODO : update these keys to ZF3 style keys (with ::class)
            'upgrade'                                => 'Swissbib\Controller\Factory::getNoProductiveSupportController',
            'install'                                => 'Swissbib\Controller\Factory::getNoProductiveSupportController',
            'person-knowledge-card'                  => 'Swissbib\Controller\Factory::getPersonKnowledgeCardController',
            'organisation-knowledge-card'            => 'Swissbib\Controller\Factory::getOrganisationKnowledgeCardController',
            'subject-knowledge-card'                 => 'Swissbib\Controller\Factory::getSubjectKnowledgeCardController',
            'person-detail-page'                     => 'Swissbib\Controller\Factory::getPersonDetailPageController',
            'organisation-detail-page'               => 'Swissbib\Controller\Factory::getOrganisationDetailPageController',
            'subject-detail-page'                    => 'Swissbib\Controller\Factory::getSubjectDetailPageController',
            'person-search'                          => 'Swissbib\Controller\Factory::getPersonSearchController',
            'organisation-search'                    => 'Swissbib\Controller\Factory::getOrganisationSearchController',
            'Swissbib\Controller\ShibtestController' => 'Laminas\ServiceManager\Factory\InvokableFactory',
        ],
        'aliases' => [
            //Overrides
            \VuFind\Controller\CoverController::class => CoverController::class,
            \VuFind\Controller\FeedbackController::class => FeedbackController::class,
            \VuFind\Controller\MyResearchController::class => MyResearchController::class,
            \VuFind\Controller\SearchController::class => SearchController::class,
            \VuFind\Controller\SummonController::class => SummonController::class,
            'ajax'                 => 'Swissbib\Controller\AjaxController',
            'AJAX'                 => 'Swissbib\Controller\AjaxController',
            'shibtest'             => 'Swissbib\Controller\ShibtestController',
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            'tagcloud' => 'Swissbib\Controller\Plugin\Factory::getTagCloud',
            'solrsearch' => 'Swissbib\Controller\Plugin\Factory::getSolrSearch',
            'elasticsearchsearch' => 'Swissbib\Controller\Plugin\Factory::getElasticSearchSearch',
        ],
    ],
    'service_manager' => [
        'factories' => [
            'VuFindTheme\ResourceContainer'                 =>  'Swissbib\VuFind\Factory::getResourceContainer',
            'Swissbib\HoldingsHelper'                       =>  'Swissbib\RecordDriver\Helper\Factory::getHoldingsHelper',
            'Swissbib\TargetsProxy\TargetsProxy'            =>  'Swissbib\TargetsProxy\Factory::getTargetsProxy',
            'Swissbib\TargetsProxy\IpMatcher'               =>  'Swissbib\TargetsProxy\Factory::getIpMatcher',
            'Swissbib\TargetsProxy\UrlMatcher'              =>  'Swissbib\TargetsProxy\Factory::getURLMatcher',

            'Swissbib\Theme\Theme'                          =>  'Swissbib\Services\Factory::getThemeConfigs',
            'Swissbib\Libadmin\Importer'                    =>  'Swissbib\Libadmin\Factory::getLibadminImporter',
            'Swissbib\Tab40Importer'                        =>  'Swissbib\Tab40Import\Factory::getTab40Importer',
            'Swissbib\LocationMap'                          =>  'Swissbib\RecordDriver\Helper\Factory::getLocationMap',
            'Swissbib\EbooksOnDemand'                       =>  'Swissbib\RecordDriver\Helper\Factory::getEbooksOnDemand',
            'Swissbib\Availability'                         =>  'Swissbib\RecordDriver\Helper\Factory::getAvailabiltyHelper',
            'Swissbib\BibCodeHelper'                        =>  'Swissbib\RecordDriver\Helper\Factory::getBibCodeHelper',

            'Swissbib\FavoriteInstitutions\DataSource'      =>  'Swissbib\Favorites\Factory::getFavoritesDataSource',
            'Swissbib\FavoriteInstitutions\Manager'         =>  'Swissbib\Favorites\Factory::getFavoritesManager',
            'Swissbib\ExtendedSolrFactoryHelper'            =>  'Swissbib\VuFind\Search\Helper\Factory::getExtendedSolrFactoryHelper',
            'Swissbib\TypeLabelMappingHelper'               =>  'Swissbib\VuFind\Search\Helper\Factory::getTypeLabelMappingHelper',

            'Swissbib\Highlight\SolrConfigurator'           =>  'Swissbib\Services\Factory::getSOLRHighlightingConfigurator',
            'Swissbib\Logger'                               =>  'Swissbib\Services\Factory::getSwissbibLogger',
            'Swissbib\RecordDriver\SolrDefaultAdapter'      =>  'Swissbib\RecordDriver\Factory::getSolrDefaultAdapter',
            'VuFind\Export'                                 =>  'Swissbib\Services\Factory::getExport',
            //no longer needed but test it more profoundly

            'Swissbib\Hierarchy\SimpleTreeGenerator'        =>  'Swissbib\Hierarchy\Factory::getSimpleTreeGenerator',
            'Swissbib\Hierarchy\MultiTreeGenerator'         =>  'Swissbib\Hierarchy\Factory::getMultiTreeGenerator',

            'VuFind\Search\Options\PluginManager'           => 'Swissbib\Services\Factory::getSearchOptionsPluginManager',
            'VuFind\Search\Params\PluginManager'            => 'Swissbib\Services\Factory::getSearchParamsPluginManager',
            'VuFind\Search\Results\PluginManager'           => 'Swissbib\Services\Factory::getSearchResultsPluginManager',

            'VuFind\Search\SearchTabsHelper'                =>  'Swissbib\View\Helper\Swissbib\Factory::getSearchTabsHelper',
            'Swissbib\Record\Form\CopyForm'                 =>  'Swissbib\Record\Factory::getCopyForm',
            'Swissbib\MyResearch\Form\AddressForm'          =>  'Swissbib\MyResearch\Factory::getAddressForm',
            'Swissbib\Feedback\Form\FeedbackForm'           =>  'Swissbib\Feedback\Factory::getFeedbackForm',
            'Swissbib\NationalLicenceService'               =>  'Swissbib\Services\Factory::getNationalLicenceService',
            'Swissbib\SwitchApiService'                     =>  'Swissbib\Services\Factory::getSwitchApiService',
            'Swissbib\SwitchBackChannelService'             =>  'Swissbib\Services\Factory::getSwitchBackChannelService',
            'Swissbib\EmailService'                         =>  'Swissbib\Services\Factory::getEmailService',
            'Swissbib\PuraService'                          =>  'Swissbib\Services\Factory::getPuraService',
            'VuFind\Search\SearchRunner'                    =>  SearchRunnerFactory::class,
            'Swissbib\Cover\Loader'                         =>  'VuFind\Cover\LoaderFactory',
            //'VuFind\ILS\Connection' => 'VuFind\ILS\Driver\Aleph',
            'Swissbib\XSLT\MARCFormatter'                   => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\Services\ElasticSearchSearch'=> 'Swissbib\Services\Factory::getElasticSearchSearch',
        ],
        'aliases' => [
            'MvcTranslator'         => 'Laminas\Mvc\I18n\Translator',
            'MarcFormatter'         => 'Swissbib\XSLT\MARCFormatter',
            'elasticsearchsearch'   => 'Swissbib\Services\ElasticSearchSearch',
        ],
    ],
    'view_helpers'    => [
        'factories'  => [
            'configAccess'                              =>  'Swissbib\View\Helper\Factory::getConfig',
            'institutionSorter'                         =>  'Swissbib\View\Helper\Factory::getInstitutionSorter',
            'extractFavoriteInstitutionsForHoldings'    =>  'Swissbib\View\Helper\Factory::getFavoriteInstitutionsExtractor',
            'institutionDefinedAsFavorite'              =>  'Swissbib\View\Helper\Factory::getInstitutionsAsDefinedFavorites',
            //'qrCode'                                    =>  'Swissbib\View\Helper\Factory::getQRCodeHelper',
            'isFavoriteInstitution'                     =>  'Swissbib\View\Helper\Factory::isFavoriteInstitutionHelper',
            'domainURL'                                 =>  'Swissbib\View\Helper\Factory::getDomainURLHelper',
            //'redirectProtocolWrapper'                   =>  'Swissbib\View\Helper\Factory::getRedirectProtocolWrapperHelper'
            'Swissbib\View\Helper\Ajax'                     => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\Authors'                          => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\FacetItem'                        => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\FacetItemLabel'                   => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\LastSearchWord'                   => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\LastTabbedSearchUri'              => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\MainTitle'                        => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\MyResearchSideBar'                => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\URLDisplay'                       => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\Number'                           => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\PhysicalDescriptions'             => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\RemoveHighlight'                  => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\SubjectHeadings'                  => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\SortAndPrepareFacetList'          => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\TabTemplate'                      => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Laminas\I18n\View\Helper\Translate'                       => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\GetVersion'                       => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\HoldingActions'                   => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\AvailabilityInfo'                 => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\TranslateLocation'                => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\QrCodeHolding'                    => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\HoldingItemsPaging'               => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\FilterUntranslatedInstitutions'   => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'Swissbib\View\Helper\LayoutClass'                      => 'Laminas\ServiceManager\Factory\InvokableFactory',
        ],
        'aliases' => [
            'ajax'                           => 'Swissbib\View\Helper\Ajax',
            //'MvcTranslator' => 'Laminas\Mvc\I18n\Translator',
            //'translator'    => 'Laminas\Mvc\I18n\Translator',
            'Authors'                        => 'Swissbib\View\Helper\Authors',
            'facetItem'                      => 'Swissbib\View\Helper\FacetItem',
            'facetItemLabel'                 => 'Swissbib\View\Helper\FacetItemLabel',
            'lastSearchWord'                 => 'Swissbib\View\Helper\LastSearchWord',
            'lastTabbedSearchUri'            => 'Swissbib\View\Helper\LastTabbedSearchUri',
            'mainTitle'                      => 'Swissbib\View\Helper\MainTitle',
            'myResearchSideBar'              => 'Swissbib\View\Helper\MyResearchSideBar',
            'urlDisplay'                     => 'Swissbib\View\Helper\URLDisplay',
            'number'                         => 'Swissbib\View\Helper\Number',
            'physicalDescription'            => 'Swissbib\View\Helper\PhysicalDescriptions',
            'removeHighlight'                => 'Swissbib\View\Helper\RemoveHighlight',
            'subjectHeadingFormatter'        => 'Swissbib\View\Helper\SubjectHeadings',
            'SortAndPrepareFacetList'        => 'Swissbib\View\Helper\SortAndPrepareFacetList',
            'tabTemplate'                    => 'Swissbib\View\Helper\TabTemplate',
            'laminasTranslate'                  => 'Laminas\I18n\View\Helper\Translate',
            'getVersion'                     => 'Swissbib\View\Helper\GetVersion',
            'holdingActions'                 => 'Swissbib\View\Helper\HoldingActions',
            'availabilityInfo'               => 'Swissbib\View\Helper\AvailabilityInfo',
            'transLocation'                  => 'Swissbib\View\Helper\TranslateLocation',
            'qrCodeHolding'                  => 'Swissbib\View\Helper\QrCodeHolding',
            'holdingItemsPaging'             => 'Swissbib\View\Helper\HoldingItemsPaging',
            'filterUntranslatedInstitutions' => 'Swissbib\View\Helper\FilterUntranslatedInstitutions',
            'layoutClass'                    => 'Swissbib\View\Helper\LayoutClass',
        ],
    ],
    'vufind' => [
        'recorddriver_tabs' => [
            'VuFind\RecordDriver\Summon'   => [
                'tabs' => [
                    'Description'  => 'articledetails',
                    'TOC'          => null, // Disable TOC tab
                ]
            ],
            'Swissbib\RecordDriver\SolrMarc' => [
                'tabs' => [
                    'Holdings' => 'HoldingsILS',
                    'Description' => 'Description',
                    'TOC' => 'TOC',
                    'UserComments' => 'UserComments',
                    'Reviews' => 'Reviews',
                    'Excerpt' => 'Excerpt',
                    'Preview' => 'preview',
                    'HierarchyTree' => 'HierarchyTree',
                    'HierarchyTreeArchival' => 'HierarchyTreeArchival',
                    'Map' => 'Map',
                    'Similar' => 'SimilarItemsCarousel',
                    'Details' => 'StaffViewMARC',
                ],
                'defaultTab' => null,
            ],
        ],
        // This section contains service manager configurations for all VuFind
        // pluggable components:
        'plugin_managers' => [
            'search_backend' => [
                'factories'  => [
                    'Solr'   => 'Swissbib\VuFind\Search\Factory\SolrDefaultBackendFactory',
                    'Summon' => 'Swissbib\VuFind\Search\Factory\SummonBackendFactory',
                ]
            ],

            'auth' => [
                'factories' => [
                    'VuFind\Auth\Shibboleth' => 'Swissbib\VuFind\Auth\Factory::getShibboleth',
                ],
            ],
            'autocomplete' => [
                'factories' => [
                    'Swissbib\VuFind\Autocomplete\Solr'  =>  'Swissbib\VuFind\Autocomplete\Factory::getSolr',
                    'Swissbib\VuFind\Autocomplete\SolrFacetBasedSuggester' => 'VuFind\Autocomplete\SolrFactory',
                    'Swissbib\VuFind\Autocomplete\SolrCombineFields' => 'VuFind\Autocomplete\SolrFactory',
                ],
                'aliases' => [
                    'solr'             => 'Swissbib\VuFind\Autocomplete\Solr',
                    'SolrFacetBasedSuggester' => 'Swissbib\VuFind\Autocomplete\SolrFacetBasedSuggester',
                    'SolrCombineFields' => 'Swissbib\VuFind\Autocomplete\SolrCombineFields',
                ],
            ],
            'content_covers' => [
                'factories' => [
                    'VuFind\Content\Covers\Amazon' => 'Swissbib\Content\Covers\PluginManager::getAmazon',
                ],
                'aliases' => [
                    'amazon'   => 'VuFind\Content\Covers\Amazon',
                ],
            ],
            'db_table' => [
                'factories' => [
                    'Swissbib\VuFind\Db\Table\NationalLicenceUser'  => 'VuFind\Db\Table\GatewayFactory',
                    'Swissbib\VuFind\Db\Table\PuraUser'             => 'VuFind\Db\Table\GatewayFactory',
                ],
                'aliases' => [
                    'nationallicence'   => 'Swissbib\VuFind\Db\Table\NationalLicenceUser',
                    'pura'              => 'Swissbib\VuFind\Db\Table\PuraUser',
                ],
            ],
            'db_row' => [
                'factories' => [
                    'Swissbib\VuFind\Db\Row\NationalLicenceUser'    => 'VuFind\Db\Row\RowGatewayFactory',
                    'Swissbib\VuFind\Db\Row\PuraUser'               => 'VuFind\Db\Row\RowGatewayFactory',
                ],
                'alias' => [
                    'nationallicence'   => 'Swissbib\VuFind\Db\Row\NationalLicenceUser',
                    'pura'              => 'Swissbib\VuFind\Db\Row\PuraUser',
                ],
            ],
            'recommend' => [
                'factories' => [
                    'favoritefacets' => 'Swissbib\Services\Factory::getFavoriteFacets',
                    //'sidefacets' => 'Swissbib\Recommend\Factory::getSideFacets',
                    'VuFind\Recommend\SideFacets' => 'Swissbib\Recommend\Factory::getSideFacets',
                    'topiprange' => 'Swissbib\Recommend\Factory::getTopIpRange'
                ],
            ],
            'recorddriver' => [
                'factories' => [
                    //'solrmarc' => 'Swissbib\RecordDriver\Factory::getSolrMarcRecordDriver',
                    //'summon'   => 'Swissbib\RecordDriver\Factory::getSummonRecordDriver',
                    //'worldcat' => 'Swissbib\RecordDriver\Factory::getWorldCatRecordDriver',
                    //'missing'  => 'Swissbib\RecordDriver\Factory::getRecordDriverMissing',
                    'VuFind\RecordDriver\SolrMarc' => 'Swissbib\RecordDriver\Factory::getSolrMarcRecordDriver',
                    'VuFind\RecordDriver\WorldCat' => 'Swissbib\RecordDriver\Factory::getWorldCatRecordDriver',
                    'VuFind\RecordDriver\Missing'  => 'Swissbib\RecordDriver\Factory::getRecordDriverMissing',
                ],
                'aliases' => [
                    //Overrides
                    \VuFind\RecordDriver\Summon::class => Summon::class,

                    //aliases
                    'solrmarc' => 'VuFind\RecordDriver\SolrMarc',
                    'worldcat' => 'VuFind\RecordDriver\WorldCat',
                    'missing' => 'VuFind\RecordDriver\Missing',
                ],
            ],
            'ils_driver' => [
                'factories' => [
                    'Swissbib\VuFind\ILS\Driver\Aleph' => 'Swissbib\VuFind\ILS\Driver\Factory::getAlephDriver',
                    'Swissbib\VuFind\ILS\Driver\MultiBackend' => 'Swissbib\VuFind\ILS\Driver\Factory::getMultiBackend',
                ],
                'aliases' => [
                    'aleph'                 => 'Swissbib\VuFind\ILS\Driver\Aleph',
                    'multibackend'          => 'Swissbib\VuFind\ILS\Driver\MultiBackend',
                ]
            ],
            'hierarchy_driver' => [
                'factories' => [
                    'Swissbib\VuFind\Hierarchy\HierarchySeries'     => 'VuFind\Hierarchy\Driver\ConfigurationBasedFactory',
                    'Swissbib\VuFind\Hierarchy\HierarchyArchival'   => 'VuFind\Hierarchy\Driver\ConfigurationBasedFactory',
                ],
                'aliases' => [
                    'series'    => 'Swissbib\VuFind\Hierarchy\HierarchySeries',
                    'archival'  => 'Swissbib\VuFind\Hierarchy\HierarchyArchival',
                ],
            ],
            'hierarchy_treedataformatter' => [
                'factories' => [
                    'Swissbib\VuFind\Hierarchy\TreeDataFormatter\Json' => 'Laminas\ServiceManager\Factory\InvokableFactory',
                ],
                'aliases' => [
                    'json' => 'Swissbib\VuFind\Hierarchy\TreeDataFormatter\Json',
                ],
            ],
            'hierarchy_treerenderer' => [
                'factories' => [
                    \Swissbib\VuFind\Hierarchy\TreeRenderer\JSTree::class => 'Swissbib\VuFind\Hierarchy\Factory::getJSTree'
                ],
                'aliases' => [
                    'jstree' => \Swissbib\VuFind\Hierarchy\TreeRenderer\JSTree::class
                ],
            ],
            'recordtab' => [
                'factories' => [
                    'Swissbib\RecordTab\HierarchyTree' => 'Swissbib\RecordTab\Factory::getHierarchyTree',
                    'Swissbib\RecordTab\HierarchyTreeArchival' => 'Swissbib\RecordTab\Factory::getHierarchyTreeArchival',
                    'Swissbib\RecordTab\ArticleDetails' => 'Laminas\ServiceManager\Factory\InvokableFactory',
                    'Swissbib\RecordTab\Description'    => 'Laminas\ServiceManager\Factory\InvokableFactory',
                ],
                'aliases' => [
                    'VuFind\RecordTab\HierarchyTree' => 'Swissbib\RecordTab\HierarchyTree',
                    'hierarchytreearchival' => 'Swissbib\RecordTab\HierarchyTreeArchival',
                    'articledetails' => 'Swissbib\RecordTab\ArticleDetails',
                    'description'    => 'Swissbib\RecordTab\Description'
                ],
            ],
            'ajaxhandler'           => [
                'factories' => [
                    'Swissbib\AjaxHandler\GetSubjects'                => 'Swissbib\AjaxHandler\AbstractAjaxFactory',
                    'Swissbib\AjaxHandler\GetAuthors'                 => 'Swissbib\AjaxHandler\AbstractAjaxFactory',
                    'Swissbib\AjaxHandler\GetBibliographicResource'   => 'Swissbib\AjaxHandler\AbstractAjaxFactory',
                    'Swissbib\AjaxHandler\GetCoAuthors'               => 'Swissbib\AjaxHandler\AbstractAjaxFactory',
                    'Swissbib\AjaxHandler\GetSameGenreAuthors'        => 'Swissbib\AjaxHandler\AbstractAjaxFactory',
                    'Swissbib\AjaxHandler\GetSameMovementAuthors'     => 'Swissbib\AjaxHandler\AbstractAjaxFactory',
                    'Swissbib\AjaxHandler\GetSubjectAuthors'          => 'Swissbib\AjaxHandler\AbstractAjaxFactory',
                    'Swissbib\AjaxHandler\GetOrganisations'           => 'Swissbib\AjaxHandler\AbstractAjaxFactory',
                    \Swissbib\AjaxHandler\GetACSuggestions::class     => \VuFind\AjaxHandler\GetACSuggestionsFactory::class,
                    'Swissbib\AjaxHandler\GetSameHierarchicalSuperiorOrganisations' => 'Swissbib\AjaxHandler\AbstractAjaxFactory',
                ],
                'aliases' =>  [
                    'getSubjects'                 => 'Swissbib\AjaxHandler\GetSubjects',
                    'getAuthors'                  => 'Swissbib\AjaxHandler\GetAuthors',
                    'getBibliographicResource'    => 'Swissbib\AjaxHandler\GetBibliographicResource',
                    'getCoAuthors'                => 'Swissbib\AjaxHandler\GetCoAuthors',
                    'getSameGenreAuthors'         => 'Swissbib\AjaxHandler\GetSameGenreAuthors',
                    'getSameMovementAuthors'      => 'Swissbib\AjaxHandler\GetSameMovementAuthors',
                    'getSubjectAuthors'           => 'Swissbib\AjaxHandler\GetSubjectAuthors',
                    'getOrganisations'            => 'Swissbib\AjaxHandler\GetOrganisations',
                    'getSameHierarchicalSuperiorOrganisations' => 'Swissbib\AjaxHandler\GetSameHierarchicalSuperiorOrganisations',
                    'getACSuggestions'            => \Swissbib\AjaxHandler\GetACSuggestions::class,
                ]
            ],
            'command' => [
                'factories' => [
                    SendNationalLicenceUserExport::class => SendNationalLicenceUserExportFactory::class,
                    UpdateNationalLicenceUserInfo::class => UpdateNationalLicenceUserInfoFactory::class,
                    SendPuraReport::class => SendPuraReportFactory::class,
                    UpdatePuraUser::class => UpdatePuraUserFactory::class,
                    LibAdminSync::class => LibAdminSyncFactory::class,
                    LibAdminSyncGeoJson::class => LibAdminSyncGeoJsonFactory::class,
                    LibAdminSyncMapPortal::class => LibAdminSyncMapPortalFactory::class,
                    HierarchyCache::class => HierarchyCacheFactory::class,
                    Tab40Import::class => Tab40ImportFactory::class,
                ]
            ],
        ]
    ],
    'swissbib' => [
        // The ignore patterns have to be valid regex!
        'ignore_css_assets' => [
            //can be used to ignore assets like this:
            //'|blueprint/screen.css|',
        ],
        'ignore_js_assets'  => [
            //can be used to ignore assets like this:
            //'|jquery\.min.js|', // jquery 1.6
            //'|^jquery\.form\.js|',
        ],
        'asset_manager' => [
            'resolver_configs' => [
                'paths' => [
                    'Swissbib'
                ]
            ]
        ],
        // This section contains service manager configurations for all Swissbib
        // pluggable components:
        'plugin_managers' => [
            'vufind_search_options' => [
                'abstract_factories' => ['Swissbib\VuFind\Search\Options\PluginFactory'],
                'aliases' => [
                    'SolrClassification' => 'Swissbib\VuFind\Search\SolrClassification\Options',
                ],
                'factories' => [
                    'elasticsearch' => '\ElasticSearch\VuFind\Search\Options\Factory::getElasticSearch'
                ],
            ], 'vufind_search_params' => [
                'abstract_factories' => ['Swissbib\VuFind\Search\Params\PluginFactory'],
                'aliases' => [
                    'solr'          => 'Swissbib\VuFind\Search\Solr\Params',
                    'SolrClassification' => 'Swissbib\VuFind\Search\SolrClassification\Params',
                    'elasticsearch' => 'ElasticSearch\VuFind\Search\Params\ElasticSearch',
                    'summon' => 'Swissbib\VuFind\Search\Summon\Params',
                ],
                'factories' => [
                    'Swissbib\VuFind\Search\Solr\Params'          => 'Swissbib\VuFind\Search\Params\Factory::getSolr',
                    'Swissbib\VuFind\Search\SolrClassification\Params' => 'Swissbib\VuFind\Search\Params\Factory::getSolrClassification',
                    'Swissbib\VuFind\Search\Summon\Params' => 'Swissbib\VuFind\Search\Params\Factory::getSummon',
                    'ElasticSearch\VuFind\Search\Params\ElasticSearch' => '\ElasticSearch\VuFind\Search\Params\Factory::getElasticSearch',
                ],

            ],
            'vufind_search_results' => [
                'abstract_factories' => ['Swissbib\VuFind\Search\Results\PluginFactory'],
                'aliases' => [
                    'solr'              => 'Swissbib\VuFind\Search\Solr\Results\Solr',
                    'solrauthorfacets'  => 'Swissbib\VuFind\Search\Solr\Results\SolrAuthorFacets',
                    'mixedlist'         => 'Swissbib\VuFind\Search\Solr\Results\MixedList',
                    'favorites'         => 'Swissbib\VuFind\Search\Solr\Results\Favorites',
                    'elasticsearch'     => 'ElasticSearch\VuFind\Search\Results\ElasticSearch',
                    'summon'            => 'Swissbib\VuFind\Search\Summon\Results',
                    'SolrClassification' => 'Swissbib\VuFind\Search\SolrClassification\Results',
                ],
                'factories' => [
                    'Swissbib\VuFind\Search\SolrClassification\Results' => 'Swissbib\VuFind\Search\Results\Factory::getSolrClassification',
                    'Swissbib\VuFind\Search\Solr\Results\Solr'              => 'Swissbib\VuFind\Search\Results\Factory::getSolr',
                    'Swissbib\VuFind\Search\Solr\Results\SolrAuthorFacets'  => 'Swissbib\VuFind\Search\Results\Factory::getSolrAuthorFacets',
                    'Swissbib\VuFind\Search\Solr\Results\MixedList'         => 'Swissbib\VuFind\Search\Results\Factory::getMixdList',
                    'Swissbib\VuFind\Search\Solr\Results\Favorites'         => 'Swissbib\VuFind\Search\Results\Factory::getFavorites',
                    'ElasticSearch\VuFind\Search\Results\ElasticSearch'     => '\ElasticSearch\VuFind\Search\Results\Factory::getElasticSearch',
                    'Swissbib\VuFind\Search\Summon\Results'                 => 'Swissbib\VuFind\Search\Results\Factory::getSummon',
                ],
            ]
        ]
    ]
];

//legacy column in resource table, for lists with removed records
$recordRoutes = [
    'vufindrecord' => RecordController::class,
];

$routeGenerator = new RouteGenerator();
$routeGenerator->addRecordRoutes($config, $recordRoutes);

return $config;
