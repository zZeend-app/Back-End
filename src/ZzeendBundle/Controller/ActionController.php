<?php


namespace ZzeendBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use UserBundle\Entity\User;
use ZzeendBundle\Entity\Contact;
use ZzeendBundle\Entity\Notification;
use ZzeendBundle\Entity\NotificationType;
use ZzeendBundle\Entity\Request;
use ZzeendBundle\Entity\Service;

class ActionController extends Controller
{

    public function addUserServicesAction($userId, $services){
        $response = array();
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        if(count($services) > 0){

            for($i = 0; $i < count($services); $i++){
                $service = new Service();
                if(trim($services[$i]) !== '') {
                    $service->setService($services[$i]);
                    $service->setUser($user);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($service);
                    $entityManager->flush();
                }
            }
        }
        $response = array("code" => "add_success");
        return new JsonResponse($response);
    }

    public function sendRequestAction($senderId, $receiverId){

        $response = array();
        $sender = $this->getDoctrine()->getRepository(User::class)->find($senderId);
        $receiver = $this->getDoctrine()->getRepository(User::class)->find($receiverId);

        $entityManager = $this->getDoctrine()->getManager();
        $zZeendRequest = new Request();
        $zZeendRequest->setUsers($sender, $receiver);
        $zZeendRequest->setCreatedAtAutomatically();
        $entityManager->persist($zZeendRequest);
        $entityManager->flush();

        $notificationType = $this->getDoctrine()->getRepository(NotificationType::class)->find(1);

        $entityManager = $this->getDoctrine()->getManager();
        $notification = new Notification();
        $notification->setRelated($zZeendRequest);
        $notification->setCreatedAtAutomatically();
        $notification->setViewed(false);
        $notification->setNotificationType($notificationType);
        $entityManager->persist($notification);
        $entityManager->flush();

        $response = array('code' => 'request_sent');

        return new JsonResponse($response);

    }

    public function changeRequestStateAction($requestId, $requestState){
        $response = array();
        $zZeendRequest = $this->getDoctrine()->getRepository(Request::class)->find($requestId);

        if($zZeendRequest !== null){

            $notificationTypeInt = 0;
            $requestAcceptedFlag = false;
            $requestRejectedFlag = false;

            if($requestState == true){
                $notificationTypeInt = 2;
                $requestAcceptedFlag = true;
                $requestRejectedFlag = false;

                $users = $zZeendRequest->getUsers();

                $sender = $users['sender'];
                $receiver = $users['receiver'];

                $entityManager = $this->getDoctrine()->getManager();

                $contact = new Contact();
                $contact->setUsers($receiver, $sender);
                $contact->setCreatedAtAutomatically();
                $entityManager->persist($contact);

                $contact = new Contact();
                $contact->setUsers($sender, $receiver);
                $contact->setCreatedAtAutomatically();
                $entityManager->persist($contact);

                $entityManager->flush();


                $response = array('code' => 'request_accepted');


            }else{
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
            $notification->setRelated($zZeendRequest);
            $notification->setCreatedAtAutomatically();
            $notification->setViewed(false);
            $notification->setNotificationType($notificationType);
            $entityManager->persist($notification);
            $entityManager->flush();

        }else{
            $response = array('code' => 'request_not_found');
        }

        return new JsonResponse($response);

    }

}