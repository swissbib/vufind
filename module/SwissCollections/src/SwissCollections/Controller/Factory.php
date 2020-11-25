<?php

namespace SwissCollections\Controller;

use Laminas\ServiceManager\ServiceManager;
use VuFind\Controller\AbstractBaseFactory;

class Factory extends AbstractBaseFactory {

    public static function getKeywordSearchController(ServiceManager $sm) {
        return new KeywordSearchController($sm);
    }

    public static function getAbcSearchController(ServiceManager $sm) {
        return new AbcSearchController($sm);
    }

    public static function getTektonikController(ServiceManager $sm) {
        return new TektonikController($sm);
    }

    public static function getBibliographiesController(ServiceManager $sm) {
        return new BibliographiesController($sm);
    }

    public static function getBrowseController(ServiceManager $sm) {
        return new BrowseController($sm);
    }
}