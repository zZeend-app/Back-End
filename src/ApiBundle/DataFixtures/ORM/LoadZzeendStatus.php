<?php


namespace ApiBundle\DataFixtures\ORM;


use ApiBundle\Entity\NotificationType;
use ApiBundle\Entity\ZzeendStatus;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadZzeendStatus implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $ZzeendStatuses = array(
            'In Progress',
            'Uncompleted',
            'Completed',
        );

        foreach ($ZzeendStatuses as $ZzeendStatus) {

            $ZzeendStatusEntity = new ZzeendStatus();
            $ZzeendStatusEntity->setTitle($ZzeendStatus);

            $manager->persist($ZzeendStatusEntity);
        }

        $manager->flush();


    }
}