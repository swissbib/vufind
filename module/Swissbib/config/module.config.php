<?php
namespace Swissbib\Module\Config;

use Swissbib\Controller\HelpPageController;
use Swissbib\Controller\LibadminSyncController;
use Swissbib\Controller\MyResearchController;
use Swissbib\Controller\NationalLicencesController;
use Swissbib\Controller\Tab40ImportController;
use Swissbib\VuFind\Search\SearchRunnerFactory;
use VuFind\Controller\AbstractBaseFactory;
use VuFind\Controller\AjaxController;

return [
    'router' => [
        'routes' => [
            // ILS location, e.g. baselbern
            'accountWithLocation' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/MyResearch/:action/:location',
                    'defaults'    => [
                        'controller' => 'my-research',
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
            // (local) Search User Settings
            'myresearch-settings' => [
                'type'    => 'literal',
                'options' => [
                    'route'    => '/MyResearch/Settings',
                    'defaults' => [
                        'controller' => 'my-research',
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
                        'controller' => 'national-licences',
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
                        'controller' => 'national-licenses-signpost',
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
                        'controller' => 'pura',
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
                                'controller' => 'pura',
                                'action' => 'library',
                            ],
                            'constraints' => [
                                'libraryCode' => 'Z01|RE01001|E02|A100',
                                'page' => 'registration|listResources'
                            ],
                        ],
                    ],
                    'barcode' => [
                        'type'    => 'segment',
                        'options' => [
                            'route'       => '/barcode/:token[/:size]',
                            'defaults'    => [
                                'controller' => 'pura',
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
                        'controller' => 'helppage',
                        'action'     => 'index'
                    ]
                ]
            ],
            'holdings-ajax' => [ // load holdings details for record with ajax
                'type'    => 'segment',
                'options' => [
                    'route'    => '/Holdings/:record/:institution',
                    'defaults' => [
                        'controller' => 'holdings',
                        'action'     => 'list'
                    ]
                ]
            ],
            'holdings-holding-items' => [ // load holding holdings details for record with ajax
                'type'    => 'segment',
                'options' => [
                    'route'    => '/Holdings/:record/:institution/items/:resource',
                    'defaults' => [
                        'controller' => 'holdings',
                        'action'     => 'holdingItems'
                    ]
                ]
            ],
            'myresearch-favorite-institutions' => [ // display defined favorite institutions
                'type'    => 'segment',
                'options' => [
                    'route'    => '/MyResearch/Favorites[/:action]',
                    'defaults' => [
                        'controller' => 'institutionFavorites',
                        'action'     => 'display'
                    ]
                ]
            ],
            'myresearch-favorites' => [ // Override vufind favorites route. Rename to Lists
                'type'    => 'literal',
                'options' => [
                    'route'    => '/MyResearch/Lists',
                    'defaults' => [
                        'controller' => 'my-research',
                        'action'     => 'favorites'
                    ]
                ]
            ],
            'myresearch-photocopies' => [ // Override vufind favorites route. Rename to Lists
                'type'    => 'literal',
                'options' => [
                    'route'    => '/MyResearch/Photocopies',
                    'defaults' => [
                        'controller' => 'my-research',
                        'action'     => 'photocopies'
                    ]
                ]
            ],
            'myresearch-bookings' => [ // Override vufind favorites route. Rename to Lists
                'type' => 'literal', 'options' => [
                    'route' => '/MyResearch/Bookings', 'defaults' => [
                        'controller' => 'my-research', 'action' => 'bookings'
                    ]
                ]
            ], 'myresearch-changeaddress' => [
                'type' => 'literal', 'options' => [
                    'route' => '/MyResearch/Address', 'defaults' => [
                        'controller' => 'my-research', 'action' => 'changeAddress'
                    ]
                ]
            ], 'record-copy' => [
                'type' => 'segment', 'options' => [
                    'route' => '/Record/:id/Copy', 'defaults' => [
                        'controller' => 'record', 'action' => 'copy'
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
        ]
    ],
    'console' => [
        'router' => [
            'router_class'  => '',
            'routes' => [
                'libadmin-sync' => [
                    'options' => [
                        'route'    => 'libadmin sync [--verbose|-v] [--dry|-d] [--result|-r]',
                        'defaults' => [
                            'controller' => LibadminSyncController::class,
                            'action'     => 'sync'
                        ]
                    ]
                ],
                'libadmin-sync-mapportal' => [
                    'options' => [
                        'route'    => 'libadmin syncMapPortal [--verbose|-v] [--result|-r] [<path>] ',
                        'defaults' => [
                            'controller' => LibadminSyncController::class,
                            'action'     => 'syncMapPortal'
                        ]
                    ]
                ],
                'tab40-import' => [ // Importer for aleph tab40 files
                    'options' => [
                        'route'    => 'tab40import <network> <locale> <source>',
                        'defaults' => [
                            'controller' => 'tab40import',
                            'action'     => 'import'
                        ]
                    ]
                ],
                'hierarchy' => [
                    'options' => [
                        'route'    => 'hierarchy [<limit>] [--verbose|-v]',
                        'defaults' => [
                            'controller' => 'hierarchycache',
                            'action'     => 'buildCache'
                        ]
                    ]
                ],
                'send-national-licence-users-export' => [
                    'options' => [
                        'route'    => 'send-national-licence-users-export',
                        'defaults' => [
                            'controller' => 'console',
                            'action'     => 'sendNationalLicenceUsersExport'
                        ]
                    ]
                ],
                'update-national-licence-user-info' => [
                    'options' => [
                        'route'    => 'update-national-licence-user-info',
                        'defaults' => [
                            'controller' => 'console',
                            'action'     => 'updateNationalLicenceUserInfo'
                        ]
                    ]
                ],
                'update-pura-user' => [
                    'options' => [
                        'route'    => 'update-pura-user',
                        'defaults' => [
                            'controller' => 'console',
                            'action'     => 'updatePuraUser'
                        ]
                    ]
                ],
                'send-pura-report' => [
                    'options' => [
                        'route'    => 'send-pura-report',
                        'defaults' => [
                            'controller' => 'console',
                            'action'     => 'sendPuraReport'
                        ]
                    ]
                ],
            ]
        ]
    ],
    'controllers' => [
        'invokables' => [
            'shibtest'             => 'Swissbib\Controller\ShibtestController',
        ],
        'factories'  => [
            AjaxController::class => 'Swissbib\Controller\Factory::getAjaxController',
            'Swissbib\Controller\SearchController' => 'VuFind\Controller\AbstractBaseFactory',
            'record' => 'Swissbib\Controller\Factory::getRecordController',
            NationalLicencesController::class => AbstractBaseFactory::class,
            'pura' => 'Swissbib\Controller\Factory::getPuraController',
            'national-licenses-signpost' => 'Swissbib\Controller\Factory::getMyResearchNationalLicenceController',
            'summon' => 'Swissbib\Controller\Factory::getSummonController',
            'Swissbib\Controller\HoldingsController' => 'VuFind\Controller\AbstractBaseFactory',
            'feedback'  => 'Swissbib\Controller\Factory::getFeedbackController',
            'cover'     => 'Swissbib\Controller\Factory::getCoverController',
            'upgrade'   => 'Swissbib\Controller\Factory::getNoProductiveSupportController',
            'install'   => 'Swissbib\Controller\Factory::getNoProductiveSupportController',
            'tab40import'   => 'Swissbib\Controller\Factory::getTab40ImportController',
            Tab40ImportController::class   => 'Swissbib\Controller\Factory::getTab40ImportController',
            'Swissbib\Controller\FavoritesController' => 'VuFind\Controller\AbstractBaseFactory',
            'hierarchycache'       => 'Swissbib\Controller\Factory::getHierarchyCacheController',
            HelpPageController::class => AbstractBaseFactory::class,
            LibadminSyncController::class => 'Swissbib\Controller\Factory::getLibadminSyncController',
            MyResearchController::class => AbstractBaseFactory::class,
            MyResearchController::class => AbstractBaseFactory::class,
            'console' => 'Swissbib\Controller\Factory::getConsoleController',
            'person-knowledge-card' => 'Swissbib\Controller\Factory::getPersonKnowledgeCardController',
            'subject-knowledge-card' => 'Swissbib\Controller\Factory::getSubjectKnowledgeCardController',
            'person-detail-page' => 'Swissbib\Controller\Factory::getPersonDetailPageController',
            'subject-detail-page' => 'Swissbib\Controller\Factory::getSubjectDetailPageController',
            'person-search' => 'Swissbib\Controller\Factory::getPersonSearchController',
        ],
        'aliases' => [
            'institutionFavorites' => 'Swissbib\Controller\FavoritesController',
            'helppage' => 'Swissbib\Controller\HelpPageController',
            'my-research' => 'Swissbib\Controller\MyResearchController',
            'national-licences' => 'Swissbib\Controller\NationalLicencesController',
            'Search' => 'Swissbib\Controller\SearchController',
            'search' => 'Swissbib\Controller\SearchController',
            'holdings'  => 'Swissbib\Controller\HoldingsController',
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            'tagcloud' => 'Swissbib\Controller\Plugin\Factory::getTagCloud',
            'solrsearch' => 'Swissbib\Controller\Plugin\Factory::getSolrSearch',
            'elasticsearchsearch' => 'Swissbib\Controller\Plugin\Factory::getElasticSearchSearch',
        ],
    ], 'service_manager' => [
        'invokables' => [
            'MarcFormatter'                                 => 'Swissbib\XSLT\MARCFormatter',
        ],
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
            'Swissbib\FavoriteInstitutions\Manager'         =>   'Swissbib\Favorites\Factory::getFavoritesManager',
            'Swissbib\ExtendedSolrFactoryHelper'            =>  'Swissbib\VuFind\Search\Helper\Factory::getExtendedSolrFactoryHelper',
            'Swissbib\TypeLabelMappingHelper'               =>  'Swissbib\VuFind\Search\Helper\Factory::getTypeLabelMappingHelper',

            'Swissbib\Highlight\SolrConfigurator'           =>  'Swissbib\Services\Factory::getSOLRHighlightingConfigurator',
            'Swissbib\Logger'                               =>  'Swissbib\Services\Factory::getSwissbibLogger',
            'Swissbib\RecordDriver\SolrDefaultAdapter'      =>  'Swissbib\RecordDriver\Factory::getSolrDefaultAdapter',
            'VuFind\Export'                                 =>  'Swissbib\Services\Factory::getExport',
            //no longer needed but test it more profoundly
            'sbSpellingProcessor'                           =>  'Swissbib\VuFind\Search\Solr\Factory::getSpellchecker',
            'sbSpellingResults'                             =>  'Swissbib\VuFind\Search\Solr\Factory::getSpellingResults',

            'Swissbib\Hierarchy\SimpleTreeGenerator'        =>  'Swissbib\Hierarchy\Factory::getSimpleTreeGenerator',
            'Swissbib\Hierarchy\MultiTreeGenerator'         =>  'Swissbib\Hierarchy\Factory::getMultiTreeGenerator',

            'VuFind\Search\Options\PluginManager'           => 'Swissbib\Services\Factory::getSearchOptionsPluginManager',
            'VuFind\Search\Params\PluginManager'            => 'Swissbib\Services\Factory::getSearchParamsPluginManager',
            'VuFind\Search\Results\PluginManager'           => 'Swissbib\Services\Factory::getSearchResultsPluginManager',

            'Swissbib\Search\SearchTabsHelper'              =>  'Swissbib\View\Helper\Swissbib\Factory::getSearchTabsHelper',
            'VuFind\Search\SearchTabsHelper'                =>  'Swissbib\View\Helper\Swissbib\Factory::getSearchTabsHelper',
            'Swissbib\Record\Form\CopyForm'                 =>  'Swissbib\Record\Factory::getCopyForm',
            'Swissbib\MyResearch\Form\AddressForm'          =>  'Swissbib\MyResearch\Factory::getAddressForm',
            'Swissbib\Feedback\Form\FeedbackForm'           =>  'Swissbib\Feedback\Factory::getFeedbackForm',
            'Swissbib\NationalLicenceService'               =>  'Swissbib\Services\Factory::getNationalLicenceService',
            'Swissbib\SwitchApiService'                     =>  'Swissbib\Services\Factory::getSwitchApiService',
            'Swissbib\SwitchBackChannelService'             =>  'Swissbib\Services\Factory::getSwitchBackChannelService',
            'Swissbib\EmailService'                         =>  'Swissbib\Services\Factory::getEmailService',
            'Swissbib\PuraService'                          =>  'Swissbib\Services\Factory::getPuraService',
            'VuFind\Search\SearchRunner'                    =>  SearchRunnerFactory::class
        ],
        'aliases' => [
            'MvcTranslator'           => 'Zend\Mvc\I18n\Translator',
        ],
    ],
    'view_helpers'    => [
        'invokables' => [
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
            'zendTranslate'                  => 'Zend\I18n\View\Helper\Translate',
            'getVersion'                     => 'Swissbib\View\Helper\GetVersion',
            'holdingActions'                 => 'Swissbib\View\Helper\HoldingActions',
            'availabilityInfo'               => 'Swissbib\View\Helper\AvailabilityInfo',
            'transLocation'                  => 'Swissbib\View\Helper\TranslateLocation',
            'qrCodeHolding'                  => 'Swissbib\View\Helper\QrCodeHolding',
            'holdingItemsPaging'             => 'Swissbib\View\Helper\HoldingItemsPaging',
            'filterUntranslatedInstitutions' => 'Swissbib\View\Helper\FilterUntranslatedInstitutions',
            'layoutClass'                    => 'Swissbib\View\Helper\LayoutClass',
            //todo: nicht mehr benoetigt??
            //'ajax'                           => 'Swissbib\View\Helper\Ajax'
        ],
        'factories'  => [
            'configAccess'                              =>  'Swissbib\View\Helper\Factory::getConfig',
            'institutionSorter'                         =>  'Swissbib\View\Helper\Factory::getInstitutionSorter',
            'extractFavoriteInstitutionsForHoldings'    =>  'Swissbib\View\Helper\Factory::getFavoriteInstitutionsExtractor',
            'institutionDefinedAsFavorite'              =>  'Swissbib\View\Helper\Factory::getInstitutionsAsDefinedFavorites',
            //'qrCode'                                    =>  'Swissbib\View\Helper\Factory::getQRCodeHelper',
            'isFavoriteInstitution'                     =>  'Swissbib\View\Helper\Factory::isFavoriteInstitutionHelper',
            'domainURL'                                 =>  'Swissbib\View\Helper\Factory::getDomainURLHelper',
            //'redirectProtocolWrapper'                   =>  'Swissbib\View\Helper\Factory::getRedirectProtocolWrapperHelper'
        ],
        'aliases' => [
            //'MvcTranslator' => 'Zend\Mvc\I18n\Translator',
            //'translator'    => 'Zend\Mvc\I18n\Translator',
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
                    'shibboleth' => 'Swissbib\VuFind\Auth\Factory::getShibboleth',
                ],
            ],
            'autocomplete' => [
                'factories' => [
                    'solr'          =>  'Swissbib\VuFind\Autocomplete\Factory::getSolr',
                ],
            ],
            'content_covers' => [
                'factories' => [
                    'amazon' => 'Swissbib\Content\Covers\Factory::getAmazon',
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
                    'VuFind\RecordDriver\Summon'   => 'Swissbib\RecordDriver\Factory::getSummonRecordDriver',
                    'VuFind\RecordDriver\WorldCat' => 'Swissbib\RecordDriver\Factory::getWorldCatRecordDriver',
                    'VuFind\RecordDriver\Missing'  => 'Swissbib\RecordDriver\Factory::getRecordDriverMissing',
                ],
                'aliases' => [
                    'solrmarc' => 'VuFind\RecordDriver\SolrMarc',
                    'summon' => 'VuFind\RecordDriver\Summon',
                    'worldcat' => 'VuFind\RecordDriver\WorldCat',
                    'missing' => 'VuFind\RecordDriver\Missing',
                ],
            ],
            'ils_driver' => [
                'factories' => [
                    'aleph' => 'Swissbib\VuFind\ILS\Driver\Factory::getAlephDriver',
                    'multibackend' => 'Swissbib\VuFind\ILS\Driver\Factory::getMultiBackend',
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
                'invokables' => [
                    'json' => 'Swissbib\VuFind\Hierarchy\TreeDataFormatter\Json',
                ],
            ],
            'hierarchy_treerenderer'   => [
                'factories' => [
                    'jstree' => 'Swissbib\VuFind\Hierarchy\Factory::getJSTree'
                ]
            ],
            'recordtab'                => [
                'invokables' => [
                    'articledetails' => 'Swissbib\RecordTab\ArticleDetails',
                    'description'    => 'Swissbib\RecordTab\Description'
                ],
                'factories' => [
                    'hierarchytree' => 'Swissbib\RecordTab\Factory::getHierarchyTree',
                    'hierarchytreearchival' => 'Swissbib\RecordTab\Factory::getHierarchyTreeArchival'
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
                'factories' => [
                    'elasticsearch' => '\ElasticSearch\VuFind\Search\Options\Factory::getElasticSearch'
                ],
            ], 'vufind_search_params' => [
                'abstract_factories' => ['Swissbib\VuFind\Search\Params\PluginFactory'],
                'aliases' => [
                    'solr'          => 'Swissbib\VuFind\Search\Solr\Params\Solr',
                    'elasticsearch' => 'ElasticSearch\VuFind\Search\Params\ElasticSearch'
                ],
                'factories' => [
                    'Swissbib\VuFind\Search\Solr\Params\Solr'          => 'Swissbib\VuFind\Search\Params\Factory::getSolr',
                    'ElasticSearch\VuFind\Search\Params\ElasticSearch' => '\ElasticSearch\VuFind\Search\Params\Factory::getElasticSearch'
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
                ],
                'factories' => [
                    'Swissbib\VuFind\Search\Solr\Results\Solr'              => 'Swissbib\VuFind\Search\Results\Factory::getSolr',
                    'Swissbib\VuFind\Search\Solr\Results\SolrAuthorFacets'  => 'Swissbib\VuFind\Search\Results\Factory::getSolrAuthorFacets',
                    'Swissbib\VuFind\Search\Solr\Results\MixedList'         => 'Swissbib\VuFind\Search\Results\Factory::getMixdList',
                    'Swissbib\VuFind\Search\Solr\Results\Favorites'         => 'Swissbib\VuFind\Search\Results\Factory::getFavorites',
                    'ElasticSearch\VuFind\Search\Results\ElasticSearch'     => '\ElasticSearch\VuFind\Search\Results\Factory::getElasticSearch',
                ],
            ]
        ]
    ]
];
