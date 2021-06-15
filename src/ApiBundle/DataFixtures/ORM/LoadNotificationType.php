<?php

namespace ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ApiBundle\Entity\NotificationType;

class LoadNotificationType implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $notificationTitles = array(
            'Request Sent',
            'Request Accepted',
            'Request Rejected',
            'Send Chat',
            'Chat Received',
            'Chat Seen'
        );

        foreach ($notificationTitles as $notificationTitle) {

            $notificationTypesEntity = new NotificationType();
            $notificationTypesEntity->setTitle($notificationTitle);

            $manager->persist($notificationTypesEntity);
        }

        $manager->flush();


    }

}

