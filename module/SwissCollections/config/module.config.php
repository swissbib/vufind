<?php

namespace SwissCollections\Module\Configuration;

use SwissCollections\Controller\BrowseController;
use VuFind\Controller\AbstractBaseWithConfigFactory;

$config = [
    'router' => [
        'routes' => [
            'abc-search' => [
                'type' => 'Laminas\Router\Http\Segment',
                'options' => [
                    'route' => '/AbcSuche/Home',
                    'defaults' => [
                        'controller' => 'abc-search',
                        'action' => 'home',
                    ]
                ],
            ],
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
            'browse-author' => [
              'type' => 'Laminas\Router\Http\Literal',
              'options' => [
                'route' => '/Browse/Author',
                'defaults' => [
                  'controller' => 'Browse',
                  'action' => 'Author',
                ],
              ],
            ],
        ]
    ],
    'controllers' => [
        'factories' => [
            'abc-search' => 'SwissCollections\Controller\Factory::getAbcSearchController',
            'tektonik' => 'SwissCollections\Controller\Factory::getTektonikController',
            'bibliographies' => 'SwissCollections\Controller\Factory::getBibliographiesController',
             BrowseController::class => AbstractBaseWithConfigFactory::class,
        ],
        'aliases' => [
          'Browse' => BrowseController::class,
          'browse' => BrowseController::class,
        ]
    ],
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
                'defaultTab' => NULL,
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
