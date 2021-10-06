<?php

namespace LiveBundle\Controller;

use LiveBundle\Entity\RoomGuest;
use LiveBundle\Entity\Room;
use OpenTok\OpenTok;
use OpenTok\Role;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class GuestController extends Controller
{
    public function createGuestTokenAction(Request $request)
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

            $roomId = $data['roomId'];

            $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

            if($user !== null){

                $room = $this->getDoctrine()->getRepository(Room::class)->find($roomId);

                if($room !== null){

                    //if the room is still active
                    if($room->getActive()){

                        //create guest token

                        $sessionId = $room->getSessionId();

                        $opentok = new OpenTok($vonage_apiKey, $vonage_secret_key);

                        $options = array(
                            'role'       => Role::PUBLISHER,
                            'expireTime' => time()+(2 * 24 * 60 * 60), // in 2 days
                            'data'       => 'name='.$user->getFullname(),
                            'initialLayoutClassList' => array('focus')
                        );

                        $guestToken = $opentok->generateToken($sessionId, $options);

                        if($guestToken !== null){

                            $em = $this->getDoctrine()->getRepository(RoomGuest::class);

                            $qb = $em->GetQueryBuilder();
                            $qb = $em->WhereUser($qb, $user);
                            $qb = $em->WhereRoom($qb, $room);

                            $guest = $qb->getQuery()->getOneOrNullResult();

                            if($guest !== null){
                                $entityManager = $this->getDoctrine()->getManager();

                                $guest->setGuestToken($guestToken);
                                $guest->setJoinedAutomatically();

                                $entityManager->persist($guest);
                                $entityManager->flush();

                                return new JsonResponse(array("guestToken" => $guestToken));

                            }

                        }


                    }else{
                        return new JsonResponse(array("code" => "room_session_closed"));
                    }

                }else{
                    return new JsonResponse(array("code" => "action_not_allowed"));
                }

            }else{
                return new JsonResponse(array("code" => "action_not_allowed"));
            }

        }else{
            return new JsonResponse(array("code" => "invalid_api_key"));
        }
    }

    public function forceDisconnectGuestAction(Request $request)
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

            $room = $this->getDoctrine()->getRepository(Room::class)->find($roomId);

            if($room !== null){

                $sessionId = $room->getSessionId();

                    //disconnect a client
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

                            $opentok->forceDisconnect($sessionId, $guestConnectionId);

                            return new JsonResponse(array("code" => "user_has_been_disconnected"));
                        }

                    }else{
                        return new JsonResponse(array("code" => "action_not_allowed"));
                    }

            }else{

                return new JsonResponse(array("code" => "action_not_allowed"));

            }



        }else{
            return new JsonResponse(array("code" => "invalid_api_key"));
        }

    }
}
