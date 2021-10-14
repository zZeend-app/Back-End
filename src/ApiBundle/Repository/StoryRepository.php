<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class StoryRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('str');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->andWhere('str.user = :user')
            ->setParameter('user', $user);
    }

    public function WhereDateIsGraterThan_24(QueryBuilder $queryBuilder){
        return $queryBuilder
            ->andWhere("str.createdAt > DATE_SUB(CURRENT_DATE(), 24, 'hour')");
    }

    public function OrderByJson(QueryBuilder $queryBuilder, $data)
    {
        foreach ($data as $order) {
            foreach ($order as $key => $value) {
                $queryBuilder = $queryBuilder->addOrderBy("str." . $key, $value);
            }
        }

        return $queryBuilder;
    }


}