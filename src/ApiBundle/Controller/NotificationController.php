<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Notification;
use ApiBundle\Entity\NotificationType;
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
        $requestObject = array();

        $data = $request->getContent();

        $data = json_decode($data, true);

        $jsonManager = $this->get("ionicapi.jsonManager");

        //make sure nothing is missing inside data
        $data = $jsonManager->getInclude($data);

        //get restriction
        $filtersInclude = $data["filters"]["include"];

        $count = $filtersInclude['count'];
        $offset = $filtersInclude['offset'];

        $limit = $offset + 19;

                $em = $this->getDoctrine()->getManager();

                $RAW_QUERY = 'SELECT notification.id, notification.viewed, request.sender_id, request.receiver_id, request.accepted, request.rejected, notification.created_at as notification_created_at, request.created_at as request_created_at, notification.notification_type_id FROM request INNER JOIN notification ON request.id = notification.related_id where request.sender_id = :userId OR request.receiver_id = :userId ORDER BY notification.id DESC LIMIT '.$offset.', '.$limit.';';

                $statement = $em->getConnection()->prepare($RAW_QUERY);
                $statement->bindValue('userId', $currentUser->getId());
                $statement->execute();

                $requests = $statement->fetchAll();

                for ($i = 0; $i < count($requests); $i++) {

                    $request = $requests[$i];

                    $sender = $this->getDoctrine()->getRepository(User::class)->find(intval($request['sender_id']));
                    $receiver = $this->getDoctrine()->getRepository(User::class)->find(intval($request['receiver_id']));
                    $notificationType = $this->getDoctrine()->getRepository(NotificationType::class)->find(intval($request['notification_type_id']));

                    $requestObject = array("id" => intval($request['id']),
                        "sender" => $sender,
                        "receiver" => $receiver,
                        "accepted" => boolval($request['accepted']),
                        "rejected" => boolval($request['rejected']),
                        "createdAt" => array("date" => $request['request_created_at'],
                            "timezone_type" => null,
                            "timezone" => null));

                    $returnObject[] = array("id" => $request['id'],
                        "notificationType" => $notificationType,
                        "request" => $requestObject,
                        "viewed" => boolval($request['viewed']),
                        "createdAt" => array("date" => $request['notification_created_at'],
                            "timezone_type" => null,
                            "timezone" => null));
                }

        return new JsonResponse($returnObject);
    }

}