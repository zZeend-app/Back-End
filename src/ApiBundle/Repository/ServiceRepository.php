<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class ServiceRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('s');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->andWhere('s.user = :user')
            ->setParameter('user', $user)
            ->addOrderBy('s.id', 'DESC');;
    }
}