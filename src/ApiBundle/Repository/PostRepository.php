<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class PostRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('pst');
    }

    public function OrderById(QueryBuilder $queryBuilder){
        return $queryBuilder
            ->addOrderBy('pst.id', 'DESC');
    }

}