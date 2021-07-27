<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Chat;
use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Notification;
use ApiBundle\Entity\NotificationType;
use ApiBundle\Entity\Zzeend;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class NotificationController extends Controller
{

    public function getAllAction(Request $request){

        $response = array();

        $currentUser = $this->getUser();

        $returnObject = array();

        $data = $request->getContent();

        $data = json_decode($data, true);

        $jsonManager = $this->get("ionicapi.jsonManager");

        //make sure nothing is missing inside data
        $data = $jsonManager->getInclude($data);

        //get restriction
        $filtersInclude = $data["filters"]["include"];

        $em = $this->getDoctrine()->getRepository(Notification::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereViewed($qb, false);

        if (array_key_exists("order", $data)) {
            $qb = $em->OrderByJson($qb, $data["order"]);
        }
        $notifications = $jsonManager->setQueryLimit($qb, $filtersInclude);

        for($i = 0; $i < count($notifications); $i++){

            $notification = $notifications[$i];

            $notificationType = $notification->getNotificationType();
            $notificationTypeId = $notificationType->getId();

            $relatedId = $notification->getRelatedId();

            //zZeend notification
            if($notificationTypeId == 1 || $notificationTypeId == 2 || $notificationTypeId == 3 || $notificationTypeId == 4 || $notificationTypeId == 5){

                $zZeend = $this->getDoctrine()->getRepository(Zzeend::class)->find($relatedId);

                if($zZeend !== null && $zZeend->getUser() !== $currentUser){
                    $returnObject[] = array("id" => $notification->getId(),
                        "notificationType" => $notificationType,
                        "relatedObject" => $zZeend,
                        "viewed" => $notification->getViewed());
                }

            }


        }


        return new JsonResponse($returnObject);
    }

    public function markAsViewedAction(Request $request){
        $response = array();
        $data = $request->getContent();

        $viewedNotifications = json_decode($data, true);

        $entityManager = $this->getDoctrine()->getManager();

        for($i = 0; $i < count($viewedNotifications); $i++){
            $notificationId = $viewedNotifications[$i];
            $notification = $this->getDoctrine()->getRepository(Notification::class)->find($notificationId);

            if($notification !== null){
                $notification->setViewed(true);
                $entityManager->persist($notification);
            }else{
                $response = array("code" => "action_not_allowed");
                return new JsonResponse($response);
            }

        }

        $entityManager->flush();

        $response = array("code" => "marked_as_viewed");

        return new JsonResponse($response);
    }

}