<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class SocialNetworkRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('sn');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->orWhere('sn.user = :user')
            ->setParameter('user', $user);
    }

}