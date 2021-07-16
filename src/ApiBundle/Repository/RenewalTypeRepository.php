<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class RenewalTypeRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('rnt');
    }


}