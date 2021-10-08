<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class ZzeendRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('z');
    }

    public function WhereZzeendId(QueryBuilder $queryBuilder, $zZeendId){
        return $queryBuilder
            ->andWhere('z.id = :id')
            ->setParameter('id', $zZeendId);
    }

    public function OrWhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->orWhere('z.user = :user')
            ->setParameter('user', $user)
            ->orWhere('z.userAssigned = :userAssigned')
            ->setParameter('userAssigned', $user);
    }

    public function WhereInProgress(QueryBuilder $queryBuilder, $status){
        return $queryBuilder
            ->andWhere('z.status = :status')
            ->setParameter('status', $status);
    }

    public function WhereCanceled(QueryBuilder $queryBuilder, $flag){
        return $queryBuilder
            ->andWhere('z.canceled = :flag')
            ->setParameter('flag', $flag);
    }


    public function OrderById(QueryBuilder $queryBuilder){
        return $queryBuilder
            ->addOrderBy('z.id', 'DESC');
    }

    public function GetCount(QueryBuilder $queryBuilder, $flag, $status)
    {
        return $queryBuilder
            ->select('count(z.id)')
            ->andWhere('z.canceled = :canceledFlag')
            ->setParameter('canceledFlag', $flag)
            ->andWhere('z.status = :status')
            ->setParameter('status', $status);
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->select('SUM(z.cost) as cost')
            ->andWhere('z.user = :user')
            ->setParameter('user', $user)
            ->andWhere('z.canceled = :canceled')
            ->setParameter('canceled', false)
            ->andWhere('z.payout is NULL')
            ->andWhere('z.transaction is NOT NULL');
    }

    public function GetTotalZzeendCreated(QueryBuilder $queryBuilder, $user)
    {
        return $queryBuilder
            ->select('count(z.id)')
            ->andWhere('z.user = :user')
            ->setParameter('user', $user);
    }


    public function GetTotalBalancePaid(QueryBuilder $queryBuilder, $user)
    {
        return $queryBuilder
            ->select('SUM(z.cost) as cost')
            ->andWhere('z.user = :user')
            ->setParameter('user', $user)
            ->andWhere('z.canceled = :canceledFlag')
            ->setParameter('canceledFlag', false)
            ->andWhere('z.done = :done')
            ->setParameter('done', true)
            ->andWhere('z.transaction is NOT NULL')
            ->andWhere('z.payout is NOT NULL');
    }


}