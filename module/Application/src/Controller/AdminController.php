<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
    public function listAction()
    {
        $view = new ViewModel();
        return $view;
    }

    public function editAction()
    {
        $view = new ViewModel();
        return $view;
    }
}
