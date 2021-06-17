<?php

namespace ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class ChatRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder()
    {
        return $queryBuilder = $this->createQueryBuilder('ch');
    }


    public function WhereContact(QueryBuilder $queryBuilder, $contact){
        return $queryBuilder
            ->andWhere('ch.contact = :contact')
            ->setParameter('contact', $contact);
    }

}
