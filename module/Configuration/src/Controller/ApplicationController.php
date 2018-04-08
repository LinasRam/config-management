<?php

namespace Configuration\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ApplicationController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}
