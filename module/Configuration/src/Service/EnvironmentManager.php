<?php

namespace Configuration\Service;

use Configuration\Entity\Environment;
use Configuration\Form\EnvironmentForm;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use Exception;
use User\Service\RbacManager;

class EnvironmentManager
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
     * EnvironmentManager constructor.
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
    public function getAllEnvironments(): array
    {
        return $this->entityManager->getRepository(Environment::class)->findBy([], ['id' => 'ASC']);
    }

    /**
     * @return array
     */
    public function getAllEnvironmentsFormattedList(): array
    {
        $environments = $this->getAllEnvironments();

        $environmentList = [];
        /** @var Environment $environment */
        foreach ($environments as $environment) {
            $environmentList[$environment->getId()] = $environment->getName();
        }

        return $environmentList;
    }

    /**
     * @param int $id
     * @return Environment|null
     */
    public function getEnvironment(int $id): ?Environment
    {
        /** @var Environment $environment */
        $environment = $this->entityManager->getRepository(Environment::class)->find($id);

        return $environment;
    }

    /**
     * @param array $data
     * @param Environment|null $environment
     * @throws Exception
     */
    public function saveEnvironment(array $data, Environment $environment = null)
    {
        $existingEnvironment = $this->entityManager->getRepository(Environment::class)
            ->findOneByName($data['name']);
        if ($existingEnvironment != null && $existingEnvironment != $environment) {
            throw new \Exception('Environment with such name already exists');
        }

        if (!$environment) {
            $environment = new Environment();
            $environment->setDateCreated(date('Y-m-d H:i:s'));
        }

        $environment->setName($data['name']);
        $environment->setDescription($data['description']);

        $this->entityManager->persist($environment);

        $this->entityManager->flush();
    }

    /**
     * @param Environment $environment
     */
    public function deleteEnvironment(Environment $environment)
    {
        $this->entityManager->remove($environment);
        $this->entityManager->flush();
    }

    /**
     * @param Environment|null $environment
     * @return EnvironmentForm
     */
    public function getEnvironmentForm(Environment $environment = null): EnvironmentForm
    {
        return new EnvironmentForm($this->entityManager, $environment);
    }

    public function createDefaultEnvironmentsIfNotExist()
    {
        $environment = $this->entityManager->getRepository(Environment::class)
            ->findOneBy([]);
        if ($environment != null) {
            return;
        }

        $defaultEnvironments = [
            'development' => 'Development environment',
            'staging' => 'Staging environment',
            'production' => 'Production environment',
        ];

        foreach ($defaultEnvironments as $name => $description) {
            $environment = new Environment();
            $environment->setName($name);
            $environment->setDescription($description);
            $environment->setDateCreated(date('Y-m-d H:i:s'));

            $this->entityManager->persist($environment);
        }

        $this->entityManager->flush();
    }
}
