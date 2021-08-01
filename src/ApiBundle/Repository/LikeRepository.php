<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class LikeRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder()
    {
        return $queryBuilder = $this->createQueryBuilder('l');
    }

    public function AndWhereUser(QueryBuilder $queryBuilder, $user)
    {
        return $queryBuilder
            ->andWhere('l.user = :user')
            ->setParameter('user', $user);
    }

    public function AndWherePost(QueryBuilder $queryBuilder, $post)
    {
        return $queryBuilder
            ->andWhere('l.post = :post')
            ->setParameter('post', $post);
    }

    public function GetLikesCount(QueryBuilder $queryBuilder, $post)
    {
        return $queryBuilder
            ->select('count(l.id)')
            ->andWhere('l.post = :post')
            ->setParameter('post', $post)
            ->andWhere('l.active = :active')
            ->setParameter('active', true);
    }

    public function WhereUserLikesPost(QueryBuilder $queryBuilder, $user, $post)
    {
        return $queryBuilder
            ->andWhere('l.post = :post')
            ->setParameter('post', $post)
            ->andWhere('l.user = :user')
            ->setParameter('user', $user);
    }
}