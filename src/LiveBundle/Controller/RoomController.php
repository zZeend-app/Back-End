<?php

namespace LiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use OpenTok\OpenTok;
use OpenTok\Session;

class RoomController extends Controller
{
    public function newRoomAction()
    {
        $vonage_apiKey = $this->getParameter('api_keys')['vonage_api_key'];
        $vonage_secret_key = $this->getParameter('api_keys')['vonage_secret_key'];


        $opentok = new OpenTok($vonage_apiKey, $vonage_secret_key);
        // Create a session that attempts to use peer-to-peer streaming:
        $session = $opentok->createSession();

        return new JsonResponse($session->getSessionId());
    }
}
