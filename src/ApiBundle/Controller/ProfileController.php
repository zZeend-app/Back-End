<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Service;
use ApiBundle\Entity\SocialNetwork;
use ApiBundle\Entity\SocialNetworkType;
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

        $em = $this->getDoctrine()->getRepository(SocialNetwork::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereUser($qb, $user);
        $socialNetworks = $qb->getQuery()->getResult();


        $response['user'] = $user;
        $response['services'] = $services;
        $response['socialNetworks'] = $socialNetworks;


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
        $response = array();
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

    public function visibilityAction(Request $request){
        $response = array();

        $currentUser = $this->getUser();

        $profileVisibility = $currentUser->getVisibility();

        if($profileVisibility == true){
            $profileVisibility = false;
            $response = array("code" => "profile_hidden");
        }else if($profileVisibility == false){
            $profileVisibility = true;
            $response = array("code" => "profile_visible");
        }

        $currentUser->setVisibility($profileVisibility);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($currentUser);
        $entityManager->flush();


        return new JsonResponse($response);

    }

    public function addSocialNetworkAction(Request $request){
        $response = array();

        $currentUser = $this->getUser();


        $data = $request->getContent();
        $data = json_decode($data, true);

        $link = $data['link'];
        $social_network_type = $data['social_network_type'];

        $socialNetworkType = $this->getDoctrine()->getRepository(SocialNetworkType::class)->find($social_network_type);

        if($socialNetworkType){

            $sameSocialNetowk = $this->getDoctrine()->getRepository(SocialNetwork::class)->findOneBy(["socialNetworkType" => $socialNetworkType]);

            if($sameSocialNetowk === null){
                $entityManager = $this->getDoctrine()->getManager();

                $socialNetwork = new SocialNetwork();
                $socialNetwork->setUser($currentUser);
                $socialNetwork->setSocialNetworkType($socialNetworkType);
                $socialNetwork->setLink($link);
                $socialNetwork->setCreatedAtAutomatically();
                $socialNetwork->setUpdatedAtAutomatically();

                $entityManager->persist($socialNetwork);
                $entityManager->flush();

                $response = array("code" => "social_network_added");
            }else{
                $response = array("code" => "action_not_allowed");
            }
        }else{
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);

    }

    public function updateSocialNetworkAction(Request $request)
    {
        $response = array();

        $currentUser = $this->getUser();


        $data = $request->getContent();
        $data = json_decode($data, true);

        $social_network_id = $data['social_network_id'];
        $link = $data['link'];

        $socialNetwork = $this->getDoctrine()->getRepository(SocialNetwork::class)->find($social_network_id);

        if($socialNetwork){

            $entityManager = $this->getDoctrine()->getManager();

            $socialNetwork->setLink($link);

            $entityManager->persist($socialNetwork);
            $entityManager->flush();

            $response = array("code" => "social_network_updated");

        }else{
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

    public function deleteSocialNetworkAction(Request $request)
    {
        $response = array();

        $currentUser = $this->getUser();


        $data = $request->getContent();
        $data = json_decode($data, true);

        $social_network_id = $data['social_network_id'];

        $entityManager = $this->getDoctrine()->getManager();

        $socialNetwork = $entityManager->getRepository(SocialNetwork::class)->find($social_network_id);

        if($socialNetwork !== null){
            $entityManager->remove($socialNetwork);
            $entityManager->flush();

            $response = array("code" => "social_network_delete");
        }else{
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);

    }


}