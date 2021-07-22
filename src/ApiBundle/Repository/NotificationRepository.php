<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class NotificationRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('n');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->orWhere('z.user = :user')
            ->setParameter('user', $user)
            ->orWhere('z.userAssigned = :userAssigned')
            ->setParameter('userAssigned', $user);
    }

    public function InnerJoin(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->join('n.related_id', 'related_id')
            ->addSelect('related_id')
            ->orWhere('z.userAssigned = :userAssigned')
            ->setParameter('userAssigned', $user);
    }

    public function GetCount(QueryBuilder $queryBuilder, $user)
    {
        return $queryBuilder
            ->select('count(n.id)')
            ->andWhere('n.user = :user')
            ->setParameter('user', $user)
            ->andWhere('n.viewed = :flag')
            ->setParameter('flag', false);
    }



}