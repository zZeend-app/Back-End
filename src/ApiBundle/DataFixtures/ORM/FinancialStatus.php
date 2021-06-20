<?php


namespace ApiBundle\DataFixtures\ORM;


use ApiBundle\Entity\Finance;
use ApiBundle\Entity\ZzeendStatus;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FinancialStatus implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $financialsStatuses = array(
            'Gain',
            'Retrieve'
        );

        foreach ($financialsStatuses as $financialsStatus) {

            $financialsStatusEntity = new \ApiBundle\Entity\FinancialStatus();
            $financialsStatusEntity->setTitle($financialsStatus);

            $manager->persist($financialsStatusEntity);
        }

        $manager->flush();

    }
}