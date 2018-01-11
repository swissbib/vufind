<?php
/**
 * Created by IntelliJ IDEA.
 * User: boehm
 * Date: 04.01.18
 * Time: 10:34
 */

namespace Swissbib\Controller;


use Zend\ServiceManager\ServiceLocatorInterface;

class DetailPageController extends KnowledgeCardController
{
    /**
     * DetailPageController constructor.
     */
    public function __construct(ServiceLocatorInterface $sm)
    {
        parent::__construct($sm);
    }

    /**
     * /Page/Detail/Person/:id
     * @return \Zend\View\Model\ViewModel
     */
    public function personAction()
    {
        return parent::personAction();
    }

    /**
     * /Page/Detail/Subject/:id
     * @return \Zend\View\Model\ViewModel
     */
    public function subjectAction()
    {
        return parent::subjectAction();
    }
}