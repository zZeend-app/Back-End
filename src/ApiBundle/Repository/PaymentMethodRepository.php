<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class PaymentMethodRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('pm');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->andWhere('pm.user = :user')
            ->setParameter('user', $user);
    }

    public function WhereActive(QueryBuilder $queryBuilder, $flag){
        return $queryBuilder
            ->andWhere('pm.active = :active')
            ->setParameter('active', $flag);
    }

    public function OrderByMain(QueryBuilder $queryBuilder){
        return $queryBuilder
            ->addOrderBy('pm.main', 'DESC');
    }

    public function OrderById(QueryBuilder $queryBuilder){
        return $queryBuilder
            ->addOrderBy('pm.id', 'DESC');
    }

    public function GetCount(QueryBuilder $queryBuilder)
    {
        return $queryBuilder
            ->select('count(pm.id)');
    }

}