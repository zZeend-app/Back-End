<?php

namespace LiveBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class RoomRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder()
    {
        return $queryBuilder = $this->createQueryBuilder('rm')
            ->leftJoin('rm.moderator', 'moderator')
            ->addSelect('moderator');
    }

}
