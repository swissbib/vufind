<?php

use SwissCollections\Formatter\SubfieldFormatterRegistry;
use SwissCollections\RecordDriver\ResultListViewFieldInfo;
use SwissCollections\View\Helper\Root\Browse;
use SwissCollections\View\Helper\Root\BrowseFactory;
use SwissCollections\View\Helper\Root\ResultListViewConfigFactory;
use SwissCollections\View\Helper\Root\SubfieldFormatterRegistryFactory;

return [
    'extends' => 'sbvfrdsingle',
    'helpers' => [
        'factories' => [
            Browse::class => BrowseFactory::class,
            ResultListViewFieldInfo::class => ResultListViewConfigFactory::class,
            SubfieldFormatterRegistry::class => SubfieldFormatterRegistryFactory::class
        ],
        'aliases' => [
            'browse' => Browse::class,
            'resultListViewConfig' => ResultListViewFieldInfo::class,
            'subfieldFormatterRegistry' => SubfieldFormatterRegistry::class
        ]
    ]
];
