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

    public function updateCurrentUserAction(Request $request){
        $reponse = array();
        $updated = array();

        $currentUser = $this->getUser();


        $data = $request->getContent();
        $data = json_decode($data, true);

        $fullname = $data['fullname'];
        $job_title = $data['job_title'];
        $job_description = $data['job_description'];
        $address = $data['address'];
        $zip_code = $data['zip_code'];
        $phone_number = $data['phone_number'];

        if($fullname !== '' AND $fullname !== $currentUser->getFullname()){
            $currentUser->setFullname($fullname);
            $updated[] = "fullname";
        }

        if($job_title !== '' AND $job_title !== $currentUser->getJobTitle()){
            $currentUser->setJobTitle($job_title);
            $updated[] = "job_title";
        }

        if($job_description !== '' AND $job_description !== $currentUser->getJobDescription()){
            $currentUser->setJobDescription($job_description);
            $updated[] = "job_description";
        }

        if($address !== '' AND $address !== $currentUser->getAddress()){
            $currentUser->setAddress($address);
            $updated[] = "address";
        }

        if($zip_code !== '' AND $zip_code !== $currentUser->getZipCode()){
            $currentUser->setZipCode($zip_code);
            $updated[] = "zip_code";
        }

        if($phone_number !== '' AND $phone_number !== $currentUser->getPhoneNumber()){
            $currentUser->setPhoneNumber($phone_number);
            $updated[] = "phone_number";
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($currentUser);
        $entityManager->flush();

        $response = array("updated" => $updated);

        return new JsonResponse($response);
    }


}