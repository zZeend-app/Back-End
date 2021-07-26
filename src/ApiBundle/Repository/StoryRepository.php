<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class StoryRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('str');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->andWhere('str.user = :user')
            ->setParameter('user', $user);
    }

}