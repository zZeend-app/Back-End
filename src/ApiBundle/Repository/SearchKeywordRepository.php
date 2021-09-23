<?php

namespace ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class SearchKeywordRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder()
    {
        return $queryBuilder = $this->createQueryBuilder('skw')
            ->leftJoin('skw.user', 'user')
            ->addSelect('user');
    }


    public function WhereUser(QueryBuilder $queryBuilder, $user)
    {
        return $queryBuilder
            ->where('user = :user')
            ->setParameter('user', $user);
    }

    public function WhereKeyword(QueryBuilder $queryBuilder, $keyword)
    {
        return $queryBuilder
            ->andWhere('skw.keyword Like :keyword')
            ->setParameter('keyword', '%'.$keyword.'%');
    }

    public function OrderByJson(QueryBuilder $queryBuilder, $data)
    {
        foreach ($data as $order) {
            foreach ($order as $key => $value) {
                $queryBuilder = $queryBuilder->addOrderBy("skw." . $key, $value);
            }
        }

        return $queryBuilder;
    }

    public function GetCount(QueryBuilder $queryBuilder)
    {
        return $queryBuilder
            ->select('count(skw.id)');
    }


}
