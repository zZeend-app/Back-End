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

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->andWhere('pst.user = :user')
            ->setParameter('user', $user);
    }


}