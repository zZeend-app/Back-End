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
        $qb = $em->OrWhereUser($qb, $currentUser);
        $nbRequests = $qb->getQuery()->getSingleScalarResult();

        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'SELECT notification.id, notification.viewed, request.sender_id, request.receiver_id, request.accepted, request.rejected, notification.created_at as notification_created_at, request.created_at as request_created_at, notification.notification_type_id FROM request INNER JOIN notification ON request.id = notification.related_id where request.sender_id = :userId OR request.receiver_id = :userId AND notification.viewed = :flag;';

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('userId', $currentUser->getId());
        $statement->bindValue('flag', false);
        $statement->execute();
        $nbNotifications = count($statement->fetchAll());

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


}