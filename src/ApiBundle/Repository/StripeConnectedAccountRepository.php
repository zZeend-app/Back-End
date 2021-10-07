<?php

namespace ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class StripeConnectedAccountRepository extends \Doctrine\ORM\EntityRepository
{
    public function GetQueryBuilder()
    {
        return $queryBuilder = $this->createQueryBuilder('ch')
            ->leftJoin('ch.contact', 'contact')
            ->addSelect('contact');
    }




}
