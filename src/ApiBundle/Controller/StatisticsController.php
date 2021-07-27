<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Chat;
use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Like;
use ApiBundle\Entity\Notification;
use ApiBundle\Entity\PaymentMethod;
use ApiBundle\Entity\Post;
use ApiBundle\Entity\Zzeend;
use ApiBundle\Entity\ZzeendStatus;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StatisticsController extends Controller
{

    public function getMenuStatsAction()
    {
        $response = array();

        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getRepository(Zzeend::class);
        $qb = $em->GetQueryBuilder();

        $status = $this->getDoctrine()->getRepository(ZzeendStatus::class)->find(1);
        $qb = $em->OrWhereUser($qb, $currentUser);
        $qb = $em->GetCount($qb, false, $status);
        $nbZzeends = $qb->getQuery()->getSingleScalarResult();

        $em = $this->getDoctrine()->getRepository(\ApiBundle\Entity\Request::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->GetCount($qb);
        $qb = $em->WhereUserReceiver($qb, $currentUser);
        $qb = $em->RequestStateIsNull($qb);
        $nbRequests = $qb->getQuery()->getSingleScalarResult();


        $em = $this->getDoctrine()->getRepository(Notification::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereViewed($qb, false);

        $notifications = $qb->getQuery()->getResult();

        $nbNotifications = 0;

        for($i = 0; $i < count($notifications); $i++){

            $notification = $notifications[$i];

            $notificationType = $notification->getNotificationType();
            $notificationTypeId = $notificationType->getId();

            $relatedId = $notification->getRelatedId();

            //zZeend notification
            if($notificationTypeId == 1 || $notificationTypeId == 2 || $notificationTypeId == 3 || $notificationTypeId == 4 || $notificationTypeId == 5){

                $zZeend = $this->getDoctrine()->getRepository(Zzeend::class)->find($relatedId);

                $zZeendCreator = $zZeend->getUser();
                $zZeendAssignedUser = $zZeend->getUserAssigned();

                //if am not the one who created the current so, add this notification
                if($zZeendCreator !== $currentUser && $zZeendAssignedUser == $currentUser){
                    $nbNotifications++;
                }


            }


        }


        $em = $this->getDoctrine()->getRepository(PaymentMethod::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->GetCount($qb);
        $qb = $em->WhereUser($qb, $currentUser);
        $nbPaymentMethods = $qb->getQuery()->getSingleScalarResult();

        $em = $this->getDoctrine()->getRepository(Chat::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->GetCount($qb, false, $currentUser);
        $nbNewChats = $qb->getQuery()->getResult();

        $response = array("zZeends" => intval($nbZzeends),
            "notifications" => intval($nbNotifications),
            "requests" => intval($nbRequests),
            "events" => '',
            "paymentMethods" => intval($nbPaymentMethods),
            "chats" => intval($nbNewChats));

       return new JsonResponse($response);
    }

}