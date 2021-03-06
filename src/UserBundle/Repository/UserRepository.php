<?php


namespace UserBundle\Repository;

use Doctrine\ORM\QueryBuilder;


class UserRepository extends \Doctrine\ORM\EntityRepository
{

    public function GetQueryBuilder(){
        return $queryBuilder = $this->createQueryBuilder('u')
            ->leftJoin('u.services', 'services')
            ->addSelect('services');
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
          ->orWhere('u.jobTitle Like :keyword')
          ->setParameter('keyword', '%'.$keyword.'%')
          ->orWhere('u.jobDescription Like :keyword')
          ->setParameter('keyword', '%'.$keyword.'%')
          ->orWhere('services.service Like :keyword')
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

    public function WhereAdministrativeAreaOrSubAdministrativeArea(QueryBuilder $queryBuilder, $specificArea){
        return $queryBuilder
            ->orWhere('u.administrativeArea Like :administrativeArea or u.subAdministrativeArea Like :subAdministrativeArea')
            ->setParameter('administrativeArea', '%'.$specificArea.'%')
            ->setParameter('subAdministrativeArea', '%'.$specificArea.'%');
    }

    public function WhereSubLocality(QueryBuilder $queryBuilder, $specificArea){
        return $queryBuilder
            ->orWhere('u.subLocality Like :subLocality')
            ->setParameter('subLocality', '%'.$specificArea.'%');
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

    public function OrderByZzeendScore(QueryBuilder $queryBuilder, $zZeendScore){
        if($zZeendScore == 0) {
            return $queryBuilder
                ->addOrderBy('u.zZeendScore', 'DESC');
        }else{
            return $queryBuilder
                ->addOrderBy('u.zZeendScore', 'ASC');
        }
    }

}