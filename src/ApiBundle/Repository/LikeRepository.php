<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class LikeRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('l');
    }

    public function AndWhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->andWhere('l.user = :user')
            ->setParameter('user', $user);
    }

    public function AndWherePost(QueryBuilder $queryBuilder, $post){
        return $queryBuilder
            ->andWhere('l.post = :post')
            ->setParameter('post', $post);
    }
}