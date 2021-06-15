<?php

namespace ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class RequestRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder()
    {
        return $queryBuilder = $this->createQueryBuilder('r');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->andWhere('r.sender = :user')
            ->setParameter('user', $user);
    }

}
