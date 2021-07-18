<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class ShareRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('sh');
    }


}