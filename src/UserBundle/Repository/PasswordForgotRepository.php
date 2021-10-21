<?php


namespace UserBundle\Repository;

use Doctrine\ORM\QueryBuilder;


class PasswordForgotRepository extends \Doctrine\ORM\EntityRepository
{

    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('pf');
    }

    public function WhereUpateIsNotNull(QueryBuilder $queryBuilder, $codeGen)
    {
        return $queryBuilder
            ->andWhere("pf.codeGen = :codeGen")
            ->setParameter("codeGen", $codeGen)
            ->andWhere("pf.updatedAt is NULL");
    }



}