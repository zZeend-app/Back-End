<?php

namespace ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class ContactRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder()
    {
        return $queryBuilder = $this->createQueryBuilder('c');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->orWhere('c.mainUser = :mainUser')
            ->setParameter('mainUser', $user)
            ->orWhere('c.secondUser = :secondUser')
            ->setParameter('secondUser', $user)
            ->addOrderBy('c.secondUser', 'DESC');
    }

    public function WhereContactId(QueryBuilder $queryBuilder, $contactId){
        return $queryBuilder
            ->andWhere('c.id = :contactId')
            ->setParameter('contactId', $contactId);
    }

}
