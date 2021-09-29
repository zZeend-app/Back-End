<?php


namespace LiveBundle\DataFixtures\ORM;


use LiveBundle\Entity\RoomType;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadRoomType implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $titles = array(
            "Presentation",
            "Meeting",
            "Video call",
            "Single live"
        );

        foreach ($titles as $title) {

            $roomTypeEntity = new RoomType();
            $roomTypeEntity->setTitle($viewTitle);

            $manager->persist($roomTypeEntity);
        }

        $manager->flush();


    }

      
}