<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class IndexingRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('Ind');
    }

}