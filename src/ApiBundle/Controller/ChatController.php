<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Chat;
use ApiBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class ChatController extends Controller
{
    public function sendChatAction(Request $request)
    {
        $data = $request->getContent();
        $data = json_decode($data, true);

        $contactId = $data['contactId'];
        $chatFromClient = $data['chat'];

        $currentUser = $this->getUser();

        $contact = $this->getDoctrine()->getRepository(Contact::class)->find($contactId);

        $em = $this->getDoctrine()->getRepository(Contact::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereContactId($qb, $contactId);

        $contactFound = $qb->getQuery()->getResult();
        if (count($contactFound) > 0) {

            $entityManager = $this->getDoctrine()->getManager();
            $chat = new Chat();
            $chat->setDiscussion($chatFromClient);
            $chat->setContact($contact);
            $chat->setCreatedAtAutomatically();
            $chat->setFilePath(null);
            $currentUser = $this->getUser();
            $chat->setUser($currentUser);
            $entityManager->persist($chat);
            $entityManager->flush();

            $response = array("code" => "chat_sent");
        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

    public function getChatAction(Request $request)
    {
        $response = array();
        $data = $request->getContent();
        $data = json_decode($data, true);

        $jsonManager = $this->get("ionicapi.jsonManager");

        //make sure nothing is missing inside data
        $data = $jsonManager->getInclude($data);

        //get restriction
        $filtersInclude = $data["filters"]["include"];

        $contactId = $filtersInclude['contactId'];

        $contact = $this->getDoctrine()->getRepository(Contact::class)->find($contactId);

        $em = $this->getDoctrine()->getRepository(Chat::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereContact($qb, $contact);
        if (array_key_exists("order", $data)) {
            $qb = $em->OrderByJson($qb, $data["order"]);
        }
        $chats = $jsonManager->setQueryLimit($qb,$filtersInclude);

        $response = array_reverse($chats);


        return new JsonResponse($response);
    }

    public function getChatContactAction(Request $request)
    {
        $currentUser = $this->getUser();

        $data = $request->getContent();

        $data = json_decode($data, true);

        $jsonManager = $this->get("ionicapi.jsonManager");

        //make sure nothing is missing inside data
        $data = $jsonManager->getInclude($data);

        //get restriction
        $filtersInclude = $data["filters"]["include"];

        $count = $filtersInclude['count'];
        $offset = $filtersInclude['offset'];

        $limit = $offset + 19;


        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'SELECT chat.contact_id FROM chat INNER JOIN contact WHERE (contact.main_user_id = :main_user_id OR contact.second_user_id = :main_user_id) AND chat.contact_id = contact.id GROUP BY chat.contact_id ORDER BY chat.id DESC LIMIT '.$offset.', '.$limit.' ;';

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('main_user_id', $currentUser->getId());
        $statement->execute();

        $chatContactIds = $statement->fetchAll();

        $contacts = [];

        for ($i = 0; $i < count($chatContactIds); $i++) {
            $chatContactId = intval($chatContactIds[$i]["contact_id"]);

            $contacts[] = $this->getDoctrine()->getRepository(Contact::class)->find($chatContactId);

        }


        return new JsonResponse($contacts);
    }


}