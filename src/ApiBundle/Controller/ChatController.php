<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Chat;
use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Like;
use ApiBundle\Entity\Notification;
use ApiBundle\Entity\Post;
use ApiBundle\Entity\View;
use Doctrine\ORM\QueryBuilder;
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
            $chat->setFileType(null);
            $chat->setViewed(false);
            $currentUser = $this->getUser();
            $chat->setUser($currentUser);
            $chat->setShare(null);
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
        $chats = $jsonManager->setQueryLimit($qb, $filtersInclude);

        $chats = array_reverse($chats);

        for ($i = 0; $i < count($chats); $i++) {

            $sharedContent = null;
            $nbLikes = null;
            $nbViews = null;
            $postLikeState = [];
            $chat = $chats[$i];
            $currentUser = $this->getUser();

            if($chat->getShare() !== null){
                $shareTypeId = $chat->getShare()->getShareType()->getId();


                $relatedId = $chat->getShare()->getRelatedId();


                if($shareTypeId == 1){
                    $sharedContent = $this->getDoctrine()->getRepository(Post::class)->find($relatedId);

                    $em = $this->getDoctrine()->getRepository(Like::class);
                    $qb = $em->GetQueryBuilder();
                    $qb = $em->GetLikesCount($qb, $sharedContent);
                    $nbLikes = $qb->getQuery()->getSingleScalarResult();

                    $em = $this->getDoctrine()->getRepository(View::class);
                    $qb = $em->GetQueryBuilder();
                    $qb = $em->GetViewsCount($qb, $sharedContent);
                    $nbViews = $qb->getQuery()->getSingleScalarResult();

                    $em = $this->getDoctrine()->getRepository(Like::class);
                    $qb = $em->GetQueryBuilder();
                    $qb =  $em->WhereUserLikesPost($qb, $currentUser, $sharedContent);
                    $postLikeState = $qb->getQuery()->getResult();

                }else if($shareTypeId == 2){
                    $sharedContent = $this->getDoctrine()->getRepository(User::class)->find($relatedId);
                }else if($shareTypeId == 3){
                    //chat shared retrieved
                    $sharedContent = $this->getDoctrine()->getRepository(Chat::class)->find($relatedId);
                }
            }

            $response[] = array(
                "chat" => $chat,
                "sharedContent" => $sharedContent,
                "postLikeState" => $postLikeState,
                "likes" => intval($nbLikes),
                "views" => intval($nbViews)
            );

        }


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
        $RAW_QUERY = 'SELECT chat.contact_id, MAX(chat.created_at) FROM chat INNER JOIN contact WHERE (contact.main_user_id = :main_user_id OR contact.second_user_id = :main_user_id) AND chat.contact_id = contact.id GROUP BY chat.contact_id ORDER BY MAX(chat.created_at) DESC LIMIT ' . $offset . ', ' . $limit . ' ;';

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('main_user_id', $currentUser->getId());
        $statement->execute();

        $chatContactIds = $statement->fetchAll();

        $contacts = [];

        for ($i = 0; $i < count($chatContactIds); $i++) {
            $chatContactId = intval($chatContactIds[$i]["contact_id"]);

            $contact = $this->getDoctrine()->getRepository(Contact::class)->find($chatContactId);

            $em = $this->getDoctrine()->getRepository(Chat::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->GetCountForEachChatContact($qb, $contact, false, $currentUser);

            $nbUnViewed = $qb->getQuery()->getSingleScalarResult();

            $contacts[] = array( "contact" => $contact, "nbUnViewed" => intval($nbUnViewed));
        }


        return new JsonResponse($contacts);
    }

    public function markAsViewedAction(Request $request){
        $response = array();
        $data = $request->getContent();

        $data = json_decode($data, true);

        $contactId = $data['contactId'];

        $contact = $this->getDoctrine()->getRepository(Contact::class)->find($contactId);

        if($contact !== null){

            $entityManager = $this->getDoctrine()->getManager();

            $em = $this->getDoctrine()->getManager();
            $RAW_QUERY = 'UPDATE chat SET chat.viewed = true WHERE chat.contact_id = :contactId;';

            $statement = $em->getConnection()->prepare($RAW_QUERY);
            $statement->bindValue('contactId', $contactId);
            $statement->execute();


            $response = array("code" => "marked_as_viewed");

        }else{

            $response = array("code" => "action_no_allowed");

        }

        return new JsonResponse($response);
    }


}