<?php

namespace ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class CommentResponseRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder()
    {
        return $queryBuilder = $this->createQueryBuilder('cmrsp')
            ->leftJoin('cmrsp.user', 'user')
            ->addSelect('user')
            ->leftJoin('cmrsp.comment', 'comment')
            ->addSelect('comment');
    }


    public function WhereComment(QueryBuilder $queryBuilder, $comment)
    {
        return $queryBuilder
            ->where('comment = :comment')
            ->setParameter('comment', $comment);
    }

    public function OrderByJson(QueryBuilder $queryBuilder, $data)
    {
        foreach ($data as $order) {
            foreach ($order as $key => $value) {
                $queryBuilder = $queryBuilder->addOrderBy("cmrsp." . $key, $value);
            }
        }

        return $queryBuilder;
    }




}
