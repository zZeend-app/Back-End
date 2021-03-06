<?php

namespace ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class RequestRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder()
    {
        return $queryBuilder = $this->createQueryBuilder('r');
    }

    public function OrWhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->orWhere('r.sender = :sender')
            ->setParameter('sender', $user)
            ->orWhere('r.receiver = :receiver')
            ->setParameter('receiver', $user);
    }

    public function _OrWhereUser(QueryBuilder $queryBuilder, $sender){
        return $queryBuilder
            ->orWhere('r.sender = :sender')
            ->setParameter('sender', $sender)
            ->orWhere('r.receiver = :receiver')
            ->setParameter('receiver', $sender);
    }

    public function WhereSenderOrReceiver(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->orWhere('r.sender = :user')
            ->setParameter('user', $user)
            ->orWhere('r.receiver = :user')
            ->setParameter('user', $user);
    }

    public function AndWhereSender(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->andWhere('r.sender = :user')
            ->setParameter('user', $user);
    }

    public function AndWhereReceiver(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->orWhere('r.receiver = :user')
            ->setParameter('user', $user);
    }

    public function WhereStatement(QueryBuilder $queryBuilder, $relatedId, $user){
        return $queryBuilder
            ->andwhere('r.id = :id')
            ->setParameter('id', $relatedId)
            ->andWhere('r.sender = :user')
            ->setParameter('user', $user)
            ->orWhere('r.id = :id')
            ->setParameter('id', $relatedId)
            ->andWhere('r.receiver = :user')
            ->setParameter('user', $user);
    }

    public function OrWhereId(QueryBuilder $queryBuilder, $relatedId){
        return $queryBuilder
            ->orWhere('r.id = :id')
            ->setParameter('id', $relatedId);
    }

    public function OrderBy(QueryBuilder $queryBuilder){
        return $queryBuilder
            ->addOrderBy('r.id', 'DESC');
    }

    public function GetCount(QueryBuilder $queryBuilder)
    {
        return $queryBuilder
            ->select('count(r.id)');
    }

    public function RequestStateIsNull(QueryBuilder $queryBuilder){
        return $queryBuilder
            ->andWhere('r.accepted is NULL')
            ->andWhere('r.rejected is NULL');
    }

    public function WhereUserReceiver(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->andWhere('r.receiver = :receiver')
            ->setParameter('receiver', $user);
    }

}
