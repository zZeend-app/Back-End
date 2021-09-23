<?php

namespace ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class CommentRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder()
    {
        return $queryBuilder = $this->createQueryBuilder('cm')
            ->leftJoin('cm.user', 'user')
            ->addSelect('user')
            ->leftJoin('cm.post', 'post')
            ->addSelect('post');
    }


    public function WherePost(QueryBuilder $queryBuilder, $post)
    {
        return $queryBuilder
            ->where('post = :post')
            ->setParameter('post', $post);
    }

    public function OrderByJson(QueryBuilder $queryBuilder, $data)
    {
        foreach ($data as $order) {
            foreach ($order as $key => $value) {
                $queryBuilder = $queryBuilder->addOrderBy("cm." . $key, $value);
            }
        }

        return $queryBuilder;
    }

    public function GetCount(QueryBuilder $queryBuilder)
    {
        return $queryBuilder
            ->select('count(cm.id)');
    }


}
