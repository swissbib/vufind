<?php
/**
 * Factory
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 1/2/13
 * Time: 4:09 PM
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
 * @category Swissbib_VuFind2
 * @package  MyResearch
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\MyResearch;

use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Csrf;
use Zend\ServiceManager\ServiceManager;
use Zend\Form\Form;
use Zend\Validator\AbstractValidator;

/**
 * Factory
 *
 * @category Swissbib_VuFind2
 * @package  MyResearch_Form
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Factory
{
    /**
     * Returns constructed AddressForm
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return Form
     */
    public static function getAddressForm(ServiceManager $sm)
    {
        AbstractValidator::setDefaultTranslator($sm->get('\\VuFind\\Translator'));

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('\\Swissbib\\MyResearch\\Form\\AddressForm');
        $form->add(new Csrf('security'));
        $form->add(
            [
            'name' => 'submit',
            'type'  => 'Submit',
            'attributes' => [
                'value' => 'Save',
            ],
            ]
        );

        return $form;
    }
}
