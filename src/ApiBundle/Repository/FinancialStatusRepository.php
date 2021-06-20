<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class FinancialStatusRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('fs');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->orWhere('z.user = :user')
            ->setParameter('user', $user)
            ->orWhere('z.userAssigned = :userAssigned')
            ->setParameter('userAssigned', $user);
    }

}