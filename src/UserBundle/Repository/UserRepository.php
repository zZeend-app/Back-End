<?php


namespace UserBundle\Repository;

use Doctrine\ORM\QueryBuilder;


class UserRepository extends \Doctrine\ORM\EntityRepository
{

    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('u');
    }
    public function FindByEmail($email){
        $queryBuilder = $this->createQueryBuilder('u');

        return $queryBuilder
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function FindByEmailAndPassword($email, $password){
        $queryBuilder = $this->createQueryBuilder('u');

        return $queryBuilder
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->andWhere('u.password = :password')
            ->setParameter('password', $password)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function WhereKeywordLike(QueryBuilder $queryBuilder, $keyword){
      return $queryBuilder
          ->andWhere('jobTitle Like :keyword')
          ->setParameter('keyword', $keyword)
          ->andWhere('jobDescription Like :keyword')
          ->setParameter('keyword', $keyword);
    }

    public function WhereCountryLike(QueryBuilder $queryBuilder, $country){
        return $queryBuilder
            ->andWhere('country Like :country')
            ->setParameter('country', $country);
    }

    public function WhereCityLike(QueryBuilder $queryBuilder, $city){
        return $queryBuilder
            ->andWhere('city Like :city')
            ->setParameter('city', $city);
    }

    public function WhereAreaLike(QueryBuilder $queryBuilder, $address){
        return $queryBuilder
            ->andWhere('address Like :address')
            ->setParameter('address', $address);
    }


}