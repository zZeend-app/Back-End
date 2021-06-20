<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class NotificationRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('n');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->orWhere('z.user = :user')
            ->setParameter('user', $user)
            ->orWhere('z.userAssigned = :userAssigned')
            ->setParameter('userAssigned', $user);
    }

}