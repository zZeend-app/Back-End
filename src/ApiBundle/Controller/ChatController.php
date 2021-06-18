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
    public function sendChatAction(Request $request){
        $data = $request->getContent();
        $data = json_decode($data, true);

        $receiverId = $data['receiverId'];
        $contactId = $data['contactId'];
        $chatFromClient = $data['chat'];

        $user_receiver = $this->getDoctrine()->getRepository(User::class)->find($receiverId);
        $contact = $this->getDoctrine()->getRepository(Contact::class)->find($contactId);

        $em = $this->getDoctrine()->getRepository(Contact::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereContactId($qb, $contactId);

        $contactFound = $qb->getQuery()->getResult();
        if(count($contactFound) > 0) {
            $users = $contactFound[0]->getUsers();
            $_mainUser = $users['mainUser'];
            $_secondUser = $users['secondUser'];

            $mainFalg = false;

            if($receiverId == $_mainUser->getId()){
                $mainFalg = true;
            }

            $entityManager = $this->getDoctrine()->getManager();
            $chat = new Chat();
            $chat->setDiscussion($chatFromClient);
            $chat->setContact($contact);
            $chat->setCreatedAtAutomatically();
            $chat->setFilePath(null);
            $currentUser = $this->getUser();
            if($mainFalg){
                $chat->setUsers($user_receiver, null);
            }else{
                $chat->setUsers(null, $user_receiver);
            }
            $entityManager->persist($chat);
            $entityManager->flush();

            $response = array("code" => "chat_sent");
        }else{
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

    public function getChatAction(Request $request){
        $response = array();
        $data = $request->getContent();
        $data = json_decode($data, true);

        $contactId = $data['contactId'];

        $contact = $this->getDoctrine()->getRepository(Contact::class)->find($contactId);

        $em = $this->getDoctrine()->getRepository(Chat::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereContact($qb, $contact);
        $chats = $qb->getQuery()->getResult();

        $response = $chats;


        return new JsonResponse($response);
    }

    public function getChatContactAction(){
        $response = array();
        $currentUser = $this->getUser();
        $em = $this->getDoctrine()->getRepository(Contact::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereUser($qb, $currentUser);

        $contacts = $qb->getQuery()->getResult();

        for($i = 0; $i < count($contacts); $i++){
            $qb = '';
            $contact = $contacts[$i];
            $em = $this->getDoctrine()->getRepository(Chat::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->WhereContact($qb, $contact);
            $qb = $em->GroupBy($qb, 'contact');

            $response = $qb->getQuery()->getResult();

        }

        return new JsonResponse($response);
    }


}