<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class ZzeendPointRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('zp');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->select('count(zp.id)')
            ->andWhere('zp.user = :user')
            ->setParameter('user', $user);
    }


}