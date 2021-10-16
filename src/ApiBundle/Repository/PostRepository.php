<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class PostRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('pst');
    }

    public function OrderByJson(QueryBuilder $queryBuilder, $data){
        foreach($data as $order){
            foreach ($order as $key => $value){
                $queryBuilder = $queryBuilder->addOrderBy("pst." .$key ,$value);
            }
        }

        return $queryBuilder;
    }

    public function OrderByRand(QueryBuilder $queryBuilder){
        return $queryBuilder
            ->orderBy('RAND()');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->andWhere('pst.user = :user')
            ->setParameter('user', $user);
    }


    public function GetIdsRandomly(QueryBuilder $queryBuilder){
        return $queryBuilder
            ->select('count(pst.id)')->getQuery();
    }

    public function WehrePostAre(QueryBuilder $queryBuilder, $random_ids){
        return $queryBuilder
            ->where('pst.id IN (:ids)')
            ->setParameter('ids', $random_ids)
            ->setMaxResults(20);
    }




}