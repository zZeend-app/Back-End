<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Chat;
use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Story;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class StoryController extends Controller
{

    public function getStoriesAction(Request $request)
    {
        $response = array();
        $myContactsStoriesUsers = array();
        $currentUserStoryExists = false;
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

        $limit = $offset + 49;


        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'SELECT story.user_id, MAX(story.created_at) FROM story INNER JOIN contact WHERE (contact.main_user_id = :main_user_id AND contact.second_user_id = story.user_id) OR (contact.main_user_id = story.user_id AND contact.second_user_id = :main_user_id) GROUP BY story.user_id ORDER BY MAX(story.created_at) DESC LIMIT ' . $offset . ', ' . $limit . ' ;';

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('main_user_id', $currentUser->getId());
        $statement->execute();

        $myContactsStoriesUsers = array_merge($myContactsStoriesUsers,$statement->fetchAll());

        $nb_myContactsStoriesUser = count($myContactsStoriesUsers);
        if($nb_myContactsStoriesUser < $count){

            $count = $count - count($myContactsStoriesUsers);

            $limit = $limit + count($myContactsStoriesUsers);

            $RAW_QUERY = 'SELECT story.user_id, MAX(story.created_at) FROM story INNER JOIN contact WHERE (contact.main_user_id = :main_user_id AND contact.second_user_id != story.user_id) OR (contact.main_user_id != story.user_id AND contact.second_user_id = :main_user_id) GROUP BY story.user_id ORDER BY MAX(story.created_at) DESC LIMIT ' . $offset . ', ' . $limit . ' ;';

            $statement = $em->getConnection()->prepare($RAW_QUERY);
            $statement->bindValue('main_user_id', $currentUser->getId());
            $statement->execute();

            $myContactsStoriesUsers = array_merge($myContactsStoriesUsers,$statement->fetchAll());


        }


        for ($i = 0; $i < count($myContactsStoriesUsers); $i++) {
            $userId = intval($myContactsStoriesUsers[$i]["user_id"]);

            if($userId == $currentUser->getId()){
                $currentUserStoryExists = true;
            }else{

                $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

                $stories = $this->getDoctrine()->getRepository(Story::class)->findBy(["user" => $user]);


                $response[] = array( "user" => $user, "stories" => $stories);

            }

        }


        if($currentUserStoryExists){


            $user = $this->getDoctrine()->getRepository(User::class)->find($currentUser->getId());

            $currentUserStories = $this->getDoctrine()->getRepository(Story::class)->findBy(["user" => $user]);


            $tempArray = array( "user" => $user, "stories" => $currentUserStories);

           $response = array_merge([$tempArray], $response);

        }



        return new JsonResponse($response);
    }

    public function getContactBySecondUserIdAction($secondUserId)
    {

        $response = array();
        $currentUser = $this->getUser();

        $secondUser = $this->getDoctrine()->getRepository(User::class)->find($secondUserId);

        if ($secondUser !== null) {

            $em = $this->getDoctrine()->getRepository(Contact::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->WhereSecondUser($qb, $currentUser, $secondUser);
            $response = $qb->getQuery()->getOneOrNullResult();

        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

}