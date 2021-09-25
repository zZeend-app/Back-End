<?php

namespace ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class RoomTypeRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder()
    {
        return $queryBuilder = $this->createQueryBuilder('ch')
            ->leftJoin('ch.contact', 'contact')
            ->addSelect('contact');
    }


    public function WhereUser(QueryBuilder $queryBuilder, $user)
    {
        return $queryBuilder
            ->orWhere('ch.mainUser = :mainUser')
            ->setParameter('mainUser', $user)
            ->orWhere('ch.secondUser = :secondUser')
            ->setParameter('secondUser', $user);
    }

    public function WhereContact(QueryBuilder $queryBuilder, $contact)
    {
        return $queryBuilder
            ->where('ch.contact = :contact')
            ->setParameter('contact', $contact);
    }

    public function GroupBy(QueryBuilder $queryBuilder, $columnName)
    {
        return $queryBuilder
            ->groupBy('ch.' . $columnName);
    }

    public function OrderBy(QueryBuilder $queryBuilder, $columnName)
    {
        return $queryBuilder
            ->addOrderBy('ch.' . $columnName, 'DESC');
    }

    public function OrderByJson(QueryBuilder $queryBuilder, $data)
    {
        foreach ($data as $order) {
            foreach ($order as $key => $value) {
                $queryBuilder = $queryBuilder->addOrderBy("ch." . $key, $value);
            }
        }

        return $queryBuilder;
    }

    public function GetCount(QueryBuilder $queryBuilder, $viewedFlag, $user)
    {
        return $queryBuilder
            ->select('count(contact)')
            ->andWhere('ch.viewed = :viewedFlag')
            ->setParameter('viewedFlag', $viewedFlag)
            ->andWhere('contact.mainUser = :user or contact.secondUser = :user ')
            ->setParameter('user', $user)
            ->andWhere('ch.sender != :user')
            ->setParameter('user', $user)
            ->groupBy('contact')
        ;
    }

    public function GetCountForEachChatContact(QueryBuilder $queryBuilder, $contact, $viewedFlag, $user)
    {
        return $queryBuilder
            ->select('count(ch.id)')
            ->andWhere('ch.viewed = :viewedFlag')
            ->setParameter('viewedFlag', $viewedFlag)
            ->andWhere('contact = :contact')
            ->setParameter('contact', $contact)
            ->andWhere('ch.sender != :user')
            ->setParameter('user', $user);
    }


}
