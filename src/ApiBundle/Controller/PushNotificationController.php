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

            $em = $this->getDoctrine()->getManager();

            $data = $request->getContent();
            $data = json_decode($data, true);

            if (!array_key_exists("token", $data)) {

                return new JsonResponse(array("code" => "device_token_not_found"), 500);

            }

            $logger = $this->get('logger');
            $token = $data["token"];

            if (!$token) {

                return new JsonResponse(array("code" => "invalid_token"), 500);

            }

            $logger->addInfo("GOT TOKEN");
            $logger->addInfo($token);

            //check if token already exists
            $deviceRepos = $em->getRepository(Device::class);
            $qb = $deviceRepos->GetQueryBuilder();
            $qb = $deviceRepos->WhereDeviceToken($qb, $token);
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
            $device->setToken($token);
            $device->setUser($user);
            $user->addDevice($device);


            $em->persist($user);
            $em->flush();

        return new JsonResponse(array("code" => "token_added"), 200);
        }

}