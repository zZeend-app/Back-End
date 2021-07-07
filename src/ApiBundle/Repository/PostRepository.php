<?php


namespace ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;

class PostRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('p');
    }
}