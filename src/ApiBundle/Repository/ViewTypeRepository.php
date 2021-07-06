<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class ViewTypeRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('vt');
    }

}