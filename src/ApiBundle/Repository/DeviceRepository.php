<?php


namespace ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use UserBundle\Entity\User;

/**
 * DeviceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */


class DeviceRepository extends \Doctrine\ORM\EntityRepository
{

    public function GetQueryBuilder()
    {

        return $queryBuilder = $this->createQueryBuilder('d')
            ->leftJoin('d.user', 'user')
            ->addSelect('user');
    }

    public function WhereDeviceToken(QueryBuilder $queryBuilder, $token)
    {
        return $queryBuilder
            ->andWhere('d.token = :token')
            ->setParameter('token', $token);
    }

    public function WhereUser(QueryBuilder $queryBuilder, $user)
    {
        return $queryBuilder
            ->andWhere('d.user = :user')
            ->setParameter('user', $user instanceof User ? $user->getId() : $user);
    }



}