<?php
namespace Jusbib\Module\Config;

return [
    'router' => [
        'routes' => [
            'search-advancedClassification' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/Search/AdvancedClassification',
                    'defaults' => [
                        'controller' => 'search',
                        'action'     => 'advancedClassification'
                    ]
                ]
            ]
        ]
    ],
    'controllers' => [
        'invokables' => [
            'search' => 'Jusbib\Controller\SearchController',
        ]
    ],
    'service_manager' => [
        'factories' => [
            'Jusbib\Theme\Theme'                => 'Jusbib\Theme\Factory::getJusbibTheme',
            'Jusbib\ExtendedSolrFactoryHelper'  => 'Jusbib\VuFind\Search\Factory::getJusbibSOLRFactoryHelper',

        ]
    ],
    'swissbib' => [
        'resultTabs' => [
            'themes' => [
                'jusbib' => [
                    'swissbib'
                ]
            ]
        ]
    ],
    'jusbib' => [
        'adv_tabs' => [
            'swissbib'       => [
                'searchClassId' => 'Solr', // VuFind searchClassId
                'label'         => 'Advanced Search', // Label
                'type'          => 'swissbibsolr', // Key for custom templates
                'advSearch'     => 'search-advanced'
            ],
            'classification' => [
                'searchClassId' => 'Solr',
                'label'         => 'classification_tree',
                'type'          => 'swissbibsolr',
                'advSearch'     => 'search-advancedClassification'
            ]
        ],
        // This section contains service manager configurations for all Swissbib
        // pluggable components:
        'plugin_managers' => [
            'vufind_search_options' => [
                'abstract_factories' => ['Jusbib\VuFind\Search\Options\PluginFactory'],
            ],
            'vufind_search_params'  => [
                'abstract_factories' => ['Swissbib\VuFind\Search\Params\PluginFactory'],
            ],
            'vufind_search_results' => [
                'abstract_factories' => ['Swissbib\VuFind\Search\Results\PluginFactory'],
            ]
        ],
    ]
];
