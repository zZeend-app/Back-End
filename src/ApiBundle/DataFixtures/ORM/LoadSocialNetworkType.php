<?php


namespace ApiBundle\DataFixtures\ORM;


use ApiBundle\Entity\NotificationType;
use ApiBundle\Entity\SocialNetworkType;
use ApiBundle\Entity\ZzeendStatus;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadSocialNetworkType implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $socialNetworks = array(
            'Facebook',
            'Twitter',
            'Instagram',
            'Youtube',
            'Google'
        );

        foreach ($socialNetworks as $socialNetwork) {

            $socialNetworkTypeEntity = new SocialNetworkType();
            $socialNetworkTypeEntity->setTitle($socialNetwork);

            $manager->persist($socialNetworkTypeEntity);
        }

        $manager->flush();


    }
}