<?php

use SwissCollections\View\Helper\Root\Browse;
use SwissCollections\View\Helper\Root\BrowseFactory;

return [
  'extends' => 'sbvfrdsingle',
  'helpers' => [
    'factories' => [
      Browse::class => BrowseFactory::class,
    ],
    'aliases' => [
      'browse' => Browse::class,
    ]
  ]
];
