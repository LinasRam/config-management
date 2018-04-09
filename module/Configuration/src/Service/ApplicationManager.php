<?php

namespace Configuration\Service;

use Configuration\Entity\Application;
use Configuration\Form\ApplicationForm;
use Doctrine\ORM\EntityManager;
use Exception;
use User\Service\RbacManager;

class ApplicationManager
{
    /**
     * Doctrine entity manager.
     * @var EntityManager
     */
    private $entityManager;

    /**
     * RBAC manager.
     * @var RbacManager
     */
    private $rbacManager;

    /**
     * ApplicationManager constructor.
     * @param EntityManager $entityManager
     * @param RbacManager $rbacManager
     */
    public function __construct(EntityManager $entityManager, RbacManager $rbacManager)
    {
        $this->entityManager = $entityManager;
        $this->rbacManager = $rbacManager;
    }

    /**
     * @return array
     */
    public function getAllApplications(): array
    {
        return $this->entityManager->getRepository(Application::class)->findBy([], ['id' => 'ASC']);
    }

    /**
     * @param int $id
     * @return Application|null
     */
    public function getApplication(int $id): ?Application
    {
        /** @var Application $application */
        $application = $this->entityManager->getRepository(Application::class)->find($id);

        return $application;
    }

    /**
     * @param array $data
     * @param Application|null $application
     * @throws Exception
     */
    public function saveApplication(array $data, Application $application = null)
    {
        $existingApplication = $this->entityManager->getRepository(Application::class)
            ->findOneByName($data['name']);
        if ($existingApplication != null && $existingApplication != $application) {
            throw new \Exception('Application with such name already exists');
        }

        if (!$application) {
            $application = new Application();
            $application->setDateCreated(date('Y-m-d H:i:s'));
        }

        $application->setName($data['name']);
        $application->setDescription($data['description']);

        $this->entityManager->persist($application);

        $this->entityManager->flush();
    }

    /**
     * @param Application $application
     */
    public function deleteApplication(Application $application)
    {
        $this->entityManager->remove($application);
        $this->entityManager->flush();
    }

    /**
     * @param Application|null $application
     * @return ApplicationForm
     */
    public function getApplicationForm(Application $application = null): ApplicationForm
    {
        return new ApplicationForm($this->entityManager, $application);
    }
}
