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
          ->andWhere('u.jobTitle Like :keyword')
          ->setParameter('keyword', '%'.$keyword.'%');
    }

    public function WhereCountryLike(QueryBuilder $queryBuilder, $country){
        return $queryBuilder
            ->andWhere('u.country Like :country')
            ->setParameter('country', '%'.$country.'%');
    }

    public function WhereCityLike(QueryBuilder $queryBuilder, $city){
        return $queryBuilder
            ->andWhere('u.city Like :city')
            ->setParameter('city', '%'.$city.'%');
    }

    public function WhereAreaLike(QueryBuilder $queryBuilder, $area){
        return $queryBuilder

            ->andWhere('u.city Like :area')
            ->setParameter('area', '%'.$area.'%');

    }

    public function WhereSpecificAreaLike(QueryBuilder $queryBuilder, $specificArea){
        return $queryBuilder
            ->andWhere('u.address Like :address or u.city Like :city')
            ->setParameter('address', '%'.$specificArea.'%')
            ->setParameter('city', '%'.$specificArea.'%');
    }

    public function WhereIdNot(QueryBuilder $queryBuilder, $id){
        return $queryBuilder
            ->andWhere('u.id != :id')
            ->setParameter('id', $id);
    }

    public function WhereRoleNot(QueryBuilder $queryBuilder, $role){
        return $queryBuilder
            ->andWhere('u.roles NOT Like :role')
            ->setParameter('role', '%'.$role.'%');
    }

    public function WhereAccountIsEnabled(QueryBuilder $queryBuilder, $flag){
        return $queryBuilder
            ->andWhere('u.enabled = :flag')
            ->setParameter('flag', $flag);
    }

    public function WhereUserVisibility(QueryBuilder $queryBuilder, $flag)
    {
        return $queryBuilder
            ->andWhere('u.visibility = :flag')
            ->setParameter('flag', $flag)
            ->andWhere('u.mainVisibility = :flag')
            ->setParameter('flag', $flag);
    }
}