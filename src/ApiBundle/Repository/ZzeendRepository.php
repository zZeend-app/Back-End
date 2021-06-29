<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class ZzeendRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('z');
    }

    public function OrWhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->orWhere('z.user = :user')
            ->setParameter('user', $user)
            ->orWhere('z.userAssigned = :userAssigned')
            ->setParameter('userAssigned', $user);
    }

}