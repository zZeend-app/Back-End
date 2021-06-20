<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class SocialNetworkTypeRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('snt');
    }

}