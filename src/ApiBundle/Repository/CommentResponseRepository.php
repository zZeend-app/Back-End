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


    public function WhereUser(QueryBuilder $queryBuilder, $user)
    {
        // return $queryBuilder
        //     ->orWhere('ch.mainUser = :mainUser')
        //     ->setParameter('mainUser', $user)
        //     ->orWhere('ch.secondUser = :secondUser')
        //     ->setParameter('secondUser', $user);
    }


}
