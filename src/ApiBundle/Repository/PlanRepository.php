<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class PlanRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('pl');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->andWhere('pl.user = :user')
            ->setParameter('user', $user);
    }


}