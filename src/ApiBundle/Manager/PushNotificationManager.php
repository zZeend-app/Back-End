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

    public function __construct(){

    }


    private function getHeaders(){
        return [
            "Authorization" => "key=" . $this->fireBaseApiKey,
            "Content-Type" => "application/json"
        ];
    }

    public function sendNotification(User $user, $titre, $description, $data = null){

        //Si aucun device, on retourne
        if(count($user->getDevices()) <= 0)
            return false;

        /**
         * @var Device $device
         */
        foreach($user->getDevices() as $device){

            $body = [
                "to" => $device->getToken(),
                "notification" => [
                    "title" => $titre,
                    "body" => $description,
                ]
            ];

            //Si on a un payload
            if($data)
                $body["data"] = $data;

            $result = Request::post($this->baseUrl . "fcm/send", $this->getHeaders(), Request\Body::Json($body));
        }

        return true;
    }


}