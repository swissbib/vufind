<?php
return array(
  'extends' => 'bootstrap3',

  'less' => array(
    'active' => false,
    'components/js-tree.less',
    'compiled.less'
  ),

  'css' => array(
  ),

  'js'      => array(
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

    'lib/autocomplete.js',
  ),
  'favicon' => 'favicon.ico',
  'helpers' => array(
    'factories'  => array(
      'VuFind\View\Helper\Root\Auth' => 'Swissbib\View\Helper\Swissbib\Factory::getAuth',
      'VuFind\View\Helper\Root\Record' => 'Swissbib\View\Helper\Swissbib\Factory::getRecordHelper',
      'citation'                  => 'Swissbib\View\Helper\Swissbib\Factory::getCitation',
      'recordlink'                => 'Swissbib\View\Helper\Swissbib\Factory::getRecordLink',
      'getextendedlastsearchlink' => 'Swissbib\View\Helper\Swissbib\Factory::getExtendedLastSearchLink',
      'auth'                      => 'Swissbib\View\Helper\Swissbib\Factory::getAuth',
      'layoutClass'               => 'Swissbib\View\Helper\Swissbib\Factory::getLayoutClass',
      'searchtabs'                => 'Swissbib\View\Helper\Swissbib\Factory::getSearchTabs',
      'includeTemplate'           => 'Swissbib\View\Helper\Swissbib\Factory::getIncludeTemplate',
      'translateFacets'           => 'Swissbib\View\Helper\Swissbib\Factory::getFacetTranslator',
      'formatRelatedEntries'      => 'Swissbib\View\Helper\Swissbib\Factory::getFormatRelatedEntries',
      'piwik'                     => 'Swissbib\View\Helper\Swissbib\Factory::getPiwik',
      'nationalLicences'          => 'Swissbib\View\Helper\Swissbib\Factory::getNationalLicences',
      'autoSuggestConfig'         => 'Swissbib\View\Helper\Swissbib\Factory::getAutoSuggestConfig',
    ),
    'aliases' => array(
        'auth'   => 'VuFind\View\Helper\Root\Auth',
        'record' => 'VuFind\View\Helper\Root\Record',
    ),
    'invokables' => array(
      //'translate' => 'Swissbib\VuFind\View\Helper\Root\Translate',
    )
  )
);
