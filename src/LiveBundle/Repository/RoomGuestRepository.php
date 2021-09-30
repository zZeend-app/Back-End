<?php

namespace LiveBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class RoomGuestRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder()
    {
        return $queryBuilder = $this->createQueryBuilder('gu')
            ->leftJoin('gu.guest', 'guest')
            ->addSelect('guest')
            ->leftJoin('gu.room', 'room')
            ->addSelect('room');
    }


    public function WhereUser(QueryBuilder $queryBuilder, $user){
        return $queryBuilder
            ->andWhere('guest = :guest')
            ->setParameter('guest', $user);
    }

    public function WhereRoom(QueryBuilder $queryBuilder, $room){
        return $queryBuilder
            ->andWhere('room = :room')
            ->setParameter('room', $room);
    }


}
