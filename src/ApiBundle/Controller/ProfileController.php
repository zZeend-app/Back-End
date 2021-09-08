<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use ApiBundle\Entity\File;
use ApiBundle\Entity\Rate;
use ApiBundle\Entity\Service;
use ApiBundle\Entity\SocialNetwork;
use ApiBundle\Entity\SocialNetworkType;
use ApiBundle\Entity\View;
use ApiBundle\Entity\ViewType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class ProfileController extends Controller
{

    public function getProfileAction(Request $request)
    {
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

        $currentUser = $this->getUser();

        $connectedUserId = $currentUser->getId();

        $em = $this->getDoctrine()->getRepository(SocialNetwork::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereUser($qb, $user);
        $socialNetworks = $qb->getQuery()->getResult();

        $em = $this->getDoctrine()->getRepository(Contact::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->GetCount($qb, $user);
        $nbContacts = $qb->getQuery()->getSingleScalarResult();

        $em = $this->getDoctrine()->getRepository(Rate::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->GetRatesAvg($qb, $user);
        $avg = $qb->getQuery()->getSingleScalarResult();

        $em = $this->getDoctrine()->getRepository(View::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->GetViewsCount($qb, $user->getId());

        $viewType = $this->getDoctrine()->getRepository(ViewType::class)->find(2);
        $qb = $em->AndWhereViewType($qb, $viewType);
        $nbViews = $qb->getQuery()->getSingleScalarResult();


        $response['user'] = $user;
        $response['services'] = $services;
        $response['socialNetworks'] = $socialNetworks;
        $response['nbContacts'] = intval($nbContacts);
        $response['avg'] = intval($avg);
        $response['nbViews'] = intval($nbViews);
        $requestSenderObject = '';

        if ($connectedUserId !== $userId) {
            $connectedUser = $this->getUser();

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT r FROM ApiBundle\Entity\Request r Where r.sender = :sender AND r.receiver = :receiver OR r.sender = :receiver AND r.receiver = :sender");
            $query->setParameters(array(
                'sender' => $currentUser,
                'receiver' => $user,
            ));
            $requestSenderObject = $query->getResult();

            if (count($requestSenderObject) > 0) {
                $response['requestAlreadySent'] = true;
            } else {
                $response['requestAlreadySent'] = false;
            }

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT c FROM ApiBundle\Entity\Contact c Where c.mainUser = :mainUser AND c.secondUser = :secondUser OR c.mainUser = :secondUser AND c.secondUser = :mainUser");
            $query->setParameters(array(
                'mainUser' => $currentUser,
                'secondUser' => $user,
            ));
            $requestSenderObject = $query->getResult();

            if (count($requestSenderObject) > 0) {
                $response['alreadyInContact'] = true;
            } else {
                $response['alreadyInContact'] = false;
            }


        }

        return new JsonResponse($response);
    }

    public function getCurrentUserAction()
    {
        return $this->forward("UserBundle:User:getCurrentUser");
    }

    public function updateCurrentUserAction(Request $request)
    {
        $response = array();
        $updated = array();
        $fileName = '';
        $data = array();

        $fileOriginalName = '';
        $fileSize = 0;

        $currentUser = $this->getUser();

        $modification = false;

        if (!empty($request->files->get('profilePhoto'))) {

            $file = $request->files->get('profilePhoto');

            $uploadDir = $this->getParameter('upload_dir');

            $data = json_decode($_POST['data'], true);

            $dataType = $data['dataType'];

            $fileName = $this->get('ionicapi.fileUploaderManager')->upload($file, $uploadDir, $dataType);

            $fileOriginalName = $file->getClientOriginalName();

            $fileSize = $file->getClientSize();

            $data = $data['objectData'];

        }

        if ($fileName == '') {

            //if no upload has made
            $data = $request->getContent();
            $data = json_decode($data, true);
        }


        $fullname = $data['fullname'];
        $job_title = $data['job_title'];
        $job_description = $data['job_description'];
        $address = $data['address'];
        $zip_code = $data['zip_code'];
        $phone_number = $data['phone_number'];
        $spoken_languages = $data['spoken_languages'];

        if ($fullname !== '' and $fullname !== $currentUser->getFullname()) {
            $currentUser->setFullname($fullname);
            $updated[] = "fullname";
            $modification = true;
        }

        if ($job_title !== '' and $job_title !== $currentUser->getJobTitle()) {
            $currentUser->setJobTitle($job_title);
            $updated[] = "job_title";
            $modification = true;
        }

        if ($job_description !== '' and $job_description !== $currentUser->getJobDescription()) {
            $currentUser->setJobDescription($job_description);
            $updated[] = "job_description";
            $modification = true;
        }

        if ($address !== '' and $address !== $currentUser->getAddress()) {
            $currentUser->setAddress($address);
            $updated[] = "address";
            $modification = true;
        }

        if ($zip_code !== '' and $zip_code !== $currentUser->getZipCode()) {
            $currentUser->setZipCode($zip_code);
            $updated[] = "zip_code";
            $modification = true;
        }

        if ($fileName !== '') {

            $fileEntityManager = $this->getDoctrine()->getManager();

            $file = new File();
            $file->setUser($currentUser);
            $file->setFilePath('profile/' . $fileName);
            $file->setFileType('image');
            $file->setFileSize($fileSize);
            $file->setThumbnail('');
            $file->setFileName($fileOriginalName);
            $file->setCreatedAtAutomatically();

            $fileEntityManager->persist($file);
            $fileEntityManager->flush();


            $currentUser->setPhoto($file);
            $updated[] = "profile_photo";
            $modification = true;
        }

        if ($phone_number !== '' and $phone_number !== $currentUser->getPhoneNumber()) {
            $currentUser->setPhoneNumber($phone_number);
            $updated[] = "phone_number";
            $modification = true;
        }

        if ($spoken_languages !== '') {

            $spokenLanaguagesInDB = $currentUser->getSpokenLanguages();

            if (count($spoken_languages) !== count($spokenLanaguagesInDB)) {
                $currentUser->setSpokenLanguages($spoken_languages);
                $updated[] = "spoken_languages";
                $modification = true;
            } else {

                for ($i = 0; $i < count($spoken_languages); $i++) {
                    $language = $spoken_languages[$i];

                    if (!in_array($language, $spokenLanaguagesInDB)) {
                        $currentUser->setSpokenLanguages($spoken_languages);
                        $updated[] = "spoken_languages";
                        $modification = true;
                        break;
                    }
                }
            }
        }

        if ($modification) {
            $currentUser->setUpdatedAtAutomatically();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($currentUser);
        $entityManager->flush();

        $response = array("updated" => $updated);

        return new JsonResponse($response);
    }

    public function visibilityAction(Request $request)
    {
        $response = array();

        $currentUser = $this->getUser();

        $profileVisibility = $currentUser->getVisibility();

        if ($profileVisibility == true) {
            $profileVisibility = false;
            $response = array("code" => "profile_hidden");
        } else if ($profileVisibility == false) {
            $profileVisibility = true;
            $response = array("code" => "profile_visible");
        }

        $currentUser->setVisibility($profileVisibility);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($currentUser);
        $entityManager->flush();


        return new JsonResponse($response);

    }

    public function addSocialNetworkAction(Request $request)
    {
        $response = array();

        $currentUser = $this->getUser();


        $datas = $request->getContent();
        $datas = json_decode($datas, true);

        for ($i = 0; $i < count($datas); $i++) {
            $data = $datas[$i];

            $link = $data['link'];
            $social_network_type = $data['social_network_type'];

            $socialNetworkType = $this->getDoctrine()->getRepository(SocialNetworkType::class)->find($social_network_type);

            if ($socialNetworkType) {

                $sameSocialNetowk = $this->getDoctrine()->getRepository(SocialNetwork::class)->findOneBy(["socialNetworkType" => $socialNetworkType, "user" => $currentUser]);

                if ($sameSocialNetowk === null) {
                    if (trim($link) !== '') {
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
                    }
                } else {
                    $linkInDb = $sameSocialNetowk->getLink();

                    if ($linkInDb !== $link) {
                        $sameSocialNetowk->setLink($link);
                        $sameSocialNetowk->setUpdatedAtAutomatically();

                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($sameSocialNetowk);
                        $entityManager->flush();
                        $response = array("code" => "social_network_updated");
                    }
                }
            }

        }

        return new JsonResponse($response);

    }

    public function getAllSocialNetworkAction(Request $request)
    {
        $response = array();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $profileId = $data['profile_id'];

        $profileUser = $this->getDoctrine()->getRepository(User::class)->find($profileId);

        $em = $this->getDoctrine()->getRepository(SocialNetwork::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereUser($qb, $profileUser);
        $qb = $em->WhereLinkNotEmpty($qb);

        $socialNetworks = $qb->getQUery()->getResult();

        return new JsonResponse($socialNetworks);
    }

    public function upgradeAccountAction()
    {

        $currentUser = $this->getUser();

        $response = array();
        $entityManager = $this->getDoctrine()->getManager();

        $currentUser->setRoles(['ROLE_OWNER']);

        $entityManager->persist($currentUser);

        $entityManager->flush();

        $response = array("code" => "user/account_upgraded");

        return new JsonResponse($response);
    }

    public function updateCurrentPositionAction(Request $request)
    {

        $response = array();

        $currentUser = $this->getUser();

        $data = $request->getContent();

        $data = json_decode($data, true);

        $city = $data['city'];
        $address = $data['address'];
        $administrativeArea = $data['administrativeArea'];
        $countryCode = $data['countryCode'];
        $zipCode = $data['zipCode'];
        $latitude = $data['latitude'];
        $longitude = $data['longitude'];
        $subLocality = $data['subLocality'];
        $subAdministrativeArea = $data['subAdministrativeArea'];
        $countryName = $data['countryName'];
        $updateProfile = $data['updateProfile'];

        if (trim($city) !== '' && trim($address) !== '') {

            if(!$updateProfile){

                $currentUser->setCity($city);
                $currentUser->setAddress($address);
                $currentUser->setZipCode($zipCode);

            }

            $currentUser->setAdministrativeArea($administrativeArea);
            $currentUser->setCountryCode($countryCode);
            $currentUser->setCountry($countryName);
            $currentUser->setLatitude($latitude);
            $currentUser->setLongitude($longitude);
            $currentUser->setSubLocality($subLocality);
            $currentUser->setSubAdministrativeArea($subAdministrativeArea);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($currentUser);
            $entityManager->flush();

            $response = array("code" => "current_position_updated");

        } else {

            $response = array("code" => "action_not_allowed");

        }

        return new JsonResponse($response);

    }

}