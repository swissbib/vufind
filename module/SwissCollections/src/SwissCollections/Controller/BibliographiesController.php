<?php

namespace SwissCollections\Controller;

use Laminas\View\Model\ViewModel;
use Swissbib\Controller\BaseController;

class BibliographiesController extends BaseController {

    public function homeAction() {
        $data = [];
        $viewModel = new ViewModel($data);
        $viewModel->setTemplate('bibliographies/home');
        return $viewModel;
    }
}