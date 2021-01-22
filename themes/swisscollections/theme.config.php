<?php

use SwissCollections\Formatter\FieldFormatterRegistry;
use SwissCollections\Formatter\FieldGroupFormatterRegistry;
use SwissCollections\Formatter\SubfieldFormatterRegistry;
use SwissCollections\RecordDriver\ResultListViewFieldInfo;
use SwissCollections\View\Helper\Root\AlphaBrowse;
use SwissCollections\View\Helper\Root\AlphaBrowseFactory;
use SwissCollections\View\Helper\Root\Browse;
use SwissCollections\View\Helper\Root\BrowseFactory;
use SwissCollections\View\Helper\Root\FieldFormatterRegistryFactory;
use SwissCollections\View\Helper\Root\FieldGroupFormatterRegistryFactory;
use SwissCollections\View\Helper\Root\ResultListViewConfigFactory;
use SwissCollections\View\Helper\Root\SubfieldFormatterRegistryFactory;

return [
    'extends' => 'sbvfrdsingle',
    'helpers' => [
        'factories' => [
            Browse::class => BrowseFactory::class,
            AlphaBrowse::class => AlphaBrowseFactory::class,
            ResultListViewFieldInfo::class => ResultListViewConfigFactory::class,
            SubfieldFormatterRegistry::class => SubfieldFormatterRegistryFactory::class,
            FieldFormatterRegistry::class => FieldFormatterRegistryFactory::class,
            FieldGroupFormatterRegistry::class => FieldGroupFormatterRegistryFactory::class,
        ],
        'aliases' => [
            'browse' => Browse::class,
            'alphabrowse' => AlphaBrowse::class,
            'resultListViewConfig' => ResultListViewFieldInfo::class,
            'subfieldFormatterRegistry' => SubfieldFormatterRegistry::class,
            'fieldFormatterRegistry' => FieldFormatterRegistry::class,
            'fieldGroupFormatterRegistry' => FieldGroupFormatterRegistry::class,
        ]
    ]
];
