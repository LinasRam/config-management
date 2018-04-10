<?php

namespace Configuration\Service;

use Configuration\Entity\ConfigurationGroup;
use Doctrine\ORM\EntityManager;
use User\Service\RbacManager;

class ConfigurationManager
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
     * ConfigurationManager constructor.
     * @param EntityManager $entityManager
     * @param RbacManager $rbacManager
     */
    public function __construct(EntityManager $entityManager, RbacManager $rbacManager)
    {
        $this->entityManager = $entityManager;
        $this->rbacManager = $rbacManager;
    }

    public function getRootGroups(): array
    {
        return $this->entityManager->getRepository(ConfigurationGroup::class)->findBy(
            ['isRoot' => true],
            ['name' => 'ASC']
        );
    }

    /**
     * @param int $id
     * @return ConfigurationGroup|null
     */
    public function getConfigurationGroup(int $id): ?ConfigurationGroup
    {
        /** @var ConfigurationGroup $configurationGroup */
        $configurationGroup = $this->entityManager->getRepository(ConfigurationGroup::class)->find($id);

        return $configurationGroup;
    }
}
