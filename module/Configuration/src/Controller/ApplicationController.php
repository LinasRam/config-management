<?php

namespace Configuration\Controller;

use Configuration\Service\ApplicationManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ApplicationController extends AbstractActionController
{
    /**
     * @var ApplicationManager
     */
    private $applicationManager;

    /**
     * ApplicationController constructor.
     * @param ApplicationManager $applicationManager
     */
    public function __construct(ApplicationManager $applicationManager)
    {
        $this->applicationManager = $applicationManager;
    }

    public function indexAction()
    {
        $applications = $this->applicationManager->getAllApplications();

        return new ViewModel(['applications' => $applications]);
    }

    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $application = $this->applicationManager->getApplication($id);

        if ($application == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return new ViewModel([
            'application' => $application
        ]);
    }

    public function addAction()
    {
        $form = $this->applicationManager->getApplicationForm();

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            $form->setData($data);

            if ($form->isValid()) {
                $data = $form->getData();

                $this->applicationManager->saveApplication($data);

                $this->flashMessenger()->addSuccessMessage('Added new application.');

                return $this->redirect()->toRoute('applications', ['action' => 'index']);
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $application = $this->applicationManager->getApplication($id);

        if ($application == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form = $this->applicationManager->getApplicationForm($application);

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            $form->setData($data);

            if ($form->isValid()) {
                $data = $form->getData();

                $this->applicationManager->saveApplication($data, $application);

                $this->flashMessenger()->addSuccessMessage('Updated the application.');

                return $this->redirect()->toRoute('applications', ['action' => 'index']);
            }
        } else {
            $form->setData(array(
                'name' => $application->getName(),
                'description' => $application->getDescription()
            ));
        }

        return new ViewModel([
            'form' => $form,
            'application' => $application
        ]);
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $application = $this->applicationManager->getApplication($id);

        if ($application == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $this->applicationManager->deleteApplication($application);

        $this->flashMessenger()->addSuccessMessage('Application deleted.');

        return $this->redirect()->toRoute('applications', ['action' => 'index']);
    }
}
