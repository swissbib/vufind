<?php
return [
  'extends' => 'bootstrap3',

  'less' => [
    'active' => false,
    'compiled.less'
  ],

  'css' => [
  ],

  'js' => [
    'vendor/jquery/plugin/jquery.cookie.js',
    'vendor/jquery/plugin/loadmask/jquery.loadmask.js',
    'vendor/chosen/chosen.jquery.min.js',

    'vendor/jstorage/jstorage.min.js', //used for favorites - there is still some amount of JS code inline of the page -> Todo: Refactoring in upcoming Sprints
    'vendor/handlebars/handlebars.js', //wird in swissbib/AdvancedSearch.js verwendet
    'vendor/respond/respond.js:lt IE 9',
    'vendor/html5shiv/html5shiv.js:lt IE 9',

    'vendor/jsTree/jstree.min.js',

    'polyfill/es6-promise.auto.min.js',

    'swissbib/swissbib.js',
    'swissbib/common.js',
    'swissbib/AdvancedSearch.js',
    'swissbib/Holdings.js',
    'swissbib/HoldingFavorites.js',
    'swissbib/FavoriteInstitutions.js',
    'swissbib/Accordion.js',
    'swissbib/Settings.js',
    'swissbib/OffCanvas.js',
  ],
  'favicon' => 'favicon.ico',
  'helpers' => [
    'factories' => [
      'VuFind\View\Helper\Root\Auth' => 'Swissbib\View\Helper\Swissbib\Factory::getAuth',
      'Swissbib\View\Helper\Record' => 'VuFind\View\Helper\Root\RecordFactory',
      'Swissbib\VuFind\View\Helper\Root\Citation' => 'VuFind\View\Helper\Root\CitationFactory',
      'Swissbib\View\Helper\RecordLink' => 'VuFind\View\Helper\Root\RecordLinkFactory',
      'Swissbib\View\Helper\LayoutClass' => 'VuFind\View\Helper\Bootstrap3\LayoutClassFactory',
      'Swissbib\VuFind\View\Helper\Root\SearchTabs' => 'VuFind\View\Helper\Root\SearchTabsFactory',
      'nationalLicences' => 'Swissbib\View\Helper\Swissbib\Factory::getNationalLicences',
      'autoSuggestConfig' => 'Swissbib\View\Helper\Swissbib\Factory::getAutoSuggestConfig',
      'getextendedlastsearchlink' => 'Swissbib\View\Helper\Swissbib\Factory::getExtendedLastSearchLink',
      'includeTemplate' => 'Swissbib\View\Helper\Swissbib\Factory::getIncludeTemplate',
      'translateFacets' => 'Swissbib\View\Helper\Swissbib\Factory::getFacetTranslator',
      'formatRelatedEntries' => 'Swissbib\View\Helper\Swissbib\Factory::getFormatRelatedEntries',
      \Swissbib\VuFind\View\Helper\Root\Translate::class => 'Zend\ServiceManager\Factory\InvokableFactory',
    ],
    'aliases' => [
        'auth' => 'VuFind\View\Helper\Root\Auth',
        'record' => 'Swissbib\View\Helper\Record',
        'layoutClass' => 'Swissbib\View\Helper\LayoutClass',
        'citation' => 'Swissbib\VuFind\View\Helper\Root\Citation',
        'recordLink' => 'Swissbib\View\Helper\RecordLink',
        'searchtabs' => 'Swissbib\VuFind\View\Helper\Root\SearchTabs',
        'translate' => \Swissbib\VuFind\View\Helper\Root\Translate::class,
    ],
  ]
];
