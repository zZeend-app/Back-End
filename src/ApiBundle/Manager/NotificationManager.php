<?php


namespace ApiBundle\Manager;


use ApiBundle\Entity\Notification;
use ApiBundle\Entity\NotificationType;
use Doctrine\ORM\EntityManager;

class NotificationManager
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;

    }

    public function newNotification($notificationTypeId,  $relatedId){

        $notificationType = $this->em->getRepository(NotificationType::class)->find($notificationTypeId);
        $notification = new Notification();
        $notification->setRelatedId($relatedId);
        $notification->setCreatedAtAutomatically();
        $notification->setViewed(false);
        $notification->setNotificationType($notificationType);
        $this->em->persist($notification);
        $this->em->flush();

    }

}