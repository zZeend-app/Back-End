<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class PaymentTypeRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('pt');
    }

}