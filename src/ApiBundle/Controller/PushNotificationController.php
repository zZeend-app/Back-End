<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Device;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use UserBundle\Entity\User;

class PushNotificationController extends Controller
{

    public function registerDeviceAction(Request $request)
    {

        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $data = $request->getContent();
        $data = json_decode($data, true);

        if (!array_key_exists("newToken", $data)) {

            return new JsonResponse(array("code" => "device_token_not_found"), 500);

        }

        $logger = $this->get('logger');

        $oldToken = '';
        $newToken = '';

        if (array_key_exists("oldToken", $data)) {
            $oldToken = $data["oldToken"];
        }

        if (array_key_exists("newToken", $data)) {
            $newToken = $data["newToken"];
        }


        if (!$newToken) {

            return new JsonResponse(array("code" => "invalid_token"), 500);

        }

        $logger->addInfo("GOT TOKEN");
        $logger->addInfo($newToken);

        if ($oldToken !== '') {
            //if old token is not empty

            if ($oldToken !== $newToken) {

                //if the old token is different from the new token
                $deviceRepos = $em->getRepository(Device::class);
                $qb = $deviceRepos->GetQueryBuilder();
                $qb = $deviceRepos->WhereDeviceToken($qb, $oldToken);
                $qb = $deviceRepos->WhereUser($qb, $currentUser);
                $deviceResult = $qb->getQuery()->getOneOrNullResult();

                //if device already exists
                if ($deviceResult) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $deviceResult->setToken($newToken);

                    $entityManager->persist($deviceResult);
                    $em->flush();
                }

            }else{

                //if the 2 tokens are the same

                $deviceRepos = $em->getRepository(Device::class);
                $qb = $deviceRepos->GetQueryBuilder();
                $qb = $deviceRepos->WhereDeviceToken($qb, $oldToken);
                $deviceResult = $qb->getQuery()->getOneOrNullResult();

                //if device already exists
                if ($deviceResult) {
                    $entityManager = $this->getDoctrine()->getManager();

                    if($deviceResult->getUser()->getId() !== $currentUser->getId()){
                        $deviceResult->setUser($currentUser);
                    }

                    $entityManager->persist($deviceResult);
                    $em->flush();
                }
            }

        } else {
            //if old token is empty

            //check if token already exists
            $deviceRepos = $em->getRepository(Device::class);
            $qb = $deviceRepos->GetQueryBuilder();
            $qb = $deviceRepos->WhereDeviceToken($qb, $newToken);
            $deviceResult = $qb->getQuery()->getResult();

            //if device already exists
            if ($deviceResult && count($deviceResult) >= 1) {
                return new JsonResponse(array("code" => "token_added"), 200);
            }

            /**
             * @var User $user
             */
            //Création du device + lien avec le user
            $user = $this->getUser();
            $device = new Device();
            $device->setToken($newToken);
            $device->setUser($user);
            $user->addDevice($device);


            $em->persist($user);
            $em->flush();

        }


        return new JsonResponse(array("code" => "token_added"), 200);
    }

}