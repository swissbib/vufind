<?php

namespace SwissCollections\Module\Configuration;

use SwissCollections\Controller\AlphabrowseController;
use SwissCollections\Controller\BrowseController;
use VuFind\Controller\AbstractBaseWithConfigFactory;

$config = [
    'router' => [
        'routes' => [
            'tektonik' => [
                'type' => 'Laminas\Router\Http\Segment',
                'options' => [
                    'route' => '/Tektonik/Home',
                    'defaults' => [
                        'controller' => 'tektonik',
                        'action' => 'home',
                    ]
                ],
            ],
            'bibliographies' => [
                'type' => 'Laminas\Router\Http\Segment',
                'options' => [
                    'route' => '/Bibliographien/Home',
                    'defaults' => [
                        'controller' => 'bibliographies',
                        'action' => 'home',
                    ]
                ],
            ],
            'browse-action' => [
                'type' => 'Laminas\Router\Http\Literal',
                'options' => [
                    'route' => '/Browse/Action',
                    'defaults' => [
                        'controller' => 'Browse',
                        'action' => 'Browse',
                    ],
                ],
            ],
          'alphabrowse-home' =>
            [
              'type' => 'Laminas\Router\Http\Literal',
              'options' =>
                [
                  'route' => '/Alphabrowse/Home',
                  'defaults' =>
                    [
                      'controller' => 'Alphabrowse',
                      'action' => 'Home',
                    ],
                ],
            ],
        ]
    ],
    'controllers' => [
        'factories' => [
            'tektonik' => 'SwissCollections\Controller\Factory::getTektonikController',
            'bibliographies' => 'SwissCollections\Controller\Factory::getBibliographiesController',
            BrowseController::class => AbstractBaseWithConfigFactory::class,
            AlphabrowseController::class => AbstractBaseWithConfigFactory::class,
        ],
        'aliases' => [
            'Browse' => BrowseController::class,
            'browse' => BrowseController::class,
          'Alphabrowse' => AlphabrowseController::class,
          'alphabrowse' => AlphabrowseController::class,
        ]
    ],
//  'service_manager' => [
//    'factories' => [
//      'Swissbib\Logger' => 'SwissCollections\\Services\\Factory::getLogger',
//    ],
//  ],
  'vufind' => [
    'recorddriver_tabs' => [
      'SwissCollections\RecordDriver\SolrMarc' => [
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
    'plugin_managers' => [
      'recorddriver' => [
        'factories' => [
          'SwissCollections\RecordDriver\SolrMarc' => 'SwissCollections\RecordDriver\Factory::getSolrMarcRecordDriver',
        ],
        'aliases' => [
          'solrmarc' => 'SwissCollections\RecordDriver\SolrMarc',
        ],
      ],
    ],
  ],
];

return $config;
