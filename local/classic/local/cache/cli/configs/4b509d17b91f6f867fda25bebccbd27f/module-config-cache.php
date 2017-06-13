<?php
return array (
  'service_manager' => 
  array (
    'invokables' => 
    array (
      'ZfcRbac\\Collector\\RbacCollector' => 'ZfcRbac\\Collector\\RbacCollector',
      'VuFind\\HierarchicalFacetHelper' => 'VuFind\\Search\\Solr\\HierarchicalFacetHelper',
      'VuFind\\IpAddressUtils' => 'VuFind\\Net\\IpAddressUtils',
      'VuFind\\Search' => 'VuFindSearch\\Service',
      'VuFind\\Session\\Settings' => 'VuFind\\Session\\Settings',
      'VuFindTheme\\ResourceContainer' => 'Swissbib\\VuFind\\ResourceContainer',
      'Swissbib\\QRCode' => 'Swissbib\\CRCode\\QrCodeService',
      'MarcFormatter' => 'Swissbib\\XSLT\\MARCFormatter',
    ),
    'factories' => 
    array (
      'ZfcRbac\\Guards' => 'ZfcRbac\\Factory\\GuardsFactory',
      'Rbac\\Rbac' => 'ZfcRbac\\Factory\\RbacFactory',
      'ZfcRbac\\Assertion\\AssertionPluginManager' => 'ZfcRbac\\Factory\\AssertionPluginManagerFactory',
      'ZfcRbac\\Guard\\GuardPluginManager' => 'ZfcRbac\\Factory\\GuardPluginManagerFactory',
      'ZfcRbac\\Identity\\AuthenticationIdentityProvider' => 'ZfcRbac\\Factory\\AuthenticationIdentityProviderFactory',
      'ZfcRbac\\Options\\ModuleOptions' => 'ZfcRbac\\Factory\\ModuleOptionsFactory',
      'ZfcRbac\\Role\\RoleProviderPluginManager' => 'ZfcRbac\\Factory\\RoleProviderPluginManagerFactory',
      'ZfcRbac\\Service\\AuthorizationService' => 'ZfcRbac\\Factory\\AuthorizationServiceFactory',
      'ZfcRbac\\Service\\RoleService' => 'ZfcRbac\\Factory\\RoleServiceFactory',
      'ZfcRbac\\View\\Strategy\\RedirectStrategy' => 'ZfcRbac\\Factory\\RedirectStrategyFactory',
      'ZfcRbac\\View\\Strategy\\UnauthorizedStrategy' => 'ZfcRbac\\Factory\\UnauthorizedStrategyFactory',
      'VuFind\\AccountCapabilities' => 'VuFind\\Service\\Factory::getAccountCapabilities',
      'VuFind\\AuthManager' => 'VuFind\\Auth\\Factory::getManager',
      'VuFind\\AuthPluginManager' => 'VuFind\\Service\\Factory::getAuthPluginManager',
      'VuFind\\AutocompletePluginManager' => 'VuFind\\Service\\Factory::getAutocompletePluginManager',
      'VuFind\\CacheManager' => 'VuFind\\Service\\Factory::getCacheManager',
      'VuFind\\Cart' => 'VuFind\\Service\\Factory::getCart',
      'VuFind\\Config' => 'VuFind\\Service\\Factory::getConfig',
      'VuFind\\ContentPluginManager' => 'VuFind\\Service\\Factory::getContentPluginManager',
      'VuFind\\ContentAuthorNotesPluginManager' => 'VuFind\\Service\\Factory::getContentAuthorNotesPluginManager',
      'VuFind\\ContentCoversPluginManager' => 'VuFind\\Service\\Factory::getContentCoversPluginManager',
      'VuFind\\ContentExcerptsPluginManager' => 'VuFind\\Service\\Factory::getContentExcerptsPluginManager',
      'VuFind\\ContentReviewsPluginManager' => 'VuFind\\Service\\Factory::getContentReviewsPluginManager',
      'VuFind\\CookieManager' => 'VuFind\\Service\\Factory::getCookieManager',
      'VuFind\\Cover\\Router' => 'VuFind\\Service\\Factory::getCoverRouter',
      'VuFind\\DateConverter' => 'VuFind\\Service\\Factory::getDateConverter',
      'VuFind\\DbAdapter' => 'VuFind\\Service\\Factory::getDbAdapter',
      'VuFind\\DbAdapterFactory' => 'VuFind\\Service\\Factory::getDbAdapterFactory',
      'VuFind\\DbTablePluginManager' => 'VuFind\\Service\\Factory::getDbTablePluginManager',
      'VuFind\\Export' => 'Swissbib\\Services\\Factory::getExport',
      'VuFind\\HierarchyDriverPluginManager' => 'VuFind\\Service\\Factory::getHierarchyDriverPluginManager',
      'VuFind\\HierarchyTreeDataFormatterPluginManager' => 'VuFind\\Service\\Factory::getHierarchyTreeDataFormatterPluginManager',
      'VuFind\\HierarchyTreeDataSourcePluginManager' => 'VuFind\\Service\\Factory::getHierarchyTreeDataSourcePluginManager',
      'VuFind\\HierarchyTreeRendererPluginManager' => 'VuFind\\Service\\Factory::getHierarchyTreeRendererPluginManager',
      'VuFind\\Http' => 'VuFind\\Service\\Factory::getHttp',
      'VuFind\\HMAC' => 'VuFind\\Service\\Factory::getHMAC',
      'VuFind\\ILSAuthenticator' => 'VuFind\\Auth\\Factory::getILSAuthenticator',
      'VuFind\\ILSConnection' => 'VuFind\\Service\\Factory::getILSConnection',
      'VuFind\\ILSDriverPluginManager' => 'VuFind\\Service\\Factory::getILSDriverPluginManager',
      'VuFind\\ILSHoldLogic' => 'VuFind\\Service\\Factory::getILSHoldLogic',
      'VuFind\\ILSHoldSettings' => 'VuFind\\Service\\Factory::getILSHoldSettings',
      'VuFind\\ILSTitleHoldLogic' => 'VuFind\\Service\\Factory::getILSTitleHoldLogic',
      'VuFind\\Logger' => 'VuFind\\Service\\Factory::getLogger',
      'VuFind\\Mailer' => 'VuFind\\Mailer\\Factory',
      'VuFind\\ProxyConfig' => 'VuFind\\Service\\Factory::getProxyConfig',
      'VuFind\\Recaptcha' => 'VuFind\\Service\\Factory::getRecaptcha',
      'VuFind\\RecommendPluginManager' => 'VuFind\\Service\\Factory::getRecommendPluginManager',
      'VuFind\\RecordCache' => 'VuFind\\Service\\Factory::getRecordCache',
      'VuFind\\RecordDriverPluginManager' => 'VuFind\\Service\\Factory::getRecordDriverPluginManager',
      'VuFind\\RecordLoader' => 'VuFind\\Service\\Factory::getRecordLoader',
      'VuFind\\RecordRouter' => 'VuFind\\Service\\Factory::getRecordRouter',
      'VuFind\\RecordStats' => 'VuFind\\Service\\Factory::getRecordStats',
      'VuFind\\RecordTabPluginManager' => 'VuFind\\Service\\Factory::getRecordTabPluginManager',
      'VuFind\\RelatedPluginManager' => 'VuFind\\Service\\Factory::getRelatedPluginManager',
      'VuFind\\ResolverDriverPluginManager' => 'VuFind\\Service\\Factory::getResolverDriverPluginManager',
      'VuFind\\Search\\BackendManager' => 'VuFind\\Service\\Factory::getSearchBackendManager',
      'VuFind\\Search\\Memory' => 'VuFind\\Service\\Factory::getSearchMemory',
      'VuFind\\SearchOptionsPluginManager' => 'Swissbib\\Services\\Factory::getSearchOptionsPluginManager',
      'VuFind\\SearchParamsPluginManager' => 'Swissbib\\Services\\Factory::getSearchParamsPluginManager',
      'VuFind\\SearchResultsPluginManager' => 'Swissbib\\Services\\Factory::getSearchResultsPluginManager',
      'VuFind\\SearchRunner' => 'VuFind\\Service\\Factory::getSearchRunner',
      'VuFind\\SearchSpecsReader' => 'VuFind\\Service\\Factory::getSearchSpecsReader',
      'VuFind\\SearchStats' => 'VuFind\\Service\\Factory::getSearchStats',
      'VuFind\\SearchTabsHelper' => 'VuFind\\Service\\Factory::getSearchTabsHelper',
      'VuFind\\NationalLicences' => 'VuFind\\Service\\Factory::getNationalLicences',
      'VuFind\\SessionManager' => 'VuFind\\Session\\ManagerFactory',
      'VuFind\\SessionPluginManager' => 'VuFind\\Service\\Factory::getSessionPluginManager',
      'VuFind\\SMS' => 'VuFind\\SMS\\Factory',
      'VuFind\\Solr\\Writer' => 'VuFind\\Service\\Factory::getSolrWriter',
      'VuFind\\StatisticsDriverPluginManager' => 'VuFind\\Service\\Factory::getStatisticsDriverPluginManager',
      'VuFind\\Tags' => 'VuFind\\Service\\Factory::getTags',
      'VuFind\\Translator' => 'VuFind\\Service\\Factory::getTranslator',
      'VuFind\\WorldCatUtils' => 'VuFind\\Service\\Factory::getWorldCatUtils',
      'Swissbib\\HoldingsHelper' => 'Swissbib\\RecordDriver\\Helper\\Factory::getHoldingsHelper',
      'Swissbib\\Services\\RedirectProtocolWrapper' => 'Swissbib\\Services\\Factory::getProtocolWrapper',
      'Swissbib\\TargetsProxy\\TargetsProxy' => 'Swissbib\\TargetsProxy\\Factory::getTargetsProxy',
      'Swissbib\\TargetsProxy\\IpMatcher' => 'Swissbib\\TargetsProxy\\Factory::getIpMatcher',
      'Swissbib\\TargetsProxy\\UrlMatcher' => 'Swissbib\\TargetsProxy\\Factory::getURLMatcher',
      'Swissbib\\Theme\\Theme' => 'Swissbib\\Services\\Factory::getThemeConfigs',
      'Swissbib\\Libadmin\\Importer' => 'Swissbib\\Libadmin\\Factory::getLibadminImporter',
      'Swissbib\\Tab40Importer' => 'Swissbib\\Tab40Import\\Factory::getTab40Importer',
      'Swissbib\\LocationMap' => 'Swissbib\\RecordDriver\\Helper\\Factory::getLocationMap',
      'Swissbib\\EbooksOnDemand' => 'Swissbib\\RecordDriver\\Helper\\Factory::getEbooksOnDemand',
      'Swissbib\\Availability' => 'Swissbib\\RecordDriver\\Helper\\Factory::getAvailabiltyHelper',
      'Swissbib\\BibCodeHelper' => 'Swissbib\\RecordDriver\\Helper\\Factory::getBibCodeHelper',
      'Swissbib\\FavoriteInstitutions\\DataSource' => 'Swissbib\\Favorites\\Factory::getFavoritesDataSource',
      'Swissbib\\FavoriteInstitutions\\Manager' => 'Swissbib\\Favorites\\Factory::getFavoritesManager',
      'Swissbib\\ExtendedSolrFactoryHelper' => 'Swissbib\\VuFind\\Search\\Helper\\Factory::getExtendedSolrFactoryHelper',
      'Swissbib\\TypeLabelMappingHelper' => 'Swissbib\\VuFind\\Search\\Helper\\Factory::getTypeLabelMappingHelper',
      'Swissbib\\Highlight\\SolrConfigurator' => 'Swissbib\\Services\\Factory::getSOLRHighlightingConfigurator',
      'Swissbib\\Logger' => 'Swissbib\\Services\\Factory::getSwissbibLogger',
      'Swissbib\\RecordDriver\\SolrDefaultAdapter' => 'Swissbib\\RecordDriver\\Factory::getSolrDefaultAdapter',
      'sbSpellingProcessor' => 'Swissbib\\VuFind\\Search\\Solr\\Factory::getSpellchecker',
      'sbSpellingResults' => 'Swissbib\\VuFind\\Search\\Solr\\Factory::getSpellingResults',
      'Swissbib\\Hierarchy\\SimpleTreeGenerator' => 'Swissbib\\Hierarchy\\Factory::getSimpleTreeGenerator',
      'Swissbib\\Hierarchy\\MultiTreeGenerator' => 'Swissbib\\Hierarchy\\Factory::getMultiTreeGenerator',
      'Swissbib\\SearchTabsHelper' => 'Swissbib\\View\\Helper\\Swissbib\\Factory::getSearchTabsHelper',
      'Swissbib\\Record\\Form\\CopyForm' => 'Swissbib\\Record\\Factory::getCopyForm',
      'Swissbib\\MyResearch\\Form\\AddressForm' => 'Swissbib\\MyResearch\\Factory::getAddressForm',
      'Swissbib\\Feedback\\Form\\FeedbackForm' => 'Swissbib\\Feedback\\Factory::getFeedbackForm',
      'Swissbib\\NationalLicenceService' => 'Swissbib\\Services\\Factory::getNationalLicenceService',
      'Swissbib\\SwitchApiService' => 'Swissbib\\Services\\Factory::getSwitchApiService',
      'Swissbib\\EmailService' => 'Swissbib\\Services\\Factory::getEmailService',
    ),
    'allow_override' => true,
    'initializers' => 
    array (
      0 => 'VuFind\\ServiceManager\\Initializer::initInstance',
    ),
    'aliases' => 
    array (
      'mvctranslator' => 'VuFind\\Translator',
      'translator' => 'VuFind\\Translator',
    ),
  ),
  'view_helpers' => 
  array (
    'factories' => 
    array (
      'ZfcRbac\\View\\Helper\\IsGranted' => 'ZfcRbac\\Factory\\IsGrantedViewHelperFactory',
      'ZfcRbac\\View\\Helper\\HasRole' => 'ZfcRbac\\Factory\\HasRoleViewHelperFactory',
      'institutionSorter' => 'Swissbib\\View\\Helper\\Factory::getInstitutionSorter',
      'extractFavoriteInstitutionsForHoldings' => 'Swissbib\\View\\Helper\\Factory::getFavoriteInstitutionsExtractor',
      'institutionDefinedAsFavorite' => 'Swissbib\\View\\Helper\\Factory::getInstitutionsAsDefinedFavorites',
      'qrCode' => 'Swissbib\\View\\Helper\\Factory::getQRCodeHelper',
      'isFavoriteInstitution' => 'Swissbib\\View\\Helper\\Factory::isFavoriteInstitutionHelper',
      'domainURL' => 'Swissbib\\View\\Helper\\Factory::getDomainURLHelper',
      'redirectProtocolWrapper' => 'Swissbib\\View\\Helper\\Factory::getRedirectProtocolWrapperHelper',
    ),
    'aliases' => 
    array (
      'isGranted' => 'ZfcRbac\\View\\Helper\\IsGranted',
      'hasRole' => 'ZfcRbac\\View\\Helper\\HasRole',
    ),
    'initializers' => 
    array (
      0 => 'VuFind\\ServiceManager\\Initializer::initZendPlugin',
    ),
    'invokables' => 
    array (
      'Authors' => 'Swissbib\\View\\Helper\\Authors',
      'facetItem' => 'Swissbib\\View\\Helper\\FacetItem',
      'facetItemLabel' => 'Swissbib\\View\\Helper\\FacetItemLabel',
      'lastSearchWord' => 'Swissbib\\View\\Helper\\LastSearchWord',
      'lastTabbedSearchUri' => 'Swissbib\\View\\Helper\\LastTabbedSearchUri',
      'mainTitle' => 'Swissbib\\View\\Helper\\MainTitle',
      'myResearchSideBar' => 'Swissbib\\View\\Helper\\MyResearchSideBar',
      'urlDisplay' => 'Swissbib\\View\\Helper\\URLDisplay',
      'number' => 'Swissbib\\View\\Helper\\Number',
      'physicalDescription' => 'Swissbib\\View\\Helper\\PhysicalDescriptions',
      'removeHighlight' => 'Swissbib\\View\\Helper\\RemoveHighlight',
      'subjectHeadingFormatter' => 'Swissbib\\View\\Helper\\SubjectHeadings',
      'SortAndPrepareFacetList' => 'Swissbib\\View\\Helper\\SortAndPrepareFacetList',
      'tabTemplate' => 'Swissbib\\View\\Helper\\TabTemplate',
      'zendTranslate' => 'Zend\\I18n\\View\\Helper\\Translate',
      'getVersion' => 'Swissbib\\View\\Helper\\GetVersion',
      'holdingActions' => 'Swissbib\\View\\Helper\\HoldingActions',
      'availabilityInfo' => 'Swissbib\\View\\Helper\\AvailabilityInfo',
      'transLocation' => 'Swissbib\\View\\Helper\\TranslateLocation',
      'qrCodeHolding' => 'Swissbib\\View\\Helper\\QrCodeHolding',
      'holdingItemsPaging' => 'Swissbib\\View\\Helper\\HoldingItemsPaging',
      'filterUntranslatedInstitutions' => 'Swissbib\\View\\Helper\\FilterUntranslatedInstitutions',
      'configAccess' => 'Swissbib\\View\\Helper\\Config',
      'layoutClass' => 'Swissbib\\View\\Helper\\LayoutClass',
    ),
  ),
  'controller_plugins' => 
  array (
    'factories' => 
    array (
      'ZfcRbac\\Mvc\\Controller\\Plugin\\IsGranted' => 'ZfcRbac\\Factory\\IsGrantedPluginFactory',
      'flashmessenger' => 'VuFind\\Controller\\Plugin\\Factory::getFlashMessenger',
      'followup' => 'VuFind\\Controller\\Plugin\\Factory::getFollowup',
      'holds' => 'VuFind\\Controller\\Plugin\\Factory::getHolds',
      'newitems' => 'VuFind\\Controller\\Plugin\\Factory::getNewItems',
      'ILLRequests' => 'VuFind\\Controller\\Plugin\\Factory::getILLRequests',
      'recaptcha' => 'VuFind\\Controller\\Plugin\\Factory::getRecaptcha',
      'reserves' => 'VuFind\\Controller\\Plugin\\Factory::getReserves',
      'result-scroller' => 'VuFind\\Controller\\Plugin\\Factory::getResultScroller',
      'storageRetrievalRequests' => 'VuFind\\Controller\\Plugin\\Factory::getStorageRetrievalRequests',
    ),
    'aliases' => 
    array (
      'isGranted' => 'ZfcRbac\\Mvc\\Controller\\Plugin\\IsGranted',
    ),
    'invokables' => 
    array (
      'db-upgrade' => 'VuFind\\Controller\\Plugin\\DbUpgrade',
      'favorites' => 'VuFind\\Controller\\Plugin\\Favorites',
      'renewals' => 'VuFind\\Controller\\Plugin\\Renewals',
    ),
  ),
  'view_manager' => 
  array (
    'template_map' => 
    array (
      'error/403' => '/usr/local/vufind/httpd/vendor/zf-commons/zfc-rbac/config/../view/error/403.phtml',
      'zend-developer-tools/toolbar/zfc-rbac' => '/usr/local/vufind/httpd/vendor/zf-commons/zfc-rbac/config/../view/zend-developer-tools/toolbar/zfc-rbac.phtml',
    ),
    'display_not_found_reason' => false,
    'display_exceptions' => true,
    'not_found_template' => 'error/404',
    'exception_template' => 'error/index',
    'template_path_stack' => 
    array (
    ),
  ),
  'zenddevelopertools' => 
  array (
    'profiler' => 
    array (
      'collectors' => 
      array (
        'zfc_rbac' => 'ZfcRbac\\Collector\\RbacCollector',
      ),
    ),
    'toolbar' => 
    array (
      'entries' => 
      array (
        'zfc_rbac' => 'zend-developer-tools/toolbar/zfc-rbac',
      ),
    ),
  ),
  'zfc_rbac' => 
  array (
    'guard_manager' => 
    array (
    ),
    'role_provider_manager' => 
    array (
      'factories' => 
      array (
        'VuFind\\Role\\DynamicRoleProvider' => 'VuFind\\Role\\DynamicRoleProviderFactory',
      ),
    ),
    'assertion_manager' => 
    array (
    ),
    'identity_provider' => 'VuFind\\AuthManager',
    'guest_role' => 'guest',
    'role_provider' => 
    array (
      'VuFind\\Role\\DynamicRoleProvider' => 
      array (
        'map_legacy_settings' => true,
      ),
    ),
    'vufind_permission_provider_manager' => 
    array (
      'factories' => 
      array (
        'ipRange' => 'VuFind\\Role\\PermissionProvider\\Factory::getIpRange',
        'ipRegEx' => 'VuFind\\Role\\PermissionProvider\\Factory::getIpRegEx',
        'serverParam' => 'VuFind\\Role\\PermissionProvider\\Factory::getServerParam',
        'shibboleth' => 'VuFind\\Role\\PermissionProvider\\Factory::getShibboleth',
        'user' => 'VuFind\\Role\\PermissionProvider\\Factory::getUser',
        'username' => 'VuFind\\Role\\PermissionProvider\\Factory::getUsername',
      ),
      'invokables' => 
      array (
        'role' => 'VuFind\\Role\\PermissionProvider\\Role',
      ),
    ),
  ),
  'router' => 
  array (
    'routes' => 
    array (
      'default' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/[:controller[/[:action]]]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'index',
            'action' => 'Home',
          ),
        ),
      ),
      'legacy-alphabrowse-results' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/AlphaBrowse/Results',
          'defaults' => 
          array (
            'controller' => 'Alphabrowse',
            'action' => 'Home',
          ),
        ),
      ),
      'legacy-bookcover' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/bookcover.php',
          'defaults' => 
          array (
            'controller' => 'cover',
            'action' => 'Show',
          ),
        ),
      ),
      'legacy-summonrecord' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Summon/Record',
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'Home',
          ),
        ),
      ),
      'legacy-worldcatrecord' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/WorldCat/Record',
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'Home',
          ),
        ),
      ),
      'record' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id[/[:tab]]]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'Home',
          ),
        ),
      ),
      'record-addcomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/AddComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'AddComment',
          ),
        ),
      ),
      'record-deletecomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/DeleteComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'DeleteComment',
          ),
        ),
      ),
      'record-addtag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/AddTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'AddTag',
          ),
        ),
      ),
      'record-deletetag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/DeleteTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'DeleteTag',
          ),
        ),
      ),
      'record-save' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/Save',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'Save',
          ),
        ),
      ),
      'record-email' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/Email',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'Email',
          ),
        ),
      ),
      'record-sms' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/SMS',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'SMS',
          ),
        ),
      ),
      'record-cite' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/Cite',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'Cite',
          ),
        ),
      ),
      'record-export' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/Export',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'Export',
          ),
        ),
      ),
      'record-rdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/RDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'RDF',
          ),
        ),
      ),
      'record-hold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/Hold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'Hold',
          ),
        ),
      ),
      'record-blockedhold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/BlockedHold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'BlockedHold',
          ),
        ),
      ),
      'record-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/Home',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'Home',
          ),
        ),
      ),
      'record-storageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/StorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'StorageRetrievalRequest',
          ),
        ),
      ),
      'record-ajaxtab' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/AjaxTab',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'AjaxTab',
          ),
        ),
      ),
      'record-blockedstorageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/BlockedStorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'BlockedStorageRetrievalRequest',
          ),
        ),
      ),
      'record-illrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/ILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'ILLRequest',
          ),
        ),
      ),
      'record-blockedillrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/BlockedILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'BlockedILLRequest',
          ),
        ),
      ),
      'record-pdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/PDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'PDF',
          ),
        ),
      ),
      'collection' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id[/[:tab]]]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'Home',
          ),
        ),
      ),
      'collection-addcomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/AddComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'AddComment',
          ),
        ),
      ),
      'collection-deletecomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/DeleteComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'DeleteComment',
          ),
        ),
      ),
      'collection-addtag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/AddTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'AddTag',
          ),
        ),
      ),
      'collection-deletetag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/DeleteTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'DeleteTag',
          ),
        ),
      ),
      'collection-save' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/Save',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'Save',
          ),
        ),
      ),
      'collection-email' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/Email',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'Email',
          ),
        ),
      ),
      'collection-sms' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/SMS',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'SMS',
          ),
        ),
      ),
      'collection-cite' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/Cite',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'Cite',
          ),
        ),
      ),
      'collection-export' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/Export',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'Export',
          ),
        ),
      ),
      'collection-rdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/RDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'RDF',
          ),
        ),
      ),
      'collection-hold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/Hold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'Hold',
          ),
        ),
      ),
      'collection-blockedhold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/BlockedHold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'BlockedHold',
          ),
        ),
      ),
      'collection-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/Home',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'Home',
          ),
        ),
      ),
      'collection-storageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/StorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'StorageRetrievalRequest',
          ),
        ),
      ),
      'collection-ajaxtab' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/AjaxTab',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'AjaxTab',
          ),
        ),
      ),
      'collection-blockedstorageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/BlockedStorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'BlockedStorageRetrievalRequest',
          ),
        ),
      ),
      'collection-illrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/ILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'ILLRequest',
          ),
        ),
      ),
      'collection-blockedillrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/BlockedILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'BlockedILLRequest',
          ),
        ),
      ),
      'collection-pdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Collection/[:id]/PDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Collection',
            'action' => 'PDF',
          ),
        ),
      ),
      'edsrecord' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id[/[:tab]]]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'Home',
          ),
        ),
      ),
      'edsrecord-addcomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/AddComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'AddComment',
          ),
        ),
      ),
      'edsrecord-deletecomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/DeleteComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'DeleteComment',
          ),
        ),
      ),
      'edsrecord-addtag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/AddTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'AddTag',
          ),
        ),
      ),
      'edsrecord-deletetag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/DeleteTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'DeleteTag',
          ),
        ),
      ),
      'edsrecord-save' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/Save',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'Save',
          ),
        ),
      ),
      'edsrecord-email' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/Email',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'Email',
          ),
        ),
      ),
      'edsrecord-sms' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/SMS',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'SMS',
          ),
        ),
      ),
      'edsrecord-cite' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/Cite',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'Cite',
          ),
        ),
      ),
      'edsrecord-export' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/Export',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'Export',
          ),
        ),
      ),
      'edsrecord-rdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/RDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'RDF',
          ),
        ),
      ),
      'edsrecord-hold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/Hold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'Hold',
          ),
        ),
      ),
      'edsrecord-blockedhold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/BlockedHold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'BlockedHold',
          ),
        ),
      ),
      'edsrecord-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/Home',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'Home',
          ),
        ),
      ),
      'edsrecord-storageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/StorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'StorageRetrievalRequest',
          ),
        ),
      ),
      'edsrecord-ajaxtab' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/AjaxTab',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'AjaxTab',
          ),
        ),
      ),
      'edsrecord-blockedstorageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/BlockedStorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'BlockedStorageRetrievalRequest',
          ),
        ),
      ),
      'edsrecord-illrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/ILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'ILLRequest',
          ),
        ),
      ),
      'edsrecord-blockedillrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/BlockedILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'BlockedILLRequest',
          ),
        ),
      ),
      'edsrecord-pdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EdsRecord/[:id]/PDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EdsRecord',
            'action' => 'PDF',
          ),
        ),
      ),
      'eitrecord' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id[/[:tab]]]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'Home',
          ),
        ),
      ),
      'eitrecord-addcomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/AddComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'AddComment',
          ),
        ),
      ),
      'eitrecord-deletecomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/DeleteComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'DeleteComment',
          ),
        ),
      ),
      'eitrecord-addtag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/AddTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'AddTag',
          ),
        ),
      ),
      'eitrecord-deletetag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/DeleteTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'DeleteTag',
          ),
        ),
      ),
      'eitrecord-save' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/Save',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'Save',
          ),
        ),
      ),
      'eitrecord-email' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/Email',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'Email',
          ),
        ),
      ),
      'eitrecord-sms' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/SMS',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'SMS',
          ),
        ),
      ),
      'eitrecord-cite' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/Cite',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'Cite',
          ),
        ),
      ),
      'eitrecord-export' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/Export',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'Export',
          ),
        ),
      ),
      'eitrecord-rdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/RDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'RDF',
          ),
        ),
      ),
      'eitrecord-hold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/Hold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'Hold',
          ),
        ),
      ),
      'eitrecord-blockedhold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/BlockedHold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'BlockedHold',
          ),
        ),
      ),
      'eitrecord-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/Home',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'Home',
          ),
        ),
      ),
      'eitrecord-storageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/StorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'StorageRetrievalRequest',
          ),
        ),
      ),
      'eitrecord-ajaxtab' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/AjaxTab',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'AjaxTab',
          ),
        ),
      ),
      'eitrecord-blockedstorageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/BlockedStorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'BlockedStorageRetrievalRequest',
          ),
        ),
      ),
      'eitrecord-illrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/ILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'ILLRequest',
          ),
        ),
      ),
      'eitrecord-blockedillrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/BlockedILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'BlockedILLRequest',
          ),
        ),
      ),
      'eitrecord-pdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/EITRecord/[:id]/PDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'EITRecord',
            'action' => 'PDF',
          ),
        ),
      ),
      'missingrecord' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id[/[:tab]]]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'Home',
          ),
        ),
      ),
      'missingrecord-addcomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/AddComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'AddComment',
          ),
        ),
      ),
      'missingrecord-deletecomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/DeleteComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'DeleteComment',
          ),
        ),
      ),
      'missingrecord-addtag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/AddTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'AddTag',
          ),
        ),
      ),
      'missingrecord-deletetag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/DeleteTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'DeleteTag',
          ),
        ),
      ),
      'missingrecord-save' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/Save',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'Save',
          ),
        ),
      ),
      'missingrecord-email' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/Email',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'Email',
          ),
        ),
      ),
      'missingrecord-sms' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/SMS',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'SMS',
          ),
        ),
      ),
      'missingrecord-cite' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/Cite',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'Cite',
          ),
        ),
      ),
      'missingrecord-export' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/Export',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'Export',
          ),
        ),
      ),
      'missingrecord-rdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/RDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'RDF',
          ),
        ),
      ),
      'missingrecord-hold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/Hold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'Hold',
          ),
        ),
      ),
      'missingrecord-blockedhold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/BlockedHold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'BlockedHold',
          ),
        ),
      ),
      'missingrecord-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/Home',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'Home',
          ),
        ),
      ),
      'missingrecord-storageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/StorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'StorageRetrievalRequest',
          ),
        ),
      ),
      'missingrecord-ajaxtab' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/AjaxTab',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'AjaxTab',
          ),
        ),
      ),
      'missingrecord-blockedstorageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/BlockedStorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'BlockedStorageRetrievalRequest',
          ),
        ),
      ),
      'missingrecord-illrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/ILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'ILLRequest',
          ),
        ),
      ),
      'missingrecord-blockedillrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/BlockedILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'BlockedILLRequest',
          ),
        ),
      ),
      'missingrecord-pdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MissingRecord/[:id]/PDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MissingRecord',
            'action' => 'PDF',
          ),
        ),
      ),
      'primorecord' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id[/[:tab]]]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'Home',
          ),
        ),
      ),
      'primorecord-addcomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/AddComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'AddComment',
          ),
        ),
      ),
      'primorecord-deletecomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/DeleteComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'DeleteComment',
          ),
        ),
      ),
      'primorecord-addtag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/AddTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'AddTag',
          ),
        ),
      ),
      'primorecord-deletetag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/DeleteTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'DeleteTag',
          ),
        ),
      ),
      'primorecord-save' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/Save',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'Save',
          ),
        ),
      ),
      'primorecord-email' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/Email',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'Email',
          ),
        ),
      ),
      'primorecord-sms' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/SMS',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'SMS',
          ),
        ),
      ),
      'primorecord-cite' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/Cite',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'Cite',
          ),
        ),
      ),
      'primorecord-export' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/Export',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'Export',
          ),
        ),
      ),
      'primorecord-rdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/RDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'RDF',
          ),
        ),
      ),
      'primorecord-hold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/Hold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'Hold',
          ),
        ),
      ),
      'primorecord-blockedhold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/BlockedHold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'BlockedHold',
          ),
        ),
      ),
      'primorecord-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/Home',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'Home',
          ),
        ),
      ),
      'primorecord-storageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/StorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'StorageRetrievalRequest',
          ),
        ),
      ),
      'primorecord-ajaxtab' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/AjaxTab',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'AjaxTab',
          ),
        ),
      ),
      'primorecord-blockedstorageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/BlockedStorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'BlockedStorageRetrievalRequest',
          ),
        ),
      ),
      'primorecord-illrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/ILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'ILLRequest',
          ),
        ),
      ),
      'primorecord-blockedillrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/BlockedILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'BlockedILLRequest',
          ),
        ),
      ),
      'primorecord-pdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/PrimoRecord/[:id]/PDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'PrimoRecord',
            'action' => 'PDF',
          ),
        ),
      ),
      'solrauthrecord' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id[/[:tab]]]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'Home',
          ),
        ),
      ),
      'solrauthrecord-addcomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/AddComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'AddComment',
          ),
        ),
      ),
      'solrauthrecord-deletecomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/DeleteComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'DeleteComment',
          ),
        ),
      ),
      'solrauthrecord-addtag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/AddTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'AddTag',
          ),
        ),
      ),
      'solrauthrecord-deletetag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/DeleteTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'DeleteTag',
          ),
        ),
      ),
      'solrauthrecord-save' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/Save',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'Save',
          ),
        ),
      ),
      'solrauthrecord-email' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/Email',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'Email',
          ),
        ),
      ),
      'solrauthrecord-sms' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/SMS',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'SMS',
          ),
        ),
      ),
      'solrauthrecord-cite' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/Cite',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'Cite',
          ),
        ),
      ),
      'solrauthrecord-export' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/Export',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'Export',
          ),
        ),
      ),
      'solrauthrecord-rdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/RDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'RDF',
          ),
        ),
      ),
      'solrauthrecord-hold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/Hold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'Hold',
          ),
        ),
      ),
      'solrauthrecord-blockedhold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/BlockedHold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'BlockedHold',
          ),
        ),
      ),
      'solrauthrecord-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/Home',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'Home',
          ),
        ),
      ),
      'solrauthrecord-storageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/StorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'StorageRetrievalRequest',
          ),
        ),
      ),
      'solrauthrecord-ajaxtab' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/AjaxTab',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'AjaxTab',
          ),
        ),
      ),
      'solrauthrecord-blockedstorageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/BlockedStorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'BlockedStorageRetrievalRequest',
          ),
        ),
      ),
      'solrauthrecord-illrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/ILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'ILLRequest',
          ),
        ),
      ),
      'solrauthrecord-blockedillrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/BlockedILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'BlockedILLRequest',
          ),
        ),
      ),
      'solrauthrecord-pdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Authority/[:id]/PDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'PDF',
          ),
        ),
      ),
      'summonrecord' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id[/[:tab]]]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'Home',
          ),
        ),
      ),
      'summonrecord-addcomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/AddComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'AddComment',
          ),
        ),
      ),
      'summonrecord-deletecomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/DeleteComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'DeleteComment',
          ),
        ),
      ),
      'summonrecord-addtag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/AddTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'AddTag',
          ),
        ),
      ),
      'summonrecord-deletetag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/DeleteTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'DeleteTag',
          ),
        ),
      ),
      'summonrecord-save' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/Save',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'Save',
          ),
        ),
      ),
      'summonrecord-email' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/Email',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'Email',
          ),
        ),
      ),
      'summonrecord-sms' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/SMS',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'SMS',
          ),
        ),
      ),
      'summonrecord-cite' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/Cite',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'Cite',
          ),
        ),
      ),
      'summonrecord-export' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/Export',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'Export',
          ),
        ),
      ),
      'summonrecord-rdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/RDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'RDF',
          ),
        ),
      ),
      'summonrecord-hold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/Hold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'Hold',
          ),
        ),
      ),
      'summonrecord-blockedhold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/BlockedHold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'BlockedHold',
          ),
        ),
      ),
      'summonrecord-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/Home',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'Home',
          ),
        ),
      ),
      'summonrecord-storageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/StorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'StorageRetrievalRequest',
          ),
        ),
      ),
      'summonrecord-ajaxtab' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/AjaxTab',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'AjaxTab',
          ),
        ),
      ),
      'summonrecord-blockedstorageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/BlockedStorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'BlockedStorageRetrievalRequest',
          ),
        ),
      ),
      'summonrecord-illrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/ILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'ILLRequest',
          ),
        ),
      ),
      'summonrecord-blockedillrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/BlockedILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'BlockedILLRequest',
          ),
        ),
      ),
      'summonrecord-pdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/SummonRecord/[:id]/PDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'SummonRecord',
            'action' => 'PDF',
          ),
        ),
      ),
      'worldcatrecord' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id[/[:tab]]]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'Home',
          ),
        ),
      ),
      'worldcatrecord-addcomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/AddComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'AddComment',
          ),
        ),
      ),
      'worldcatrecord-deletecomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/DeleteComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'DeleteComment',
          ),
        ),
      ),
      'worldcatrecord-addtag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/AddTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'AddTag',
          ),
        ),
      ),
      'worldcatrecord-deletetag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/DeleteTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'DeleteTag',
          ),
        ),
      ),
      'worldcatrecord-save' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/Save',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'Save',
          ),
        ),
      ),
      'worldcatrecord-email' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/Email',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'Email',
          ),
        ),
      ),
      'worldcatrecord-sms' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/SMS',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'SMS',
          ),
        ),
      ),
      'worldcatrecord-cite' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/Cite',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'Cite',
          ),
        ),
      ),
      'worldcatrecord-export' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/Export',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'Export',
          ),
        ),
      ),
      'worldcatrecord-rdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/RDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'RDF',
          ),
        ),
      ),
      'worldcatrecord-hold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/Hold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'Hold',
          ),
        ),
      ),
      'worldcatrecord-blockedhold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/BlockedHold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'BlockedHold',
          ),
        ),
      ),
      'worldcatrecord-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/Home',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'Home',
          ),
        ),
      ),
      'worldcatrecord-storageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/StorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'StorageRetrievalRequest',
          ),
        ),
      ),
      'worldcatrecord-ajaxtab' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/AjaxTab',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'AjaxTab',
          ),
        ),
      ),
      'worldcatrecord-blockedstorageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/BlockedStorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'BlockedStorageRetrievalRequest',
          ),
        ),
      ),
      'worldcatrecord-illrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/ILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'ILLRequest',
          ),
        ),
      ),
      'worldcatrecord-blockedillrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/BlockedILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'BlockedILLRequest',
          ),
        ),
      ),
      'worldcatrecord-pdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/WorldcatRecord/[:id]/PDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'WorldcatRecord',
            'action' => 'PDF',
          ),
        ),
      ),
      'vufindrecord' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id[/[:tab]]]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'Home',
          ),
        ),
      ),
      'vufindrecord-addcomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/AddComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'AddComment',
          ),
        ),
      ),
      'vufindrecord-deletecomment' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/DeleteComment',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'DeleteComment',
          ),
        ),
      ),
      'vufindrecord-addtag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/AddTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'AddTag',
          ),
        ),
      ),
      'vufindrecord-deletetag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/DeleteTag',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'DeleteTag',
          ),
        ),
      ),
      'vufindrecord-save' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/Save',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'Save',
          ),
        ),
      ),
      'vufindrecord-email' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/Email',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'Email',
          ),
        ),
      ),
      'vufindrecord-sms' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/SMS',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'SMS',
          ),
        ),
      ),
      'vufindrecord-cite' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/Cite',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'Cite',
          ),
        ),
      ),
      'vufindrecord-export' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/Export',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'Export',
          ),
        ),
      ),
      'vufindrecord-rdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/RDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'RDF',
          ),
        ),
      ),
      'vufindrecord-hold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/Hold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'Hold',
          ),
        ),
      ),
      'vufindrecord-blockedhold' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/BlockedHold',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'BlockedHold',
          ),
        ),
      ),
      'vufindrecord-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/Home',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'Home',
          ),
        ),
      ),
      'vufindrecord-storageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/StorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'StorageRetrievalRequest',
          ),
        ),
      ),
      'vufindrecord-ajaxtab' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/AjaxTab',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'AjaxTab',
          ),
        ),
      ),
      'vufindrecord-blockedstorageretrievalrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/BlockedStorageRetrievalRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'BlockedStorageRetrievalRequest',
          ),
        ),
      ),
      'vufindrecord-illrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/ILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'ILLRequest',
          ),
        ),
      ),
      'vufindrecord-blockedillrequest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/BlockedILLRequest',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'BlockedILLRequest',
          ),
        ),
      ),
      'vufindrecord-pdf' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/Record/[:id]/PDF',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Record',
            'action' => 'PDF',
          ),
        ),
      ),
      'userList' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MyResearch/MyList/[:id]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'MyList',
          ),
        ),
      ),
      'editList' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/MyResearch/EditList/[:id]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'EditList',
          ),
        ),
      ),
      'editLibraryCard' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/LibraryCards/editCard/[:id]',
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => 
          array (
            'controller' => 'LibraryCards',
            'action' => 'editCard',
          ),
        ),
      ),
      'alphabrowse-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Alphabrowse/Home',
          'defaults' => 
          array (
            'controller' => 'Alphabrowse',
            'action' => 'Home',
          ),
        ),
      ),
      'author-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Author/Home',
          'defaults' => 
          array (
            'controller' => 'Author',
            'action' => 'Home',
          ),
        ),
      ),
      'author-search' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Author/Search',
          'defaults' => 
          array (
            'controller' => 'Author',
            'action' => 'Search',
          ),
        ),
      ),
      'authority-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Authority/Home',
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'Home',
          ),
        ),
      ),
      'authority-record' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Authority/Record',
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'Record',
          ),
        ),
      ),
      'authority-search' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Authority/Search',
          'defaults' => 
          array (
            'controller' => 'Authority',
            'action' => 'Search',
          ),
        ),
      ),
      'browse-author' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Browse/Author',
          'defaults' => 
          array (
            'controller' => 'Browse',
            'action' => 'Author',
          ),
        ),
      ),
      'browse-dewey' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Browse/Dewey',
          'defaults' => 
          array (
            'controller' => 'Browse',
            'action' => 'Dewey',
          ),
        ),
      ),
      'browse-era' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Browse/Era',
          'defaults' => 
          array (
            'controller' => 'Browse',
            'action' => 'Era',
          ),
        ),
      ),
      'browse-genre' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Browse/Genre',
          'defaults' => 
          array (
            'controller' => 'Browse',
            'action' => 'Genre',
          ),
        ),
      ),
      'browse-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Browse/Home',
          'defaults' => 
          array (
            'controller' => 'Browse',
            'action' => 'Home',
          ),
        ),
      ),
      'browse-lcc' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Browse/LCC',
          'defaults' => 
          array (
            'controller' => 'Browse',
            'action' => 'LCC',
          ),
        ),
      ),
      'browse-region' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Browse/Region',
          'defaults' => 
          array (
            'controller' => 'Browse',
            'action' => 'Region',
          ),
        ),
      ),
      'browse-tag' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Browse/Tag',
          'defaults' => 
          array (
            'controller' => 'Browse',
            'action' => 'Tag',
          ),
        ),
      ),
      'browse-topic' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Browse/Topic',
          'defaults' => 
          array (
            'controller' => 'Browse',
            'action' => 'Topic',
          ),
        ),
      ),
      'cart-doexport' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Cart/doExport',
          'defaults' => 
          array (
            'controller' => 'Cart',
            'action' => 'doExport',
          ),
        ),
      ),
      'cart-email' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Cart/Email',
          'defaults' => 
          array (
            'controller' => 'Cart',
            'action' => 'Email',
          ),
        ),
      ),
      'cart-export' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Cart/Export',
          'defaults' => 
          array (
            'controller' => 'Cart',
            'action' => 'Export',
          ),
        ),
      ),
      'cart-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Cart/Home',
          'defaults' => 
          array (
            'controller' => 'Cart',
            'action' => 'Home',
          ),
        ),
      ),
      'cart-myresearchbulk' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Cart/MyResearchBulk',
          'defaults' => 
          array (
            'controller' => 'Cart',
            'action' => 'MyResearchBulk',
          ),
        ),
      ),
      'cart-processor' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Cart/Processor',
          'defaults' => 
          array (
            'controller' => 'Cart',
            'action' => 'Processor',
          ),
        ),
      ),
      'cart-save' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Cart/Save',
          'defaults' => 
          array (
            'controller' => 'Cart',
            'action' => 'Save',
          ),
        ),
      ),
      'cart-searchresultsbulk' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Cart/SearchResultsBulk',
          'defaults' => 
          array (
            'controller' => 'Cart',
            'action' => 'SearchResultsBulk',
          ),
        ),
      ),
      'collections-bytitle' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Collections/ByTitle',
          'defaults' => 
          array (
            'controller' => 'Collections',
            'action' => 'ByTitle',
          ),
        ),
      ),
      'collections-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Collections/Home',
          'defaults' => 
          array (
            'controller' => 'Collections',
            'action' => 'Home',
          ),
        ),
      ),
      'combined-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Combined/Home',
          'defaults' => 
          array (
            'controller' => 'Combined',
            'action' => 'Home',
          ),
        ),
      ),
      'combined-results' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Combined/Results',
          'defaults' => 
          array (
            'controller' => 'Combined',
            'action' => 'Results',
          ),
        ),
      ),
      'combined-searchbox' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Combined/SearchBox',
          'defaults' => 
          array (
            'controller' => 'Combined',
            'action' => 'SearchBox',
          ),
        ),
      ),
      'confirm-confirm' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Confirm/Confirm',
          'defaults' => 
          array (
            'controller' => 'Confirm',
            'action' => 'Confirm',
          ),
        ),
      ),
      'cover-show' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Cover/Show',
          'defaults' => 
          array (
            'controller' => 'Cover',
            'action' => 'Show',
          ),
        ),
      ),
      'cover-unavailable' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Cover/Unavailable',
          'defaults' => 
          array (
            'controller' => 'Cover',
            'action' => 'Unavailable',
          ),
        ),
      ),
      'eds-advanced' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/EDS/Advanced',
          'defaults' => 
          array (
            'controller' => 'EDS',
            'action' => 'Advanced',
          ),
        ),
      ),
      'eds-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/EDS/Home',
          'defaults' => 
          array (
            'controller' => 'EDS',
            'action' => 'Home',
          ),
        ),
      ),
      'eds-search' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/EDS/Search',
          'defaults' => 
          array (
            'controller' => 'EDS',
            'action' => 'Search',
          ),
        ),
      ),
      'eit-advanced' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/EIT/Advanced',
          'defaults' => 
          array (
            'controller' => 'EIT',
            'action' => 'Advanced',
          ),
        ),
      ),
      'eit-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/EIT/Home',
          'defaults' => 
          array (
            'controller' => 'EIT',
            'action' => 'Home',
          ),
        ),
      ),
      'eit-search' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/EIT/Search',
          'defaults' => 
          array (
            'controller' => 'EIT',
            'action' => 'Search',
          ),
        ),
      ),
      'error-unavailable' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Error/Unavailable',
          'defaults' => 
          array (
            'controller' => 'Error',
            'action' => 'Unavailable',
          ),
        ),
      ),
      'feedback-email' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Feedback/Email',
          'defaults' => 
          array (
            'controller' => 'Feedback',
            'action' => 'Email',
          ),
        ),
      ),
      'feedback-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Feedback/Home',
          'defaults' => 
          array (
            'controller' => 'Feedback',
            'action' => 'Home',
          ),
        ),
      ),
      'help-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Help/Home',
          'defaults' => 
          array (
            'controller' => 'Help',
            'action' => 'Home',
          ),
        ),
      ),
      'install-done' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Install/Done',
          'defaults' => 
          array (
            'controller' => 'Install',
            'action' => 'Done',
          ),
        ),
      ),
      'install-fixbasicconfig' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Install/FixBasicConfig',
          'defaults' => 
          array (
            'controller' => 'Install',
            'action' => 'FixBasicConfig',
          ),
        ),
      ),
      'install-fixcache' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Install/FixCache',
          'defaults' => 
          array (
            'controller' => 'Install',
            'action' => 'FixCache',
          ),
        ),
      ),
      'install-fixdatabase' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Install/FixDatabase',
          'defaults' => 
          array (
            'controller' => 'Install',
            'action' => 'FixDatabase',
          ),
        ),
      ),
      'install-fixdependencies' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Install/FixDependencies',
          'defaults' => 
          array (
            'controller' => 'Install',
            'action' => 'FixDependencies',
          ),
        ),
      ),
      'install-fixils' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Install/FixILS',
          'defaults' => 
          array (
            'controller' => 'Install',
            'action' => 'FixILS',
          ),
        ),
      ),
      'install-fixsecurity' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Install/FixSecurity',
          'defaults' => 
          array (
            'controller' => 'Install',
            'action' => 'FixSecurity',
          ),
        ),
      ),
      'install-fixsolr' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Install/FixSolr',
          'defaults' => 
          array (
            'controller' => 'Install',
            'action' => 'FixSolr',
          ),
        ),
      ),
      'install-fixsslcerts' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Install/FixSSLCerts',
          'defaults' => 
          array (
            'controller' => 'Install',
            'action' => 'FixSSLCerts',
          ),
        ),
      ),
      'install-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Install/Home',
          'defaults' => 
          array (
            'controller' => 'Install',
            'action' => 'Home',
          ),
        ),
      ),
      'install-performsecurityfix' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Install/PerformSecurityFix',
          'defaults' => 
          array (
            'controller' => 'Install',
            'action' => 'PerformSecurityFix',
          ),
        ),
      ),
      'install-showsql' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Install/ShowSQL',
          'defaults' => 
          array (
            'controller' => 'Install',
            'action' => 'ShowSQL',
          ),
        ),
      ),
      'libguides-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/LibGuides/Home',
          'defaults' => 
          array (
            'controller' => 'LibGuides',
            'action' => 'Home',
          ),
        ),
      ),
      'libguides-results' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/LibGuides/Results',
          'defaults' => 
          array (
            'controller' => 'LibGuides',
            'action' => 'Results',
          ),
        ),
      ),
      'librarycards-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/LibraryCards/Home',
          'defaults' => 
          array (
            'controller' => 'LibraryCards',
            'action' => 'Home',
          ),
        ),
      ),
      'librarycards-selectcard' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/LibraryCards/SelectCard',
          'defaults' => 
          array (
            'controller' => 'LibraryCards',
            'action' => 'SelectCard',
          ),
        ),
      ),
      'librarycards-deletecard' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/LibraryCards/DeleteCard',
          'defaults' => 
          array (
            'controller' => 'LibraryCards',
            'action' => 'DeleteCard',
          ),
        ),
      ),
      'myresearch-account' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/Account',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'Account',
          ),
        ),
      ),
      'myresearch-changepassword' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/ChangePassword',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'ChangePassword',
          ),
        ),
      ),
      'myresearch-checkedout' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/CheckedOut',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'CheckedOut',
          ),
        ),
      ),
      'myresearch-delete' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/Delete',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'Delete',
          ),
        ),
      ),
      'myresearch-deletelist' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/DeleteList',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'DeleteList',
          ),
        ),
      ),
      'myresearch-edit' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/Edit',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'Edit',
          ),
        ),
      ),
      'myresearch-email' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/Email',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'Email',
          ),
        ),
      ),
      'myresearch-favorites' => 
      array (
        'type' => 'literal',
        'options' => 
        array (
          'route' => '/MyResearch/Lists',
          'defaults' => 
          array (
            'controller' => 'my-research',
            'action' => 'favorites',
          ),
        ),
      ),
      'myresearch-fines' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/Fines',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'Fines',
          ),
        ),
      ),
      'myresearch-holds' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/Holds',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'Holds',
          ),
        ),
      ),
      'myresearch-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/Home',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'Home',
          ),
        ),
      ),
      'myresearch-illrequests' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/ILLRequests',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'ILLRequests',
          ),
        ),
      ),
      'myresearch-logout' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/Logout',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'Logout',
          ),
        ),
      ),
      'myresearch-newpassword' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/NewPassword',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'NewPassword',
          ),
        ),
      ),
      'myresearch-profile' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/Profile',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'Profile',
          ),
        ),
      ),
      'myresearch-recover' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/Recover',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'Recover',
          ),
        ),
      ),
      'myresearch-savesearch' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/SaveSearch',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'SaveSearch',
          ),
        ),
      ),
      'myresearch-storageretrievalrequests' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/StorageRetrievalRequests',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'StorageRetrievalRequests',
          ),
        ),
      ),
      'myresearch-userlogin' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/UserLogin',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'UserLogin',
          ),
        ),
      ),
      'myresearch-verify' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/MyResearch/Verify',
          'defaults' => 
          array (
            'controller' => 'MyResearch',
            'action' => 'Verify',
          ),
        ),
      ),
      'primo-advanced' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Primo/Advanced',
          'defaults' => 
          array (
            'controller' => 'Primo',
            'action' => 'Advanced',
          ),
        ),
      ),
      'primo-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Primo/Home',
          'defaults' => 
          array (
            'controller' => 'Primo',
            'action' => 'Home',
          ),
        ),
      ),
      'primo-search' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Primo/Search',
          'defaults' => 
          array (
            'controller' => 'Primo',
            'action' => 'Search',
          ),
        ),
      ),
      'qrcode-show' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/QRCode/Show',
          'defaults' => 
          array (
            'controller' => 'QRCode',
            'action' => 'Show',
          ),
        ),
      ),
      'qrcode-unavailable' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/QRCode/Unavailable',
          'defaults' => 
          array (
            'controller' => 'QRCode',
            'action' => 'Unavailable',
          ),
        ),
      ),
      'oai-server' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/OAI/Server',
          'defaults' => 
          array (
            'controller' => 'OAI',
            'action' => 'Server',
          ),
        ),
      ),
      'pazpar2-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Pazpar2/Home',
          'defaults' => 
          array (
            'controller' => 'Pazpar2',
            'action' => 'Home',
          ),
        ),
      ),
      'pazpar2-search' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Pazpar2/Search',
          'defaults' => 
          array (
            'controller' => 'Pazpar2',
            'action' => 'Search',
          ),
        ),
      ),
      'records-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Records/Home',
          'defaults' => 
          array (
            'controller' => 'Records',
            'action' => 'Home',
          ),
        ),
      ),
      'search-advanced' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Search/Advanced',
          'defaults' => 
          array (
            'controller' => 'Search',
            'action' => 'Advanced',
          ),
        ),
      ),
      'search-email' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Search/Email',
          'defaults' => 
          array (
            'controller' => 'Search',
            'action' => 'Email',
          ),
        ),
      ),
      'search-facetlist' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Search/FacetList',
          'defaults' => 
          array (
            'controller' => 'Search',
            'action' => 'FacetList',
          ),
        ),
      ),
      'search-history' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Search/History',
          'defaults' => 
          array (
            'controller' => 'Search',
            'action' => 'History',
          ),
        ),
      ),
      'search-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Search/Home',
          'defaults' => 
          array (
            'controller' => 'Search',
            'action' => 'Home',
          ),
        ),
      ),
      'search-newitem' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Search/NewItem',
          'defaults' => 
          array (
            'controller' => 'Search',
            'action' => 'NewItem',
          ),
        ),
      ),
      'search-opensearch' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Search/OpenSearch',
          'defaults' => 
          array (
            'controller' => 'Search',
            'action' => 'OpenSearch',
          ),
        ),
      ),
      'search-reserves' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Search/Reserves',
          'defaults' => 
          array (
            'controller' => 'Search',
            'action' => 'Reserves',
          ),
        ),
      ),
      'search-results' => 
      array (
        'type' => 'segment',
        'options' => 
        array (
          'route' => '/Search/Results[/:tab]',
          'defaults' => 
          array (
            'controller' => 'Search',
            'action' => 'results',
          ),
        ),
      ),
      'search-suggest' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Search/Suggest',
          'defaults' => 
          array (
            'controller' => 'Search',
            'action' => 'Suggest',
          ),
        ),
      ),
      'summon-advanced' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Summon/Advanced',
          'defaults' => 
          array (
            'controller' => 'Summon',
            'action' => 'Advanced',
          ),
        ),
      ),
      'summon-facetlist' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Summon/FacetList',
          'defaults' => 
          array (
            'controller' => 'Summon',
            'action' => 'FacetList',
          ),
        ),
      ),
      'summon-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Summon/Home',
          'defaults' => 
          array (
            'controller' => 'Summon',
            'action' => 'Home',
          ),
        ),
      ),
      'summon-search' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Summon/Search',
          'defaults' => 
          array (
            'controller' => 'Summon',
            'action' => 'Search',
          ),
        ),
      ),
      'tag-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Tag/Home',
          'defaults' => 
          array (
            'controller' => 'Tag',
            'action' => 'Home',
          ),
        ),
      ),
      'upgrade-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Upgrade/Home',
          'defaults' => 
          array (
            'controller' => 'Upgrade',
            'action' => 'Home',
          ),
        ),
      ),
      'upgrade-fixanonymoustags' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Upgrade/FixAnonymousTags',
          'defaults' => 
          array (
            'controller' => 'Upgrade',
            'action' => 'FixAnonymousTags',
          ),
        ),
      ),
      'upgrade-fixduplicatetags' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Upgrade/FixDuplicateTags',
          'defaults' => 
          array (
            'controller' => 'Upgrade',
            'action' => 'FixDuplicateTags',
          ),
        ),
      ),
      'upgrade-fixconfig' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Upgrade/FixConfig',
          'defaults' => 
          array (
            'controller' => 'Upgrade',
            'action' => 'FixConfig',
          ),
        ),
      ),
      'upgrade-fixdatabase' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Upgrade/FixDatabase',
          'defaults' => 
          array (
            'controller' => 'Upgrade',
            'action' => 'FixDatabase',
          ),
        ),
      ),
      'upgrade-fixmetadata' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Upgrade/FixMetadata',
          'defaults' => 
          array (
            'controller' => 'Upgrade',
            'action' => 'FixMetadata',
          ),
        ),
      ),
      'upgrade-getdbcredentials' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Upgrade/GetDBCredentials',
          'defaults' => 
          array (
            'controller' => 'Upgrade',
            'action' => 'GetDBCredentials',
          ),
        ),
      ),
      'upgrade-getdbencodingpreference' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Upgrade/GetDbEncodingPreference',
          'defaults' => 
          array (
            'controller' => 'Upgrade',
            'action' => 'GetDbEncodingPreference',
          ),
        ),
      ),
      'upgrade-getsourcedir' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Upgrade/GetSourceDir',
          'defaults' => 
          array (
            'controller' => 'Upgrade',
            'action' => 'GetSourceDir',
          ),
        ),
      ),
      'upgrade-getsourceversion' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Upgrade/GetSourceVersion',
          'defaults' => 
          array (
            'controller' => 'Upgrade',
            'action' => 'GetSourceVersion',
          ),
        ),
      ),
      'upgrade-reset' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Upgrade/Reset',
          'defaults' => 
          array (
            'controller' => 'Upgrade',
            'action' => 'Reset',
          ),
        ),
      ),
      'upgrade-showsql' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Upgrade/ShowSQL',
          'defaults' => 
          array (
            'controller' => 'Upgrade',
            'action' => 'ShowSQL',
          ),
        ),
      ),
      'web-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Web/Home',
          'defaults' => 
          array (
            'controller' => 'Web',
            'action' => 'Home',
          ),
        ),
      ),
      'web-results' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Web/Results',
          'defaults' => 
          array (
            'controller' => 'Web',
            'action' => 'Results',
          ),
        ),
      ),
      'worldcat-advanced' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Worldcat/Advanced',
          'defaults' => 
          array (
            'controller' => 'Worldcat',
            'action' => 'Advanced',
          ),
        ),
      ),
      'worldcat-home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Worldcat/Home',
          'defaults' => 
          array (
            'controller' => 'Worldcat',
            'action' => 'Home',
          ),
        ),
      ),
      'worldcat-search' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Worldcat/Search',
          'defaults' => 
          array (
            'controller' => 'Worldcat',
            'action' => 'Search',
          ),
        ),
      ),
      'home' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/',
          'defaults' => 
          array (
            'controller' => 'index',
            'action' => 'Home',
          ),
        ),
      ),
      'admin' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/Admin',
          'defaults' => 
          array (
            'controller' => 'Admin',
            'action' => 'Home',
          ),
        ),
        'may_terminate' => true,
        'child_routes' => 
        array (
          'disabled' => 
          array (
            'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
            'options' => 
            array (
              'route' => '/Disabled',
              'defaults' => 
              array (
                'controller' => 'Admin',
                'action' => 'Disabled',
              ),
            ),
          ),
          'config' => 
          array (
            'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
            'options' => 
            array (
              'route' => '/Config[/:action]',
              'defaults' => 
              array (
                'controller' => 'AdminConfig',
                'action' => 'Home',
              ),
            ),
          ),
          'maintenance' => 
          array (
            'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
            'options' => 
            array (
              'route' => '/Maintenance[/:action]',
              'defaults' => 
              array (
                'controller' => 'AdminMaintenance',
                'action' => 'Home',
              ),
            ),
          ),
          'social' => 
          array (
            'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
            'options' => 
            array (
              'route' => '/Social[/:action]',
              'defaults' => 
              array (
                'controller' => 'AdminSocial',
                'action' => 'Home',
              ),
            ),
          ),
          'statistics' => 
          array (
            'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
            'options' => 
            array (
              'route' => '/Statistics[/:action]',
              'defaults' => 
              array (
                'controller' => 'AdminStatistics',
                'action' => 'Home',
              ),
            ),
          ),
          'tags' => 
          array (
            'type' => 'Zend\\Mvc\\Router\\Http\\Segment',
            'options' => 
            array (
              'route' => '/Tags[/:action]',
              'defaults' => 
              array (
                'controller' => 'AdminTags',
                'action' => 'Home',
              ),
            ),
          ),
        ),
      ),
      'accountWithLocation' => 
      array (
        'type' => 'segment',
        'options' => 
        array (
          'route' => '/MyResearch/:action/:location',
          'defaults' => 
          array (
            'controller' => 'my-research',
            'action' => 'Profile',
            'location' => 'baselbern',
          ),
          'constraints' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'location' => '[a-z]+',
          ),
        ),
      ),
      'myresearch-settings' => 
      array (
        'type' => 'literal',
        'options' => 
        array (
          'route' => '/MyResearch/Settings',
          'defaults' => 
          array (
            'controller' => 'my-research',
            'action' => 'settings',
          ),
        ),
      ),
      'national-licences' => 
      array (
        'type' => 'segment',
        'options' => 
        array (
          'route' => '/NationalLicences[/:action]',
          'defaults' => 
          array (
            'controller' => 'national-licences',
            'action' => 'index',
          ),
          'constraints' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
        ),
      ),
      'national-licenses-signpost' => 
      array (
        'type' => 'segment',
        'options' => 
        array (
          'route' => '/MyResearchNationalLicenses[/:action]',
          'defaults' => 
          array (
            'controller' => 'national-licenses-signpost',
            'action' => 'nlsignpost',
          ),
          'constraints' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
        ),
      ),
      'help-page' => 
      array (
        'type' => 'segment',
        'options' => 
        array (
          'route' => '/HelpPage[/:topic]',
          'defaults' => 
          array (
            'controller' => 'helppage',
            'action' => 'index',
          ),
        ),
      ),
      'holdings-ajax' => 
      array (
        'type' => 'segment',
        'options' => 
        array (
          'route' => '/Holdings/:record/:institution',
          'defaults' => 
          array (
            'controller' => 'holdings',
            'action' => 'list',
          ),
        ),
      ),
      'holdings-holding-items' => 
      array (
        'type' => 'segment',
        'options' => 
        array (
          'route' => '/Holdings/:record/:institution/items/:resource',
          'defaults' => 
          array (
            'controller' => 'holdings',
            'action' => 'holdingItems',
          ),
        ),
      ),
      'myresearch-favorite-institutions' => 
      array (
        'type' => 'segment',
        'options' => 
        array (
          'route' => '/MyResearch/Favorites[/:action]',
          'defaults' => 
          array (
            'controller' => 'institutionFavorites',
            'action' => 'display',
          ),
        ),
      ),
      'myresearch-photocopies' => 
      array (
        'type' => 'literal',
        'options' => 
        array (
          'route' => '/MyResearch/Photocopies',
          'defaults' => 
          array (
            'controller' => 'my-research',
            'action' => 'photocopies',
          ),
        ),
      ),
      'myresearch-bookings' => 
      array (
        'type' => 'literal',
        'options' => 
        array (
          'route' => '/MyResearch/Bookings',
          'defaults' => 
          array (
            'controller' => 'my-research',
            'action' => 'bookings',
          ),
        ),
      ),
      'myresearch-changeaddress' => 
      array (
        'type' => 'literal',
        'options' => 
        array (
          'route' => '/MyResearch/Address',
          'defaults' => 
          array (
            'controller' => 'my-research',
            'action' => 'changeAddress',
          ),
        ),
      ),
      'record-copy' => 
      array (
        'type' => 'segment',
        'options' => 
        array (
          'route' => '/Record/:id/Copy',
          'defaults' => 
          array (
            'controller' => 'record',
            'action' => 'copy',
          ),
        ),
      ),
    ),
  ),
  'controllers' => 
  array (
    'factories' => 
    array (
      'browse' => 'VuFind\\Controller\\Factory::getBrowseController',
      'cart' => 'VuFind\\Controller\\Factory::getCartController',
      'collection' => 'VuFind\\Controller\\Factory::getCollectionController',
      'collections' => 'VuFind\\Controller\\Factory::getCollectionsController',
      'record' => 'Swissbib\\Controller\\Factory::getRecordController',
      'upgrade' => 'VuFind\\Controller\\Factory::getUpgradeController',
      'national-licences' => 'Swissbib\\Controller\\Factory::getNationalLicenceController',
      'national-licenses-signpost' => 'Swissbib\\Controller\\Factory::getMyResearchNationalLicenceController',
    ),
    'invokables' => 
    array (
      'ajax' => 'Swissbib\\Controller\\AjaxController',
      'alphabrowse' => 'VuFind\\Controller\\AlphabrowseController',
      'author' => 'VuFind\\Controller\\AuthorController',
      'authority' => 'VuFind\\Controller\\AuthorityController',
      'combined' => 'VuFind\\Controller\\CombinedController',
      'confirm' => 'VuFind\\Controller\\ConfirmController',
      'cover' => 'Swissbib\\Controller\\CoverController',
      'eds' => 'VuFind\\Controller\\EdsController',
      'edsrecord' => 'VuFind\\Controller\\EdsrecordController',
      'eit' => 'VuFind\\Controller\\EITController',
      'eitrecord' => '\\VuFind\\Controller\\EITrecordController',
      'error' => 'VuFind\\Controller\\ErrorController',
      'feedback' => 'Swissbib\\Controller\\FeedbackController',
      'help' => 'VuFind\\Controller\\HelpController',
      'hierarchy' => 'VuFind\\Controller\\HierarchyController',
      'index' => 'VuFind\\Controller\\IndexController',
      'install' => 'Swissbib\\Controller\\NoProductiveSupportController',
      'libguides' => 'VuFind\\Controller\\LibGuidesController',
      'librarycards' => 'VuFind\\Controller\\LibraryCardsController',
      'missingrecord' => 'VuFind\\Controller\\MissingrecordController',
      'my-research' => 'Swissbib\\Controller\\MyResearchController',
      'oai' => 'VuFind\\Controller\\OaiController',
      'pazpar2' => 'VuFind\\Controller\\Pazpar2Controller',
      'primo' => 'VuFind\\Controller\\PrimoController',
      'primorecord' => 'VuFind\\Controller\\PrimorecordController',
      'qrcode' => 'VuFind\\Controller\\QRCodeController',
      'records' => 'VuFind\\Controller\\RecordsController',
      'search' => 'Swissbib\\Controller\\SearchController',
      'summon' => 'Swissbib\\Controller\\SummonController',
      'summonrecord' => 'VuFind\\Controller\\SummonrecordController',
      'tag' => 'VuFind\\Controller\\TagController',
      'web' => 'VuFind\\Controller\\WebController',
      'worldcat' => 'VuFind\\Controller\\WorldcatController',
      'worldcatrecord' => 'VuFind\\Controller\\WorldcatrecordController',
      'admin' => 'VuFindAdmin\\Controller\\AdminController',
      'adminconfig' => 'VuFindAdmin\\Controller\\ConfigController',
      'adminsocial' => 'VuFindAdmin\\Controller\\SocialstatsController',
      'adminmaintenance' => 'VuFindAdmin\\Controller\\MaintenanceController',
      'adminstatistics' => 'VuFindAdmin\\Controller\\StatisticsController',
      'admintags' => 'VuFindAdmin\\Controller\\TagsController',
      'generate' => 'VuFindConsole\\Controller\\GenerateController',
      'harvest' => 'VuFindConsole\\Controller\\HarvestController',
      'import' => 'VuFindConsole\\Controller\\ImportController',
      'language' => 'VuFindConsole\\Controller\\LanguageController',
      'util' => 'VuFindConsole\\Controller\\UtilController',
      'helppage' => 'Swissbib\\Controller\\HelpPageController',
      'libadminsync' => 'Swissbib\\Controller\\LibadminSyncController',
      'holdings' => 'Swissbib\\Controller\\HoldingsController',
      'tab40import' => 'Swissbib\\Controller\\Tab40ImportController',
      'institutionFavorites' => 'Swissbib\\Controller\\FavoritesController',
      'hierarchycache' => 'Swissbib\\Controller\\HierarchyCacheController',
      'shibtest' => 'Swissbib\\Controller\\ShibtestController',
      'upgrade' => 'Swissbib\\Controller\\NoProductiveSupportController',
      'console' => 'Swissbib\\Controller\\ConsoleController',
    ),
  ),
  'translator' => 
  array (
  ),
  'vufind' => 
  array (
    'config_reader' => 
    array (
      'abstract_factories' => 
      array (
        0 => 'VuFind\\Config\\PluginFactory',
      ),
    ),
    'pgsql_seq_mapping' => 
    array (
      'comments' => 
      array (
        0 => 'id',
        1 => 'comments_id_seq',
      ),
      'oai_resumption' => 
      array (
        0 => 'id',
        1 => 'oai_resumption_id_seq',
      ),
      'record' => 
      array (
        0 => 'id',
        1 => 'record_id_seq',
      ),
      'resource' => 
      array (
        0 => 'id',
        1 => 'resource_id_seq',
      ),
      'resource_tags' => 
      array (
        0 => 'id',
        1 => 'resource_tags_id_seq',
      ),
      'search' => 
      array (
        0 => 'id',
        1 => 'search_id_seq',
      ),
      'session' => 
      array (
        0 => 'id',
        1 => 'session_id_seq',
      ),
      'tags' => 
      array (
        0 => 'id',
        1 => 'tags_id_seq',
      ),
      'user' => 
      array (
        0 => 'id',
        1 => 'user_id_seq',
      ),
      'user_list' => 
      array (
        0 => 'id',
        1 => 'user_list_id_seq',
      ),
      'user_resource' => 
      array (
        0 => 'id',
        1 => 'user_resource_id_seq',
      ),
    ),
    'plugin_managers' => 
    array (
      'auth' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'VuFind\\Auth\\PluginFactory',
        ),
        'factories' => 
        array (
          'choiceauth' => 'VuFind\\Auth\\Factory::getChoiceAuth',
          'facebook' => 'VuFind\\Auth\\Factory::getFacebook',
          'ils' => 'VuFind\\Auth\\Factory::getILS',
          'multiils' => 'VuFind\\Auth\\Factory::getMultiILS',
          'shibbolethmock' => 'Swissbib\\VuFind\\Auth\\Factory::getShibMock',
        ),
        'invokables' => 
        array (
          'cas' => 'VuFind\\Auth\\CAS',
          'database' => 'VuFind\\Auth\\Database',
          'ldap' => 'VuFind\\Auth\\LDAP',
          'multiauth' => 'VuFind\\Auth\\MultiAuth',
          'shibboleth' => 'Swissbib\\VuFind\\Auth\\Shibboleth',
          'sip2' => 'VuFind\\Auth\\SIP2',
        ),
        'aliases' => 
        array (
          'db' => 'Database',
          'sip' => 'Sip2',
        ),
      ),
      'autocomplete' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'VuFind\\Autocomplete\\PluginFactory',
        ),
        'factories' => 
        array (
          'solr' => 'Swissbib\\VuFind\\Autocomplete\\Factory::getSolr',
          'solrauth' => 'VuFind\\Autocomplete\\Factory::getSolrAuth',
          'solrcn' => 'VuFind\\Autocomplete\\Factory::getSolrCN',
          'solrreserves' => 'VuFind\\Autocomplete\\Factory::getSolrReserves',
        ),
        'invokables' => 
        array (
          'none' => 'VuFind\\Autocomplete\\None',
          'oclcidentities' => 'VuFind\\Autocomplete\\OCLCIdentities',
          'tag' => 'VuFind\\Autocomplete\\Tag',
        ),
        'aliases' => 
        array (
          'noautocomplete' => 'None',
          'oclcidentitiesautocomplete' => 'OCLCIdentities',
          'solrautocomplete' => 'Solr',
          'solrauthautocomplete' => 'SolrAuth',
          'solrcnautocomplete' => 'SolrCN',
          'solrreservesautocomplete' => 'SolrReserves',
          'tagautocomplete' => 'Tag',
        ),
      ),
      'content' => 
      array (
        'factories' => 
        array (
          'authornotes' => 'VuFind\\Content\\Factory::getAuthorNotes',
          'excerpts' => 'VuFind\\Content\\Factory::getExcerpts',
          'reviews' => 'VuFind\\Content\\Factory::getReviews',
        ),
      ),
      'content_authornotes' => 
      array (
        'factories' => 
        array (
          'syndetics' => 'VuFind\\Content\\AuthorNotes\\Factory::getSyndetics',
          'syndeticsplus' => 'VuFind\\Content\\AuthorNotes\\Factory::getSyndeticsPlus',
        ),
      ),
      'content_excerpts' => 
      array (
        'factories' => 
        array (
          'syndetics' => 'VuFind\\Content\\Excerpts\\Factory::getSyndetics',
          'syndeticsplus' => 'VuFind\\Content\\Excerpts\\Factory::getSyndeticsPlus',
        ),
      ),
      'content_covers' => 
      array (
        'factories' => 
        array (
          'amazon' => 'Swissbib\\Content\\Covers\\Factory::getAmazon',
          'booksite' => 'VuFind\\Content\\Covers\\Factory::getBooksite',
          'buchhandel' => 'VuFind\\Content\\Covers\\Factory::getBuchhandel',
          'contentcafe' => 'VuFind\\Content\\Covers\\Factory::getContentCafe',
          'syndetics' => 'VuFind\\Content\\Covers\\Factory::getSyndetics',
        ),
        'invokables' => 
        array (
          'google' => 'VuFind\\Content\\Covers\\Google',
          'librarything' => 'VuFind\\Content\\Covers\\LibraryThing',
          'openlibrary' => 'VuFind\\Content\\Covers\\OpenLibrary',
          'summon' => 'VuFind\\Content\\Covers\\Summon',
        ),
      ),
      'content_reviews' => 
      array (
        'factories' => 
        array (
          'amazon' => 'VuFind\\Content\\Reviews\\Factory::getAmazon',
          'amazoneditorial' => 'VuFind\\Content\\Reviews\\Factory::getAmazonEditorial',
          'booksite' => 'VuFind\\Content\\Reviews\\Factory::getBooksite',
          'syndetics' => 'VuFind\\Content\\Reviews\\Factory::getSyndetics',
          'syndeticsplus' => 'VuFind\\Content\\Reviews\\Factory::getSyndeticsPlus',
        ),
        'invokables' => 
        array (
          'guardian' => 'VuFind\\Content\\Reviews\\Guardian',
        ),
      ),
      'db_table' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'VuFind\\Db\\Table\\PluginFactory',
        ),
        'factories' => 
        array (
          'resource' => 'VuFind\\Db\\Table\\Factory::getResource',
          'resourcetags' => 'VuFind\\Db\\Table\\Factory::getResourceTags',
          'tags' => 'VuFind\\Db\\Table\\Factory::getTags',
          'user' => 'VuFind\\Db\\Table\\Factory::getUser',
          'userlist' => 'VuFind\\Db\\Table\\Factory::getUserList',
        ),
        'invokables' => 
        array (
          'changetracker' => 'VuFind\\Db\\Table\\ChangeTracker',
          'comments' => 'VuFind\\Db\\Table\\Comments',
          'oairesumption' => 'VuFind\\Db\\Table\\OaiResumption',
          'record' => 'VuFind\\Db\\Table\\Record',
          'search' => 'VuFind\\Db\\Table\\Search',
          'session' => 'VuFind\\Db\\Table\\Session',
          'userresource' => 'VuFind\\Db\\Table\\UserResource',
          'userstats' => 'VuFind\\Db\\Table\\UserStats',
          'userstatsfields' => 'VuFind\\Db\\Table\\UserStatsFields',
        ),
      ),
      'hierarchy_driver' => 
      array (
        'factories' => 
        array (
          'default' => 'VuFind\\Hierarchy\\Driver\\Factory::getHierarchyDefault',
          'flat' => 'VuFind\\Hierarchy\\Driver\\Factory::getHierarchyFlat',
          'series' => 'Swissbib\\VuFind\\Hierarchy\\Factory::getHierarchyDriverSeries',
          'archival' => 'Swissbib\\VuFind\\Hierarchy\\Factory::getHierarchyDriverArchival',
        ),
      ),
      'hierarchy_treedataformatter' => 
      array (
        'invokables' => 
        array (
          'json' => 'Swissbib\\VuFind\\Hierarchy\\TreeDataFormatter\\Json',
          'xml' => 'VuFind\\Hierarchy\\TreeDataFormatter\\Xml',
        ),
      ),
      'hierarchy_treedatasource' => 
      array (
        'factories' => 
        array (
          'solr' => 'VuFind\\Hierarchy\\TreeDataSource\\Factory::getSolr',
        ),
        'invokables' => 
        array (
          'xmlfile' => 'VuFind\\Hierarchy\\TreeDataSource\\XMLFile',
        ),
      ),
      'hierarchy_treerenderer' => 
      array (
        'factories' => 
        array (
          'jstree' => 'Swissbib\\VuFind\\Hierarchy\\Factory::getJSTree',
        ),
      ),
      'ils_driver' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'VuFind\\ILS\\Driver\\PluginFactory',
        ),
        'factories' => 
        array (
          'aleph' => 'Swissbib\\VuFind\\ILS\\Driver\\Factory::getAlephDriver',
          'daia' => 'VuFind\\ILS\\Driver\\Factory::getDAIA',
          'demo' => 'VuFind\\ILS\\Driver\\Factory::getDemo',
          'horizon' => 'VuFind\\ILS\\Driver\\Factory::getHorizon',
          'horizonxmlapi' => 'VuFind\\ILS\\Driver\\Factory::getHorizonXMLAPI',
          'lbs4' => 'VuFind\\ILS\\Driver\\Factory::getLBS4',
          'multibackend' => 'Swissbib\\VuFind\\ILS\\Driver\\Factory::getMultiBackend',
          'noils' => 'VuFind\\ILS\\Driver\\Factory::getNoILS',
          'paia' => 'VuFind\\ILS\\Driver\\Factory::getPAIA',
          'kohailsdi' => 'VuFind\\ILS\\Driver\\Factory::getKohaILSDI',
          'unicorn' => 'VuFind\\ILS\\Driver\\Factory::getUnicorn',
          'voyager' => 'VuFind\\ILS\\Driver\\Factory::getVoyager',
          'voyagerrestful' => 'VuFind\\ILS\\Driver\\Factory::getVoyagerRestful',
        ),
        'invokables' => 
        array (
          'amicus' => 'VuFind\\ILS\\Driver\\Amicus',
          'claviussql' => 'VuFind\\ILS\\Driver\\ClaviusSQL',
          'evergreen' => 'VuFind\\ILS\\Driver\\Evergreen',
          'innovative' => 'VuFind\\ILS\\Driver\\Innovative',
          'koha' => 'VuFind\\ILS\\Driver\\Koha',
          'newgenlib' => 'VuFind\\ILS\\Driver\\NewGenLib',
          'polaris' => 'VuFind\\ILS\\Driver\\Polaris',
          'sample' => 'VuFind\\ILS\\Driver\\Sample',
          'sierra' => 'VuFind\\ILS\\Driver\\Sierra',
          'symphony' => 'VuFind\\ILS\\Driver\\Symphony',
          'virtua' => 'VuFind\\ILS\\Driver\\Virtua',
          'xcncip2' => 'VuFind\\ILS\\Driver\\XCNCIP2',
        ),
      ),
      'recommend' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'VuFind\\Recommend\\PluginFactory',
        ),
        'factories' => 
        array (
          'authorfacets' => 'VuFind\\Recommend\\Factory::getAuthorFacets',
          'authorinfo' => 'VuFind\\Recommend\\Factory::getAuthorInfo',
          'authorityrecommend' => 'VuFind\\Recommend\\Factory::getAuthorityRecommend',
          'catalogresults' => 'VuFind\\Recommend\\Factory::getCatalogResults',
          'collectionsidefacets' => 'VuFind\\Recommend\\Factory::getCollectionSideFacets',
          'dplaterms' => 'VuFind\\Recommend\\Factory::getDPLATerms',
          'europeanaresults' => 'VuFind\\Recommend\\Factory::getEuropeanaResults',
          'expandfacets' => 'VuFind\\Recommend\\Factory::getExpandFacets',
          'favoritefacets' => 'Swissbib\\Services\\Factory::getFavoriteFacets',
          'mapselection' => 'VuFind\\Recommend\\Factory::getMapSelection',
          'resultgooglemapajax' => 'VuFind\\Recommend\\Factory::getResultGoogleMapAjax',
          'sidefacets' => 'Swissbib\\Recommend\\Factory::getSideFacets',
          'randomrecommend' => 'VuFind\\Recommend\\Factory::getRandomRecommend',
          'summonbestbets' => 'VuFind\\Recommend\\Factory::getSummonBestBets',
          'summondatabases' => 'VuFind\\Recommend\\Factory::getSummonDatabases',
          'summonresults' => 'VuFind\\Recommend\\Factory::getSummonResults',
          'summontopics' => 'VuFind\\Recommend\\Factory::getSummonTopics',
          'switchquery' => 'VuFind\\Recommend\\Factory::getSwitchQuery',
          'topfacets' => 'VuFind\\Recommend\\Factory::getTopFacets',
          'visualfacets' => 'VuFind\\Recommend\\Factory::getVisualFacets',
          'webresults' => 'VuFind\\Recommend\\Factory::getWebResults',
          'worldcatidentities' => 'VuFind\\Recommend\\Factory::getWorldCatIdentities',
          'topiprange' => 'Swissbib\\Recommend\\Factory::getTopIpRange',
        ),
        'invokables' => 
        array (
          'alphabrowselink' => 'VuFind\\Recommend\\AlphaBrowseLink',
          'europeanaresultsdeferred' => 'VuFind\\Recommend\\EuropeanaResultsDeferred',
          'facetcloud' => 'VuFind\\Recommend\\FacetCloud',
          'libraryh3lp' => 'VuFind\\Recommend\\Libraryh3lp',
          'openlibrarysubjects' => 'VuFind\\Recommend\\OpenLibrarySubjects',
          'openlibrarysubjectsdeferred' => 'VuFind\\Recommend\\OpenLibrarySubjectsDeferred',
          'pubdatevisajax' => 'VuFind\\Recommend\\PubDateVisAjax',
          'removefilters' => 'VuFind\\Recommend\\RemoveFilters',
          'spellingsuggestions' => 'VuFind\\Recommend\\SpellingSuggestions',
          'summonbestbetsdeferred' => 'VuFind\\Recommend\\SummonBestBetsDeferred',
          'summondatabasesdeferred' => 'VuFind\\Recommend\\SummonDatabasesDeferred',
          'summonresultsdeferred' => 'VuFind\\Recommend\\SummonResultsDeferred',
          'switchtype' => 'VuFind\\Recommend\\SwitchType',
          'worldcatterms' => 'VuFind\\Recommend\\Deprecated',
        ),
      ),
      'recorddriver' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'VuFind\\RecordDriver\\PluginFactory',
        ),
        'factories' => 
        array (
          'eds' => 'VuFind\\RecordDriver\\Factory::getEDS',
          'eit' => 'VuFind\\RecordDriver\\Factory::getEIT',
          'missing' => 'Swissbib\\RecordDriver\\Factory::getRecordDriverMissing',
          'pazpar2' => 'VuFind\\RecordDriver\\Factory::getPazpar2',
          'primo' => 'VuFind\\RecordDriver\\Factory::getPrimo',
          'solrauth' => 'VuFind\\RecordDriver\\Factory::getSolrAuth',
          'solrdefault' => 'VuFind\\RecordDriver\\Factory::getSolrDefault',
          'solrmarc' => 'Swissbib\\RecordDriver\\Factory::getSolrMarcRecordDriver',
          'solrmarcremote' => 'VuFind\\RecordDriver\\Factory::getSolrMarcRemote',
          'solrreserves' => 'VuFind\\RecordDriver\\Factory::getSolrReserves',
          'solrweb' => 'VuFind\\RecordDriver\\Factory::getSolrWeb',
          'summon' => 'Swissbib\\RecordDriver\\Factory::getSummonRecordDriver',
          'worldcat' => 'Swissbib\\RecordDriver\\Factory::getWorldCatRecordDriver',
        ),
        'invokables' => 
        array (
          'libguides' => 'VuFind\\RecordDriver\\LibGuides',
        ),
      ),
      'recordtab' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'VuFind\\RecordTab\\PluginFactory',
        ),
        'factories' => 
        array (
          'collectionhierarchytree' => 'VuFind\\RecordTab\\Factory::getCollectionHierarchyTree',
          'collectionlist' => 'VuFind\\RecordTab\\Factory::getCollectionList',
          'excerpt' => 'VuFind\\RecordTab\\Factory::getExcerpt',
          'hierarchytree' => 'Swissbib\\RecordTab\\Factory::getHierarchyTree',
          'holdingsils' => 'VuFind\\RecordTab\\Factory::getHoldingsILS',
          'holdingsworldcat' => 'VuFind\\RecordTab\\Factory::getHoldingsWorldCat',
          'map' => 'VuFind\\RecordTab\\Factory::getMap',
          'preview' => 'VuFind\\RecordTab\\Factory::getPreview',
          'reviews' => 'VuFind\\RecordTab\\Factory::getReviews',
          'similaritemscarousel' => 'VuFind\\RecordTab\\Factory::getSimilarItemsCarousel',
          'usercomments' => 'VuFind\\RecordTab\\Factory::getUserComments',
          'hierarchytreearchival' => 'Swissbib\\RecordTab\\Factory::getHierarchyTreeArchival',
        ),
        'invokables' => 
        array (
          'description' => 'Swissbib\\RecordTab\\Description',
          'staffviewarray' => 'VuFind\\RecordTab\\StaffViewArray',
          'staffviewmarc' => 'VuFind\\RecordTab\\StaffViewMARC',
          'toc' => 'VuFind\\RecordTab\\TOC',
          'articledetails' => 'Swissbib\\RecordTab\\ArticleDetails',
        ),
        'initializers' => 
        array (
          0 => 'ZfcRbac\\Initializer\\AuthorizationServiceInitializer',
        ),
      ),
      'related' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'VuFind\\Related\\PluginFactory',
        ),
        'factories' => 
        array (
          'similar' => 'VuFind\\Related\\Factory::getSimilar',
          'worldcatsimilar' => 'VuFind\\Related\\Factory::getWorldCatSimilar',
        ),
        'invokables' => 
        array (
          'editions' => 'VuFind\\Related\\Deprecated',
          'worldcateditions' => 'VuFind\\Related\\Deprecated',
        ),
      ),
      'resolver_driver' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'VuFind\\Resolver\\Driver\\PluginFactory',
        ),
        'factories' => 
        array (
          '360link' => 'VuFind\\Resolver\\Driver\\Factory::getThreesixtylink',
          'ezb' => 'VuFind\\Resolver\\Driver\\Factory::getEzb',
          'sfx' => 'VuFind\\Resolver\\Driver\\Factory::getSfx',
          'redi' => 'VuFind\\Resolver\\Driver\\Factory::getRedi',
        ),
        'invokables' => 
        array (
          'demo' => 'VuFind\\Resolver\\Driver\\Demo',
        ),
        'aliases' => 
        array (
          'threesixtylink' => '360link',
        ),
      ),
      'search_backend' => 
      array (
        'factories' => 
        array (
          'EDS' => 'VuFind\\Search\\Factory\\EdsBackendFactory',
          'EIT' => 'VuFind\\Search\\Factory\\EITBackendFactory',
          'LibGuides' => 'VuFind\\Search\\Factory\\LibGuidesBackendFactory',
          'Pazpar2' => 'VuFind\\Search\\Factory\\Pazpar2BackendFactory',
          'Primo' => 'VuFind\\Search\\Factory\\PrimoBackendFactory',
          'Solr' => 'Swissbib\\VuFind\\Search\\Factory\\SolrDefaultBackendFactory',
          'SolrAuth' => 'VuFind\\Search\\Factory\\SolrAuthBackendFactory',
          'SolrReserves' => 'VuFind\\Search\\Factory\\SolrReservesBackendFactory',
          'SolrStats' => 'VuFind\\Search\\Factory\\SolrStatsBackendFactory',
          'SolrWeb' => 'VuFind\\Search\\Factory\\SolrWebBackendFactory',
          'Summon' => 'Swissbib\\VuFind\\Search\\Factory\\SummonBackendFactory',
          'WorldCat' => 'VuFind\\Search\\Factory\\WorldCatBackendFactory',
        ),
        'aliases' => 
        array (
          'authority' => 'SolrAuth',
          'biblio' => 'Solr',
          'reserves' => 'SolrReserves',
          'stats' => 'SolrStats',
          'VuFind' => 'Solr',
        ),
      ),
      'search_options' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'VuFind\\Search\\Options\\PluginFactory',
        ),
        'factories' => 
        array (
          'eds' => 'VuFind\\Search\\Options\\Factory::getEDS',
        ),
      ),
      'search_params' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'VuFind\\Search\\Params\\PluginFactory',
        ),
      ),
      'search_results' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'VuFind\\Search\\Results\\PluginFactory',
        ),
        'factories' => 
        array (
          'favorites' => 'VuFind\\Search\\Results\\Factory::getFavorites',
          'solr' => 'VuFind\\Search\\Results\\Factory::getSolr',
        ),
      ),
      'session' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'VuFind\\Session\\PluginFactory',
        ),
        'invokables' => 
        array (
          'database' => 'VuFind\\Session\\Database',
          'file' => 'VuFind\\Session\\File',
          'memcache' => 'VuFind\\Session\\Memcache',
        ),
        'aliases' => 
        array (
          'filesession' => 'File',
          'memcachesession' => 'Memcache',
          'mysqlsession' => 'Database',
        ),
      ),
      'statistics_driver' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'VuFind\\Statistics\\Driver\\PluginFactory',
        ),
        'factories' => 
        array (
          'file' => 'VuFind\\Statistics\\Driver\\Factory::getFile',
          'solr' => 'VuFind\\Statistics\\Driver\\Factory::getSolr',
        ),
        'invokables' => 
        array (
          'db' => 'VuFind\\Statistics\\Driver\\Db',
        ),
        'aliases' => 
        array (
          'database' => 'db',
        ),
      ),
    ),
    'recorddriver_collection_tabs' => 
    array (
      'VuFind\\RecordDriver\\AbstractBase' => 
      array (
        'tabs' => 
        array (
          'CollectionList' => 'CollectionList',
          'HierarchyTree' => 'CollectionHierarchyTree',
        ),
        'defaultTab' => NULL,
      ),
    ),
    'recorddriver_tabs' => 
    array (
      'VuFind\\RecordDriver\\EDS' => 
      array (
        'tabs' => 
        array (
          'Description' => 'Description',
          'TOC' => 'TOC',
          'UserComments' => 'UserComments',
          'Reviews' => 'Reviews',
          'Excerpt' => 'Excerpt',
          'Preview' => 'preview',
          'Details' => 'StaffViewArray',
        ),
        'defaultTab' => NULL,
      ),
      'VuFind\\RecordDriver\\Pazpar2' => 
      array (
        'tabs' => 
        array (
          'Details' => 'StaffViewMARC',
        ),
        'defaultTab' => NULL,
      ),
      'VuFind\\RecordDriver\\Primo' => 
      array (
        'tabs' => 
        array (
          'Description' => 'Description',
          'TOC' => 'TOC',
          'UserComments' => 'UserComments',
          'Reviews' => 'Reviews',
          'Excerpt' => 'Excerpt',
          'Preview' => 'preview',
          'Details' => 'StaffViewArray',
        ),
        'defaultTab' => NULL,
      ),
      'VuFind\\RecordDriver\\SolrAuth' => 
      array (
        'tabs' => 
        array (
          'Details' => 'StaffViewMARC',
        ),
        'defaultTab' => NULL,
      ),
      'VuFind\\RecordDriver\\SolrDefault' => 
      array (
        'tabs' => 
        array (
          'Holdings' => 'HoldingsILS',
          'Description' => 'Description',
          'TOC' => 'TOC',
          'UserComments' => 'UserComments',
          'Reviews' => 'Reviews',
          'Excerpt' => 'Excerpt',
          'Preview' => 'preview',
          'HierarchyTree' => 'HierarchyTree',
          'Map' => 'Map',
          'Similar' => 'SimilarItemsCarousel',
          'Details' => 'StaffViewArray',
        ),
        'defaultTab' => NULL,
      ),
      'VuFind\\RecordDriver\\SolrMarc' => 
      array (
        'tabs' => 
        array (
          'Holdings' => 'HoldingsILS',
          'Description' => 'Description',
          'TOC' => 'TOC',
          'UserComments' => 'UserComments',
          'Reviews' => 'Reviews',
          'Excerpt' => 'Excerpt',
          'Preview' => 'preview',
          'HierarchyTree' => 'HierarchyTree',
          'Map' => 'Map',
          'Similar' => 'SimilarItemsCarousel',
          'Details' => 'StaffViewMARC',
        ),
        'defaultTab' => NULL,
      ),
      'VuFind\\RecordDriver\\Summon' => 
      array (
        'tabs' => 
        array (
          'Description' => 'articledetails',
          'TOC' => NULL,
          'UserComments' => 'UserComments',
          'Reviews' => 'Reviews',
          'Excerpt' => 'Excerpt',
          'Preview' => 'preview',
          'Details' => 'StaffViewArray',
        ),
        'defaultTab' => NULL,
      ),
      'VuFind\\RecordDriver\\WorldCat' => 
      array (
        'tabs' => 
        array (
          'Holdings' => 'HoldingsWorldCat',
          'Description' => 'Description',
          'TOC' => 'TOC',
          'UserComments' => 'UserComments',
          'Reviews' => 'Reviews',
          'Excerpt' => 'Excerpt',
          'Details' => 'StaffViewMARC',
        ),
        'defaultTab' => NULL,
      ),
      'Swissbib\\RecordDriver\\SolrMarc' => 
      array (
        'tabs' => 
        array (
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
        ),
        'defaultTab' => NULL,
      ),
    ),
  ),
  'console' => 
  array (
    'router' => 
    array (
      'router_class' => '',
      'routes' => 
      array (
        'libadmin-sync' => 
        array (
          'options' => 
          array (
            'route' => 'libadmin sync [--verbose|-v] [--dry|-d] [--result|-r]',
            'defaults' => 
            array (
              'controller' => 'libadminsync',
              'action' => 'sync',
            ),
          ),
        ),
        'libadmin-sync-mapportal' => 
        array (
          'options' => 
          array (
            'route' => 'libadmin syncMapPortal [--verbose|-v] [--result|-r] [<path>] ',
            'defaults' => 
            array (
              'controller' => 'libadminsync',
              'action' => 'syncMapPortal',
            ),
          ),
        ),
        'tab40-import' => 
        array (
          'options' => 
          array (
            'route' => 'tab40import <network> <locale> <source>',
            'defaults' => 
            array (
              'controller' => 'tab40import',
              'action' => 'import',
            ),
          ),
        ),
        'hierarchy' => 
        array (
          'options' => 
          array (
            'route' => 'hierarchy [<limit>] [--verbose|-v]',
            'defaults' => 
            array (
              'controller' => 'hierarchycache',
              'action' => 'buildCache',
            ),
          ),
        ),
        'send-national-licence-users-export' => 
        array (
          'options' => 
          array (
            'route' => 'send-national-licence-users-export',
            'defaults' => 
            array (
              'controller' => 'console',
              'action' => 'sendNationalLicenceUsersExport',
            ),
          ),
        ),
        'update-national-licence-user-info' => 
        array (
          'options' => 
          array (
            'route' => 'update-national-licence-user-info',
            'defaults' => 
            array (
              'controller' => 'console',
              'action' => 'updateNationalLicenceUserInfo',
            ),
          ),
        ),
      ),
    ),
  ),
  'swissbib' => 
  array (
    'ignore_css_assets' => 
    array (
    ),
    'ignore_js_assets' => 
    array (
    ),
    'asset_manager' => 
    array (
      'resolver_configs' => 
      array (
        'paths' => 
        array (
          0 => 'Swissbib',
        ),
      ),
    ),
    'plugin_managers' => 
    array (
      'vufind_search_options' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'Swissbib\\VuFind\\Search\\Options\\PluginFactory',
        ),
      ),
      'vufind_search_params' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'Swissbib\\VuFind\\Search\\Params\\PluginFactory',
        ),
      ),
      'vufind_search_results' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'Swissbib\\VuFind\\Search\\Results\\PluginFactory',
        ),
        'factories' => 
        array (
          'favorites' => 'Swissbib\\VuFind\\Search\\Results\\Factory::getFavorites',
        ),
      ),
    ),
    'db_table' => 
    array (
      'invokeables' => 
      array (
        'nationallicence' => 'Swissbib\\VuFind\\Db\\Table\\NationalLicenceUser',
      ),
    ),
  ),
);