<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class EventRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('ev');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->andWhere('ev.user = :user')
            ->setParameter('user', $user);
    }
}