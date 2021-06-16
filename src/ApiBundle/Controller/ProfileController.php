<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Service;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class ProfileController extends Controller
{

    public function getProfileAction(Request $request){
        $response = array();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $userId = $data['userId'];

        $response = array();
        $em = $this->getDoctrine()->getRepository(User::class);
        $user = $em->find($userId);

        $em = $this->getDoctrine()->getRepository(Service::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereUser($qb, $user);
        $services = $qb->getQuery()->getResult();

        $connectedUserId = $this->getUser()->getId();

        $response['user'] = $user;
        $response['services'] = $services;


        if($connectedUserId !== $userId){
            $connectedUser = $this->getUser();
            $em = $this->getDoctrine()->getRepository(\ApiBundle\Entity\Request::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->WhereUser($qb, $connectedUser, $user);
            $requestSenderObject = $qb->getQuery()->getResult();

            if(count($requestSenderObject) > 0){
                $response['requestAlreadySent'] = true;
            }else{
                $response['requestAlreadySent'] = false;
            }
        }

        return new JsonResponse($response);
    }

    public function getCurrentUserAction(){
        return $this->forward("UserBundle:User:getCurrentUser");
    }


}