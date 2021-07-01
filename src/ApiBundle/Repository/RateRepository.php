<?php

namespace ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class RateRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder()
    {
        return $queryBuilder = $this->createQueryBuilder('rt');
    }

    public function WhereRatedUser(QueryBuilder $queryBuilder, $ratedUser){
        return $queryBuilder
            ->andWhere('rt.ratedUser = :ratedUser')
            ->setParameter('ratedUser', $ratedUser);
    }

    public function GetRatesAvg(QueryBuilder $queryBuilder, $ratedUser){
        return $queryBuilder
            ->select('avg(rt.stars)')
            ->andWhere('rt.ratedUser = :ratedUser')
            ->setParameter('ratedUser', $ratedUser);
    }


}
