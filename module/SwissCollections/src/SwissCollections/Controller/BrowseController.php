<?php

namespace SwissCollections\Controller;

use Laminas\View\Model\ViewModel;
use Swissbib\Controller\BaseController;

class BrowseController extends BaseController {

    public function homeAction() {
        $data = [];
        $viewModel = new ViewModel($data);
        $viewModel->setTemplate('browse/home');
        return $viewModel;
    }
}