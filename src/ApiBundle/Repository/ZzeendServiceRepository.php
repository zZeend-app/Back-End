<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class ZzeendServiceRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('zs');
    }

    public function WhereZzeendCostIsBetween(QueryBuilder $queryBuilder, $zZeendCost){
        return $queryBuilder
            ->andWhere('zs.startFees <= :zZeendCost')
            ->setParameter('zZeendCost', $zZeendCost)
            ->andWhere('zs.endFees >= :zZeendCost')
            ->setParameter('zZeendCost', $zZeendCost);
    }

    public function WhereZzeendCostIsOverThan5000(QueryBuilder $queryBuilder, $zZeendCost){
        return $queryBuilder
            ->andWhere('zs.startFees >= :zZeendCost')
            ->setParameter('zZeendCost', 5000);
    }


}