<?php

namespace ApiBundle\DataFixtures\ORM;

use ApiBundle\Entity\IndexingType;
use ApiBundle\Entity\ViewType;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ApiBundle\Entity\NotificationType;

class LoadIndexingType implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $indexings = array(
            "Post",
            'Profile'
        );

        foreach ($indexings as $indexing) {

            $indexingTypeEntity = new IndexingType();
            $indexingTypeEntity->setTitle($indexing);

            $manager->persist($indexingTypeEntity);
        }

        $manager->flush();


    }

}

