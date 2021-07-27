<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use ApiBundle\Entity\Notification;
use ApiBundle\Entity\NotificationType;

class RequestController extends Controller
{

    public function sendRequestAction(Request $request)
    {
        $data = $request->getContent();
        $data = json_decode($data, true);

        $senderId = $data['senderId'];
        $receiverId = $data['receiverId'];

        $response = array();
        $sender = $this->getDoctrine()->getRepository(User::class)->find($senderId);
        $receiver = $this->getDoctrine()->getRepository(User::class)->find($receiverId);

        $entityManager = $this->getDoctrine()->getManager();
        $zZeendRequest = new \ApiBundle\Entity\Request();
        $zZeendRequest->setUsers($sender, $receiver);
        $zZeendRequest->setCreatedAtAutomatically();
        $entityManager->persist($zZeendRequest);
        $entityManager->flush();

        $response = array('code' => 'request_sent');

        return new JsonResponse($response);
    }

    public function applyRequestStateAction(Request $request)
    {
        $data = $request->getContent();
        $data = json_decode($data, true);

        $requestId = $data['requestId'];
        $requestState = $data['requestState'];

        $response = array();
        $zZeendRequest = $this->getDoctrine()->getRepository(\ApiBundle\Entity\Request::class)->find($requestId);

        if ($zZeendRequest !== null) {

            $notificationTypeInt = 0;
            $requestAcceptedFlag = false;
            $requestRejectedFlag = false;

            if ($requestState == true) {
                $notificationTypeInt = 2;
                $requestAcceptedFlag = true;
                $requestRejectedFlag = false;

                $users = $zZeendRequest->getUsers();

                $sender = $users['sender'];
                $receiver = $users['receiver'];

                if ($receiver->isGranted('ROLE_OWNER')) {


                    $entityManager = $this->getDoctrine()->getManager();

                    $contact = new Contact();
                    $contact->setUsers($receiver, $sender);
                    $contact->setCreatedAtAutomatically();
                    $entityManager->persist($contact);

                    $entityManager->flush();

                    $response = array('code' => 'request_accepted');
                } else {
                    $response = array('code' => 'action_not_allowed');
                }

            } else {
                $notificationTypeInt = 3;
                $requestAcceptedFlag = false;
                $requestRejectedFlag = true;
                $response = array('code' => 'request_rejected');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $zZeendRequest->setAccepted($requestAcceptedFlag);
            $zZeendRequest->setRejected($requestRejectedFlag);
            $entityManager->persist($zZeendRequest);
            $entityManager->flush();

            $notificationType = $this->getDoctrine()->getRepository(NotificationType::class)->find($notificationTypeInt);

            $entityManager = $this->getDoctrine()->getManager();
            $notification = new Notification();
            $notification->setRelatedId($zZeendRequest->getId());
            $notification->setCreatedAtAutomatically();
            $notification->setViewed(false);
            $notification->setNotificationType($notificationType);
            $entityManager->persist($notification);
            $entityManager->flush();


        } else {
            $response = array('code' => 'request_not_found');
        }

        return new JsonResponse($response);
    }

    public function getAllRequestsAction(Request $request)
    {
        $response = array();
        $data = $request->getContent();
        $data = json_decode($data, true);

        $jsonManager = $this->get("ionicapi.jsonManager");

        //make sure nothing is missing inside data
        $data = $jsonManager->getInclude($data);

        //get restriction
        $filtersInclude = $data["filters"]["include"];

        $userId = $filtersInclude['userId'];


        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        $em = $this->getDoctrine()->getRepository(\ApiBundle\Entity\Request::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereSenderOrReceiver($qb, $user);
        $qb = $em->OrderBy($qb);
        $requests = $jsonManager->setQueryLimit($qb,$filtersInclude);

        return new JsonResponse($requests);

    }

}