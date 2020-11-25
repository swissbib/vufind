<?php

namespace SwissCollections\Controller;

use Laminas\View\Model\ViewModel;
use Swissbib\Controller\BaseController;

class TektonikController extends BaseController {

    public function homeAction() {
        $data = [];
        $viewModel = new ViewModel($data);
        $viewModel->setTemplate('tektonik/home');
        return $viewModel;
    }
}