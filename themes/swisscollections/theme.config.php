<?php

use SwissCollections\View\Helper\Root\AlphaBrowse;
use SwissCollections\View\Helper\Root\AlphaBrowseFactory;
use SwissCollections\View\Helper\Root\Browse;
use SwissCollections\View\Helper\Root\BrowseFactory;

return [
  'extends' => 'sbvfrdsingle',
  'helpers' => [
    'factories' => [
      Browse::class => BrowseFactory::class,
      AlphaBrowse::class => AlphaBrowseFactory::class,
    ],
    'aliases' => [
      'browse' => Browse::class,
      'alphabrowse' => AlphaBrowse::class
    ]
  ]
];
