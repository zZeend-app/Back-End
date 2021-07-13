<?php

namespace ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class ChatRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder()
    {
        return $queryBuilder = $this->createQueryBuilder('ch');
    }


    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->orWhere('ch.mainUser = :mainUser')
            ->setParameter('mainUser', $user)
            ->orWhere('ch.secondUser = :secondUser')
            ->setParameter('secondUser', $user);
    }

    public function WhereContact(QueryBuilder $queryBuilder, $contact){
        return $queryBuilder
            ->where('ch.contact = :contact')
            ->setParameter('contact', $contact);
    }

    public function GroupBy(QueryBuilder $queryBuilder, $columnName){
        return $queryBuilder
            ->groupBy('ch.'.$columnName);
    }

    public function OrderBy(QueryBuilder $queryBuilder, $columnName){
        return $queryBuilder
            ->addOrderBy('ch.'.$columnName, 'DESC');
    }

    public function OrderByJson(QueryBuilder $queryBuilder, $data){
        foreach($data as $order){
            foreach ($order as $key => $value){
                $queryBuilder = $queryBuilder->addOrderBy("ch." .$key ,$value);
            }
        }

        return $queryBuilder;
    }

}
