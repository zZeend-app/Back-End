<?php


namespace LiveBundle\DataFixtures\ORM;


use LiveBundle\Entity\RoomTargetType;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadRoomTargetType implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $titles = array(
            "Public",
            "Contact",
            "Public & contact"
        );

        foreach ($titles as $title) {

            $roomDestinationTypeEntity = new RoomTargetType();
            $roomDestinationTypeEntity->setTitle($viewTitle);

            $manager->persist($roomDestinationTypeEntity);
        }

        $manager->flush();


    }

      
}