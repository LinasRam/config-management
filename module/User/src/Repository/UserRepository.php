<?php

namespace User\Repository;

use Doctrine\ORM\EntityRepository;
use User\Entity\Role;

class UserRepository extends EntityRepository
{
    public function findByRole(int $roleId)
    {
        return $this->createQueryBuilder('user')
            ->select()
            ->innerJoin(Role::class, 'role')
            ->andWhere('role.id = :roleId')
            ->setParameter('roleId', $roleId)
            ->getQuery()
            ->getResult()
        ;
    }
}
