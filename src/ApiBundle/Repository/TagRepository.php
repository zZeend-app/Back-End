<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class TagRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('tg');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->andWhere('tg.user = :user')
            ->setParameter('user', $user);
    }

    public function WhereActiveIsTrue(QueryBuilder $queryBuilder, $flag){
        return $queryBuilder
            ->andWhere('tg.active = :active')
            ->setParameter('active', $flag);
    }

}