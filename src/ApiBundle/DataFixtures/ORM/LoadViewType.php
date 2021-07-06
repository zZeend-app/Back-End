<?php

namespace ApiBundle\DataFixtures\ORM;

use ApiBundle\Entity\ViewType;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ApiBundle\Entity\NotificationType;

class LoadViewType implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $viewTitles = array(
            'Post',
            'Profile'
        );

        foreach ($viewTitles as $viewTitle) {

            $viewTypesEntity = new ViewType();
            $viewTypesEntity->setTitle($viewTitle);

            $manager->persist($viewTypesEntity);
        }

        $manager->flush();


    }

}

