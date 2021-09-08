<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class FileRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('f');
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->andWhere('f.user = :user')
            ->setParameter('user', $user);
    }


}