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
            ->andWhere('sn.user = :user')
            ->setParameter('user', $user);
    }

    public function WhereLinkNotEmpty(QueryBuilder $queryBuilder){
        return $queryBuilder
            ->andWhere('sn.link != :link')
            ->setParameter('link', "");
    }

}