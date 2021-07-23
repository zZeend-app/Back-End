<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Like;
use ApiBundle\Entity\Notification;
use ApiBundle\Entity\PaymentMethod;
use ApiBundle\Entity\Post;
use ApiBundle\Entity\Zzeend;
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
        $qb = $em->GetCount($qb);
        $qb = $em->OrWhereUser($qb, $currentUser);
        $nbZzeends = $qb->getQuery()->getSingleScalarResult();

        $em = $this->getDoctrine()->getRepository(\ApiBundle\Entity\Request::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->GetCount($qb);
        $qb = $em->WhereUserReceiver($qb, $currentUser);
        $qb = $em->RequestStateIsNull($qb);
        $nbRequests = $qb->getQuery()->getSingleScalarResult();

        $nbNotifications = 0;

        $nbNotifications = $this->notificationStatistics($currentUser, 'SELECT notification.id, notification.viewed, request.sender_id, request.receiver_id, request.accepted, request.rejected, notification.created_at as notification_created_at, request.created_at as request_created_at, notification.notification_type_id FROM request INNER JOIN notification ON request.id = notification.related_id where notification.notification_type_id = 1 AND request.receiver_id = :userId AND notification.viewed = :flag;');

        $nbNotifications_2 = $this->notificationStatistics($currentUser, 'SELECT notification.id, notification.viewed, request.sender_id, request.receiver_id, request.accepted, request.rejected, notification.created_at as notification_created_at, request.created_at as request_created_at, notification.notification_type_id FROM request INNER JOIN notification ON request.id = notification.related_id where notification.notification_type_id = 2 AND request.sender_id = :userId AND notification.viewed = :flag;');

        $nbNotifications_3 = $this->notificationStatistics($currentUser, 'SELECT notification.id, notification.viewed, request.sender_id, request.receiver_id, request.accepted, request.rejected, notification.created_at as notification_created_at, request.created_at as request_created_at, notification.notification_type_id FROM request INNER JOIN notification ON request.id = notification.related_id where notification.notification_type_id = 3 AND request.sender_id = :userId AND notification.viewed = :flag;');

        $nbNotifications = $nbNotifications + $nbNotifications_2 + $nbNotifications_3;

        $em = $this->getDoctrine()->getRepository(PaymentMethod::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->GetCount($qb);
        $qb = $em->WhereUser($qb, $currentUser);
        $nbPaymentMethods = $qb->getQuery()->getSingleScalarResult();

        $response = array("zZeends" => intval($nbZzeends),
            "notifications" => intval($nbNotifications),
            "requests" => intval($nbRequests),
            "events" => '',
            "paymentMethods" => intval($nbPaymentMethods));

       return new JsonResponse($response);
    }

    private function notificationStatistics($currentUser, $RAW_QUERY){
        $em = $this->getDoctrine()->getManager();

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('userId', $currentUser->getId());
        $statement->bindValue('flag', false);
        $statement->execute();
        return count($statement->fetchAll());
    }


}