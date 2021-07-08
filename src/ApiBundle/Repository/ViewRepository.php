<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class ViewRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('v');
    }

    public function AndWhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->andWhere('v.user = :user')
            ->setParameter('user', $user);
    }

    public function AndWhereRelatedId(QueryBuilder $queryBuilder, $relatedId){
        return $queryBuilder
            ->andWhere('v.relatedId = :relatedId')
            ->setParameter('relatedId', $relatedId);
    }

    public function AndWhereViewType(QueryBuilder $queryBuilder, $viewType){
        return $queryBuilder
            ->andWhere('v.viewType = :viewType')
            ->setParameter('viewType', $viewType);
    }

    public function GetViewsCount(QueryBuilder $queryBuilder, $related_id){
        return $queryBuilder
            ->select('count(v.id)')
            ->andWhere('v.relatedId = :relatedId')
            ->setParameter('relatedId', $related_id);
    }


}