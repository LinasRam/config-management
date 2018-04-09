<?php

namespace Configuration\Controller;

use Configuration\Entity\Environment;
use Configuration\Service\ApplicationManager;
use Configuration\Service\EnvironmentManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ApplicationController extends AbstractActionController
{
    /**
     * @var ApplicationManager
     */
    private $applicationManager;

    /**
     * @var EnvironmentManager
     */
    private $environmentManager;

    /**
     * ApplicationController constructor.
     * @param ApplicationManager $applicationManager
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(ApplicationManager $applicationManager, EnvironmentManager $environmentManager)
    {
        $this->applicationManager = $applicationManager;
        $this->environmentManager = $environmentManager;
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

        $environmentList = $this->environmentManager->getAllEnvironmentsFormattedList();
        $form->get('environments')->setValueOptions($environmentList);

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

        $environmentList = $this->environmentManager->getAllEnvironmentsFormattedList();
        $form->get('environments')->setValueOptions($environmentList);

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
            $environmentIds = [];
            /** @var Environment $environment */
            foreach ($application->getEnvironments() as $environment) {
                $environmentIds[] = $environment->getId();
            }

            $form->setData(array(
                'name' => $application->getName(),
                'description' => $application->getDescription(),
                'environments' => $environmentIds,
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
