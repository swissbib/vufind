<?php

namespace SwissCollections\Controller;

use Laminas\View\Model\ViewModel;
use Swissbib\Controller\BaseController;

class AbcSearchController extends BaseController {

    public function homeAction() {
        $data = [];
        $viewModel = new ViewModel($data);
        $viewModel->setTemplate('abcsearch/home');
        return $viewModel;
    }
}