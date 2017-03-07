<?php
/**
 * Jusbib SearchController
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Jusbib_VuFind2
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Jusbib\Controller;

use Zend\View\Model\ViewModel;
use Swissbib\Controller\SearchController as SwissbibSearchController;

/**
 * ZF2 module definition for the VuFind application
 *
 * @category Jusbib_VuFind2
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class SearchController extends SwissbibSearchController
{
    /**
     * Render advanced search classification trees
     *
     * @return ViewModel
     */
    public function advancedClassificationAction()
    {
        $viewModel = parent::advancedAction();
        //reset the searchClassId to its actual value
        $viewModel->searchClassId = $this->searchClassId = 'SolrClassification';

        $viewModel->setVariable(
            'classificationTrees',
            $this->getServiceLocator()->get('Swissbib\Hierarchy\MultiTreeGenerator')
                ->getTrees($viewModel->facetList)
        );
        $viewModel->setTemplate('search/advanced');

        return $viewModel;
    }
}
