<?php

namespace LiveBundle\Controller;

use LiveBundle\Entity\RoomArchive;
use LiveBundle\Entity\RoomGuest;
use LiveBundle\Entity\Room;
use LiveBundle\Entity\RoomTargetType;
use LiveBundle\Entity\RoomType;
use OpenTok\ArchiveMode;
use OpenTok\MediaMode;
use OpenTok\OutputMode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use OpenTok\OpenTok;
use OpenTok\Session;
use OpenTok\Role;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class RoomController extends Controller
{
    public function newRoomAction(Request $request)
    {

        $response = array();

        $headers = $request->headers->all();
        $live_api_key = $this->getParameter('api_keys')['live-api-key'];

        if (array_key_exists('live-api-key', $headers) AND $headers['live-api-key'][0] == $live_api_key) {

            $vonage_apiKey = $this->getParameter('api_keys')['vonage-api-key'];
            $vonage_secret_key = $this->getParameter('api_keys')['vonage-secret-key'];

            $data = $request->getContent();
            $data = json_decode($data, true);

            $userId = $data['userId'];
            $roomTypeIndex = $data['roomTypeIndex'];
            $roomTargetIndex = $data['roomTargetIndex'];

            $moderator = $this->getDoctrine()->getRepository(User::class)->find($userId);

            if($moderator !== null){

                $roomTypeId = 0;

                //type is presentation
                if($roomTypeIndex == 0){
                    $roomTypeId = 1;
                }

                //type is meeting
                if($roomTypeIndex == 1){
                    $roomTypeId = 2;
                }

                //type is single live
                if($roomTypeIndex == 2){
                    $roomTypeId = 4;
                }

                //type is video call
                if($roomTypeIndex == 3){
                    $roomTypeId = 3;
                }

                $roomType = $this->getDoctrine()->getRepository(RoomType::class)->find($roomTypeId);

                if($roomType !== null){


                    $roomTargetId = 0;

                    //target is public
                    if($roomTargetIndex == 0){
                        $roomTargetId = 1;
                    }

                    //type is contact
                    if($roomTargetIndex == 1){
                        $roomTargetId = 2;
                    }

                    //type is public & contacts
                    if($roomTargetIndex == 2){
                        $roomTargetId = 3;
                    }

                    $roomTargetType = $this->getDoctrine()->getRepository(RoomTargetType::class)->find($roomTargetId);

                    if($roomTargetType !== null){

                        $opentok = new OpenTok($vonage_apiKey, $vonage_secret_key);
                        // Create a session that attempts to use peer-to-peer streaming:


                        $sessionOptions = array(
                            'mediaMode' => MediaMode::ROUTED
                        );

                        $session = $opentok->createSession();

                        $sessionId = $session->getSessionId();


                        //create a moderator

                        $options = array(
                            'role'       => Role::MODERATOR,
                            'expireTime' => time()+(2 * 24 * 60 * 60), // in 2 days
                            'data'       => 'name='.$moderator->getFullname(),
                            'initialLayoutClassList' => array('focus')
                        );

                        $moderatorToken = $session->generateToken($options);

                        if($moderatorToken !== null){

                            $entityManager = $this->getDoctrine()->getManager();

                            $room = new Room();

                            if(array_key_exists('name', $data)){
                                $room->setName(trim($data['name']));
                            }

                            if(array_key_exists('description', $data)){
                                $room->setDescription(trim($data['description']));
                            }

                            if(array_key_exists('archived', $data)){
                                $room->setArchived(trim($data['archived']));

                            }else{
                                $room->setArchived(false);
                            }

                            $room->setModerator($moderator);
                            $room->setModeratorToken($moderatorToken);

                            if(array_key_exists('nbParticipant', $data)){
                                $room->setNbParticipant($data['nbParticipant']);
                            }

                            $room->setSessionId($sessionId);
                            $room->setActive(true);
                            $room->setRoomType($roomType);
                            $room->setRoomTargetType($roomTargetType);

                            if(array_key_exists('extraData', $data)){
                                $room->setExtraData(trim($data['extraData']));
                            }

                            $room->setCreatedAtAutomatically();

                            $entityManager->persist($room);
                            $entityManager->flush();

                            if(array_key_exists('archived', $data)){

                                if($data['archived']){

                                   $this->archiveStream($room, $opentok, $sessionId, $data['name'],'1280x720');

                                }

                            }else{
                                $room->setArchived(false);
                            }

                            //save guests

                            //2 for contacts and 3 for public and contacts
                            if($roomTargetId == 2 || $roomTargetId == 3){

                                $userIds = $data['userIds'];
                                if(count($userIds) > 0){

                                    for($i = 0; $i < count($userIds); $i++){
                                        $userId = $userIds[$i];

                                        $userGuest = $this->getDoctrine()->getRepository(User::class)->find($userId);

                                        if( $userGuest !== null){

                                            $entityManager = $this->getDoctrine()->getManager();

                                            $guest = new RoomGuest();
                                            $guest->setGuest($userGuest);
                                            $guest->setRoom($room);
                                            $guest->setInvitedAutomatically();

                                            $entityManager->persist($guest);
                                            $entityManager->flush();

                                            $translator = $this->get('translator');

                                            $translateTo = 'en';
                                            if($userGuest->getLang() !== ''){
                                                $translateTo = $userGuest->getLang();
                                            }

                                            $userFullname = $moderator->getFullname();

                                                $subject = $translator->trans('%userFullname% is calling you...', ['%userFullname%' => $userFullname], null, $translateTo);



                                            //send notification
                                            $pushNotificationManager = $this->get('ionicapi.push.notification.manager');

                                            $incomingCall = array(
                                                "room" => $room,
                                                "callee" => $userGuest
                                            );

                                            $data = array("type" => 13,
                                                "incomingCall" => $incomingCall);


                                            //replace moderator by $userGuest in real use
                                            $pushNotificationManager->sendNotification($userGuest, $translator->trans('zZeend video call', [], null, $translateTo), $subject , $data, $moderator->getPhoto() !== null ? $moderator->getPhoto()->getFilePath() : null);


                                        }

                                    }

                                }

                            }


                            $response = $room;

                        }

                    }else{
                        return new JsonResponse(array("code" => "action_not_allowed"));
                    }


                }else{
                    return new JsonResponse(array("code" => "action_not_allowed"));
                }

            }else{
                return new JsonResponse(array("code" => "action_not_allowed"));
            }

        } else {
            return new JsonResponse(array("code" => "invalid_api_key"));
        }

        return new JsonResponse($response);
    }

    public function sendSignalAction(Request $request)
    {
        $response = array();

        $headers = $request->headers->all();
        $live_api_key = $this->getParameter('api_keys')['live-api-key'];

        if (array_key_exists('live-api-key', $headers) and $headers['live-api-key'][0] == $live_api_key) {

            $vonage_apiKey = $this->getParameter('api_keys')['vonage-api-key'];
            $vonage_secret_key = $this->getParameter('api_keys')['vonage-secret-key'];

            $opentok = new OpenTok($vonage_apiKey, $vonage_secret_key);

            $data = $request->getContent();
            $data = json_decode($data, true);

            $roomId = $data['roomId'];
            $data = $data['data'];
            $signalType = $data['signalType'];

            $signalPayload = array(
                'data' => 'some signal message',
                'type' => '1'
            );

            $room = $this->getDoctrine()->getRepository(Room::class)->find($roomId);

            if($room !== null){

                $sessionId = $room->getSessionId();

                if(array_key_exists('userId', $data)){

                    $userId = $data['userId'];
                    $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

                    if($user !== null){

                        $em = $this->getDoctrine()->getRepository(RoomGuest::class);

                        $qb = $em->GetQueryBuilder();
                        $qb = $em->WhereUser($qb, $user);
                        $qb = $em->WhereRoom($qb, $room);

                        $guest = $qb->getQuery()->getOneOrNullResult();

                        if($guest !== null){

                            $guestConnectionId = $guest->getConnectionId();

                            // Send a signal to a specific client
                            $opentok->signal($sessionId, $signalPayload, $guestConnectionId);

                            return new JsonResponse(array("code" => "user_has_been_disconnected"));
                        }

                    }else{
                        return new JsonResponse(array("code" => "action_not_allowed"));
                    }

                }else{
                    // Send a signal to all clients
                    $opentok->signal($sessionId, $signalPayload);
                }

            }else{

                return new JsonResponse(array("code" => "action_not_allowed"));

            }


        }else{
            return new JsonResponse(array("code" => "invalid_api_key"));
        }

    }

    private function archiveStream($room, $opentok, $sessionId, $name, $screenSize){
        // Create a simple archive of a session
        $archive = $opentok->startArchive($sessionId);


        // Create an archive using custom options
        $archiveOptions = array(
            'name' => $name,     // default: null
            'hasAudio' => true,                     // default: true
            'hasVideo' => true,                     // default: true
            'outputMode' => OutputMode::COMPOSED,   // default: OutputMode::COMPOSED
            'resolution' => $screenSize              // default: '640x480'
        );
        $archive = $opentok->startArchive($sessionId, $archiveOptions);

        // Store this archiveId in the database for later use
        $archiveId = $archive->id;

        $entityManager = $this->getDoctrine()->getManager();

        $roomArchive = new RoomArchive();
        $roomArchive->setRoom($room);
        $roomArchive->setArchiveId($archiveId);

        $entityManager->persist($roomArchive);
        $entityManager->flush();

        return $roomArchive;
    }

    private function stopArchive($opentok, $archiveId){
        return $opentok->getArchive($archiveId);
    }

    private function deleteArchive($opentok, $archiveId){
       return $opentok->deleteArchive($archiveId);
    }

    public function getArchiveListAction(Request $request){
        $response = array();

        $headers = $request->headers->all();
        $live_api_key = $this->getParameter('api_keys')['live-api-key'];

        if (array_key_exists('live-api-key', $headers) and $headers['live-api-key'][0] == $live_api_key) {

            $vonage_apiKey = $this->getParameter('api_keys')['vonage-api-key'];
            $vonage_secret_key = $this->getParameter('api_keys')['vonage-secret-key'];

            $opentok = new OpenTok($vonage_apiKey, $vonage_secret_key);

            $data = $request->getContent();
            $data = json_decode($data, true);

            $offset = $data['offset'];
            $count = $data['count'];
            $roomId = $data['roomId'];

            $room = $this->getDoctrine()->getRepository(Room::class)->find($roomId);

            if($room !== null) {

                $sessionId = $room->getSessionId();

                $archiveList = $opentok->listArchives($offset, $count, $sessionId);

                return new JsonResponse($archiveList);

            }else{
                return new JsonResponse(array("code" => "action_not_allowed"));
            }

        }else{
            return new JsonResponse(array("code" => "invalid_api_key"));
        }
    }

    public function callRejectedAction(Request  $request)
    {

        $response = array();

        $headers = $request->headers->all();
        $live_api_key = $this->getParameter('api_keys')['live-api-key'];

        if (array_key_exists('live-api-key', $headers) and $headers['live-api-key'][0] == $live_api_key) {

            $vonage_apiKey = $this->getParameter('api_keys')['vonage-api-key'];
            $vonage_secret_key = $this->getParameter('api_keys')['vonage-secret-key'];

            $data = $request->getContent();
            $data = json_decode($data, true);

            $userId = $data['userId'];
            $roomId = $data['roomId'];

            $userGuest = $this->getDoctrine()->getRepository(User::class)->find($userId);

            if($userGuest !== null){

                $room = $this->getDoctrine()->getRepository(Room::class)->find($roomId);

                if($room !== null){

                    $moderator = $room->getModerator();

                    $translator = $this->get('translator');
                    $translateTo = 'en';
                    if($moderator->getLang() !== ''){
                        $translateTo = $moderator->getLang();
                    }

                    //send notification to moderator that the call has been rejected
                    $pushNotificationManager = $this->get('ionicapi.push.notification.manager');

                    $userFullname = $userGuest->getFullname();

                    $subject = $translator->trans('%userFullname% reject your call.', ['%userFullname%' => $userFullname], null, $translateTo);

                    $incomingCall = array(
                        "room" => $room,
                        "callee" => $userGuest
                    );

                    $data = array("type" => 15,
                        "incomingCall" => $incomingCall);

                    $pushNotificationManager->sendNotification($moderator, $translator->trans('Call rejected', [], null, $translateTo), $subject , $data, $userGuest->getPhoto() !== null ? $userGuest->getPhoto()->getFilePath() : null);

                         $response = array("code" => "notification_sent");

                }else{
                    return new JsonResponse(array("code" => "action_not_allowed"));
                }

            }else{
                return new JsonResponse(array("code" => "action_not_allowed"));
            }


        }else{
            return new JsonResponse(array("code" => "action_not_allowed"));
        }

        return new JsonResponse($response);

    }


    public function callPickedAction(Request  $request)
    {

        $response = array();

        $headers = $request->headers->all();
        $live_api_key = $this->getParameter('api_keys')['live-api-key'];

        if (array_key_exists('live-api-key', $headers) and $headers['live-api-key'][0] == $live_api_key) {

            $vonage_apiKey = $this->getParameter('api_keys')['vonage-api-key'];
            $vonage_secret_key = $this->getParameter('api_keys')['vonage-secret-key'];

            $data = $request->getContent();
            $data = json_decode($data, true);

            $userId = $data['userId'];
            $roomId = $data['roomId'];

            $userGuest = $this->getDoctrine()->getRepository(User::class)->find($userId);

            if($userGuest !== null){

                $room = $this->getDoctrine()->getRepository(Room::class)->find($roomId);

                if($room !== null){

                    $moderator = $room->getModerator();

                    $translator = $this->get('translator');
                    $translateTo = 'en';
                    if($moderator->getLang() !== ''){
                        $translateTo = $moderator->getLang();
                    }

                    $userFullname = $userGuest->getFullname();


                    //send notification to moderator that the call has been rejected
                    $pushNotificationManager = $this->get('ionicapi.push.notification.manager');

                    $subject = $translator->trans('%userFullname% is in video session now.', ['%userFullname%' => $userFullname], null, $translateTo);

                    $incomingCall = array(
                        "room" => $room,
                        "callee" => $userGuest
                    );

                    $data = array("type" => 14,
                        "incomingCall" => $incomingCall);

                    $pushNotificationManager->sendNotification($moderator, $translator->trans('Call picked', [], null, $translateTo), $subject , $data, $userGuest->getPhoto() !== null ? $userGuest->getPhoto()->getFilePath() : null);

                    $response = array("code" => "notification_sent");

                }else{
                    return new JsonResponse(array("code" => "action_not_allowed"));
                }

            }else{
                return new JsonResponse(array("code" => "action_not_allowed"));
            }


        }else{
            return new JsonResponse(array("code" => "action_not_allowed"));
        }

        return new JsonResponse($response);

    }


    public function callBackAction(Request  $request)
    {

        $response = array();

        $headers = $request->headers->all();
        $live_api_key = $this->getParameter('api_keys')['live-api-key'];

        if (array_key_exists('live-api-key', $headers) and $headers['live-api-key'][0] == $live_api_key) {

            $vonage_apiKey = $this->getParameter('api_keys')['vonage-api-key'];
            $vonage_secret_key = $this->getParameter('api_keys')['vonage-secret-key'];

            $data = $request->getContent();
            $data = json_decode($data, true);

            $userId = $data['userId'];
            $roomId = $data['roomId'];

            $userGuest = $this->getDoctrine()->getRepository(User::class)->find($userId);

            if($userGuest !== null){

                $room = $this->getDoctrine()->getRepository(Room::class)->find($roomId);

                if($room !== null){

                    $moderator = $room->getModerator();

                    //send notification to moderator that the call has been rejected
                    $pushNotificationManager = $this->get('ionicapi.push.notification.manager');

                    $translator = $this->get('translator');
                    $translateTo = 'en';
                    if($moderator->getLang() !== ''){
                        $translateTo = $moderator->getLang();
                    }

                    $userFullname = $userGuest->getFullname();

                    $subject = $translator->trans('%userFullname% said he/she will call you back.', ['%userFullname%' => $userFullname], null, $translateTo);

                    $incomingCall = array(
                        "room" => $room,
                        "callee" => $userGuest
                    );

                    $data = array("type" => 16,
                        "incomingCall" => $incomingCall);

                    $pushNotificationManager->sendNotification($moderator, $translator->trans('zZeend video call', [], null, $translateTo), $subject , $data, $userGuest->getPhoto() !== null ? $userGuest->getPhoto()->getFilePath() : null);

                    $response = array("code" => "notification_sent");

                }else{
                    return new JsonResponse(array("code" => "action_not_allowed"));
                }

            }else{
                return new JsonResponse(array("code" => "action_not_allowed"));
            }


        }else{
            return new JsonResponse(array("code" => "action_not_allowed"));
        }

        return new JsonResponse($response);

    }



    public function videoCallStateChangeAction(Request  $request)
    {

        $response = array();

        $headers = $request->headers->all();
        $live_api_key = $this->getParameter('api_keys')['live-api-key'];

        if (array_key_exists('live-api-key', $headers) and $headers['live-api-key'][0] == $live_api_key) {

            $vonage_apiKey = $this->getParameter('api_keys')['vonage-api-key'];
            $vonage_secret_key = $this->getParameter('api_keys')['vonage-secret-key'];

            $data = $request->getContent();
            $data = json_decode($data, true);

            $userId = $data['userId'];
            $roomId = $data['roomId'];
            $userRole = $data['userRole'];

            $userGuest = $this->getDoctrine()->getRepository(User::class)->find($userId);

            if($userGuest !== null){

                $room = $this->getDoctrine()->getRepository(Room::class)->find($roomId);

                if($room !== null){

                    $moderator = $room->getModerator();

                    //send notification to moderator that the call has been rejected
                    $pushNotificationManager = $this->get('ionicapi.push.notification.manager');

                    $translator = $this->get('translator');

                    $incomingCall = array(
                        "room" => $room,
                        "callee" => $userGuest
                    );

                    $data = array("type" => 17,
                        "incomingCall" => $incomingCall);


                    //replace moderator by $userGuest in real use
                    if($userRole == 'GUEST'){

                        $translateTo = 'en';
                        if($moderator->getLang() !== ''){
                            $translateTo = $moderator->getLang();
                        }


                        $userFullname = $userGuest->getFullname();

                        $subject = $translator->trans('%userFullname% ends video session.', ['%userFullname%' => $userFullname], null, $translateTo);


                        //if the guest cut off the call, send notification to moderator that he cut off the call
                        $pushNotificationManager->sendNotification($moderator, $translator->trans('zZeend video call', [], null, $translateTo), $subject , $data, $userGuest->getPhoto() !== null ? $userGuest->getPhoto()->getFilePath() : null);
                    }else if($userRole == 'MODERATOR'){

                        $translateTo = 'en';
                        if($userGuest->getLang() !== ''){
                            $translateTo = $userGuest->getLang();
                        }


                        $userFullname = $moderator->getFullname();

                        $subject = $translator->trans('%userFullname% ends video session.', ['%userFullname%' => $userFullname], null, $translateTo);


                        //the same thing in reverse
                        $pushNotificationManager->sendNotification($userGuest, $translator->trans('zZeend video call', [], null, $translateTo), $subject , $data, $moderator->getPhoto() !== null ? $moderator->getPhoto()->getFilePath() : null);
                    }

                    $response = array("code" => "notification_sent");

                }else{
                    return new JsonResponse(array("code" => "action_not_allowed"));
                }

            }else{
                return new JsonResponse(array("code" => "action_not_allowed"));
            }


        }else{
            return new JsonResponse(array("code" => "action_not_allowed"));
        }

        return new JsonResponse($response);

    }


}
