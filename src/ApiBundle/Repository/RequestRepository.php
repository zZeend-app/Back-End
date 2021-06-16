<?php

namespace ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class RequestRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder()
    {
        return $queryBuilder = $this->createQueryBuilder('r');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $sender, $receiver){
        return $queryBuilder
            ->andWhere('r.sender = :sender')
            ->setParameter('sender', $sender)
            ->andWhere('r.receiver = :receiver')
            ->setParameter('receiver', $receiver);
    }

    public function WhereSenderOrReceiver(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->orWhere('r.sender = :user')
            ->setParameter('user', $user)
            ->orWhere('r.receiver = :user')
            ->setParameter('user', $user);
    }

    public function OrderBy(QueryBuilder $queryBuilder){
        return $queryBuilder
            ->orderBy('r.id', 'DESC');
    }

}
