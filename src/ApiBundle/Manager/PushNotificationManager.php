<?php


namespace ApiBundle\Manager;


use ApiBundle\Entity\Device;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Unirest\Request;
use UserBundle\Entity\User;

class PushNotificationManager
{

    private $baseUrl = "https://fcm.googleapis.com/";
    private $fireBaseApiKey = "	AAAAgVfGQ3Q:APA91bHkA-jsIF6BQJmqzTLDnONtZoPejNp4TxULm6rTMuGu6GzLzcCnq6CUdyMNX62TgcovOSMuIGtA8Rk4JYp42EVc_WlLxYQsq6QS92r4t7oRvzMFp4riQ8m1ZGCOSTac1EvRjk2f";

    public function __construct()
    {

    }


    private function getHeaders()
    {
        return [
            "Authorization" => "key=" . $this->fireBaseApiKey,
            "Content-Type" => "application/json"
        ];
    }

    public function sendNotification(User $user, $titre, $description, $data = null, $actionUserPhotoFilePath = null)
    {

        //Si aucun device, on retourne
        if (count($user->getDevices()) <= 0)
            return false;

        /**
         * @var Device $device
         */
        foreach ($user->getDevices() as $device) {

            $body = [
                "to" => $device->getToken(),
                "notification" => [
                    "title" => $titre,
                    "body" => $description,
                    "image" => "http://192.168.2.208/zZeend/Back-End/web/app_dev.php/api/auth/media/file/" . $actionUserPhotoFilePath,
                    "tag" => $titre
                ]
            ];

            //Si on a un payload
            if ($data)
                $body["data"] = $data;

            $result = Request::post($this->baseUrl . "fcm/send", $this->getHeaders(), Request\Body::Json($body));
        }

        return true;
    }


    public function sendGroupNotification(User $users, $titre, $description, $data = null, $actionUserPhotoFilePath = null)
    {

        //Si aucun device, on retourne
        if (count($users) == 0)
            return false;

        /**
         * @var Device $device
         */

        $tokensArray = [];

        for ($i = 0; $i < count($users); $i++) {
            $user = $users[$i];

            foreach ($user->getDevices() as $device) {

                array_push($tokensArray, $device->getToken());

            }

        }

        $tokensArray = print_r(array_chunk($tokensArray, 20));


        for ($k = 0; $k < count($tokensArray); $k++) {
            $_20_arrays = $tokensArray[$k];

            $notification_body = array("notification" => [
                "title" => $titre,
                "body" => $description,
                "image" => "http://192.168.2.208/zZeend/Back-End/web/app_dev.php/api/auth/media/file/" . $actionUserPhotoFilePath,
                "tag" => $titre
            ],

                "registration_ids" => $_20_arrays
            );

            $result = Request::post($this->baseUrl . "fcm/send", $this->getHeaders(), Request\Body::Json($notification_body));
        }


        return true;
    }

}