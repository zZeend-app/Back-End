<?php

namespace ApiBundle\DataFixtures\ORM;

use ApiBundle\Entity\VersionControl;
use ApiBundle\Entity\ViewType;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ApiBundle\Entity\NotificationType;

class LoadAppVersion implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {


        $versionControl = new VersionControl();
        $versionControl->setVersion('1.0.0');

        $manager->persist($versionControl);

        $manager->flush();


    }

}

