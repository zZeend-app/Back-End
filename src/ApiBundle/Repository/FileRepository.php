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

    public function WhereFileType(QueryBuilder $queryBuilder, $fileType){
        return $queryBuilder
            ->andWhere('f.fileType = :fileType')
            ->setParameter('fileType', $fileType);
    }

    public function OrderByJson(QueryBuilder $queryBuilder, $data)
    {
        foreach ($data as $order) {
            foreach ($order as $key => $value) {
                $queryBuilder = $queryBuilder->addOrderBy("f." . $key, $value);
            }
        }

        return $queryBuilder;
    }


}