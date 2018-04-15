<?php

namespace Application\Service;

use Doctrine\ORM\EntityManager;
use Zend\Authentication\AuthenticationService;
use Zend\Permissions\Rbac\Rbac;
use User\Entity\User;

/**
 * This service is used for invoking user-defined RBAC dynamic assertions.
 */
class RbacAssertionManager
{
    /**
     * Entity manager.
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Auth service.
     * @var AuthenticationService
     */
    private $authService;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager, $authService)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
    }

    /**
     * This method is used for dynamic assertions.
     */
    public function assert(Rbac $rbac, $permission, $params)
    {
        $currentUser = $this->entityManager->getRepository(User::class)
            ->findOneByEmail($this->authService->getIdentity());

        if ($params['user']->getId() == $currentUser->getId()) {
            return true;
        }

        return false;
    }
}



