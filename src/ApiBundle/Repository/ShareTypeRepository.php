<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class ShareTypeRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('sht');
    }


}