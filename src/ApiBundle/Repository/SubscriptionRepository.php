<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class SubscriptionRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('sb');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->andWhere('sb.user = :user')
            ->setParameter('user', $user);
    }


}