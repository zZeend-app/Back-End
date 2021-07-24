<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class IndexingTypeRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('Ind_tp');
    }

}