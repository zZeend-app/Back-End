<?php


namespace ApiBundle\DataFixtures\ORM;


use ApiBundle\Entity\PaymentType;
use ApiBundle\Entity\RenewalType;
use ApiBundle\Entity\ShareType;
use ApiBundle\Entity\ZzeendStatus;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadShareType implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $shareTypes = array(
            'Post',
            'Profile',
            'Chat',
            'File'
        );

        foreach ($shareTypes as $shareType) {

            $shareTypeEntity = new ShareType();
            $shareTypeEntity->setTitle($shareType);

            $manager->persist($shareTypeEntity);
        }

        $manager->flush();


    }
}