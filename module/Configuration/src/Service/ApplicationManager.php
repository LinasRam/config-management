<?php

namespace Configuration\Service;

use Configuration\Entity\Application;
use Configuration\Entity\ConfigurationGroup;
use Configuration\Entity\Environment;
use Configuration\Form\ApplicationForm;
use Doctrine\ORM\EntityManager;
use Exception;
use User\Entity\Permission;
use User\Entity\Role;
use User\Entity\User;
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
     * @var ConfigurationGroupManager
     */
    private $configGroupManager;

    /**
     * ApplicationManager constructor.
     * @param EntityManager $entityManager
     * @param RbacManager $rbacManager
     * @param ConfigurationGroupManager $configurationGroupManager
     */
    public function __construct(
        EntityManager $entityManager,
        RbacManager $rbacManager,
        ConfigurationGroupManager $configurationGroupManager
    ) {
        $this->entityManager = $entityManager;
        $this->rbacManager = $rbacManager;
        $this->configGroupManager = $configurationGroupManager;
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

        $this->createRootGroups($application, $data['environments']);
        $this->createPermissions($application, $data['environments']);
        $this->assignEnvironments($application, $data['environments']);

        $this->entityManager->persist($application);

        $this->entityManager->flush();

        // Reload RBAC container.
        $this->rbacManager->init(true);
    }

    /**
     * @param Application $application
     * @param array $environmentIds
     * @throws Exception
     */
    private function assignEnvironments(Application $application, array $environmentIds)
    {
        $application->getEnvironments()->clear();

        foreach ($environmentIds as $environmentId) {
            /** @var Environment $environment */
            $environment = $this->entityManager->getRepository(Environment::class)
                ->find($environmentId);
            if ($environment == null) {
                throw new Exception('Not found environment by ID');
            }

            $application->addEnvironment($environment);
        }
    }

    /**
     * @param Application $application
     * @param array $environmentIds
     */
    private function createPermissions(Application $application, array $environmentIds)
    {
        $environments = $this->entityManager->getRepository(Environment::class)->findAll();

        /** @var Environment $environment */
        foreach ($environments as $environment) {
            $permissionName = 'manage.' . $application->getName() . '.' . $environment->getName();
            /** @var Permission $permission */
            $permission = $this->entityManager->getRepository(Permission::class)
                ->findOneByName($permissionName);

            if (!in_array($environment->getId(), $environmentIds)) {
                if ($permission) {
                    $this->entityManager->remove($permission);
                }
            } else {
                if (!$permission) {
                    $permission = new Permission();
                    $permission->setName($permissionName);
                    $permission->setDescription(
                        'Manage ' . $application->getName() . ' ' . $environment->getName()
                        . ' environment configuration'
                    );
                    $permission->setDateCreated(date('Y-m-d H:i:s'));

                    $this->entityManager->persist($permission);

                    /** @var Role $rootRole */
                    $rootRole = $this->entityManager->getRepository(Role::class)->find(1);
                    $rootRole->getPermissions()->add($permission);
                }
            }
        }
    }

    /**
     * @param Application $application
     * @param array $environmentIds
     * @throws Exception
     */
    private function createRootGroups(Application $application, array $environmentIds)
    {
        $existingEnvironmentIds = [];
        /** @var Environment $existingEnvironment */
        foreach ($application->getEnvironments() as $existingEnvironment) {
            $existingEnvironmentIds[] = $existingEnvironment->getId();
        }

        foreach ($environmentIds as $environmentId) {
            if (!in_array($environmentId, $existingEnvironmentIds)) {
                /** @var Environment $environment */
                $environment = $this->entityManager->getRepository(Environment::class)
                    ->find($environmentId);
                if ($environment == null) {
                    throw new Exception('Not found environment by ID');
                }

                $configurationGroup = $this->configGroupManager->getRootConfigurationGroup(
                    $application->getName(),
                    $environment->getName()
                );

                if (!$configurationGroup) {
                    $configurationGroup = new ConfigurationGroup();
                    $configurationGroup->setApplication($application);
                    $configurationGroup->setEnvironment($environment);
                    $configurationGroup->setName($application->getName() . ' ' . $environment->getName());
                    $configurationGroup->setIsRoot(true);
                }

                $this->entityManager->persist($configurationGroup);
            }
        }

        $environments = $this->entityManager->getRepository(Environment::class)->findAll();
        foreach ($environments as $environment) {
            if (!in_array($environment->getId(), $environmentIds)) {
                $configurationGroup = $this->configGroupManager->getRootConfigurationGroup(
                    $application->getName(),
                    $environment->getName()
                );
                if ($configurationGroup) {
                    $this->entityManager->remove($configurationGroup);
                }
            }
        }
    }

    /**
     * @param Application $application
     */
    public function deleteApplication(Application $application)
    {
        $this->deleteRoles($application);

        $this->entityManager->remove($application);
        $this->entityManager->flush();
    }

    /**
     * @param Application $application
     */
    private function deleteRoles(Application $application)
    {
        $environments = $this->entityManager->getRepository(Environment::class)->findAll();

        /** @var Environment $environment */
        foreach ($environments as $environment) {
            $roleName = $application->getName() . ' ' . $environment->getName() . ' manager';
            /** @var Role $role */
            $role = $this->entityManager->getRepository(Role::class)->findOneByName($roleName);

            if ($role) {
                $usersWithRole = $this->entityManager->getRepository(User::class)->findByRole($role->getId());
                /** @var User $user */
                foreach ($usersWithRole as $user) {
                    $user->getRoles()->removeElement($role);
                }
                $this->entityManager->remove($role);
            }

            $permissionName = 'manage.' . $application->getName() . '.' . $environment->getName();
            $permission = $this->entityManager->getRepository(Permission::class)->findOneByName($permissionName);
            if ($permission) {
                $this->entityManager->remove($permission);
            }
        }
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
