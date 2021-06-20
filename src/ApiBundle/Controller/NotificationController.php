<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class NotificationController extends Controller
{

    public function getAllAction(Request $request){

        $response = array();

        $currentUser = $this->getUser();

        $notifications = $this->getDoctrine()->getRepository(Notification::class)->findAll();

        return new JsonResponse($notifications);
    }

}