<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Comment;
use ApiBundle\Entity\CommentResponse;
use ApiBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends Controller
{

    public function newCommentAction(Request $request)
    {

        $data = $request->getContent();
        $data = json_decode($data, true);

        $postId = $data['postId'];
        $comment_text = $data['comment'];

        $currentUser = $this->getUser();

        $post = $this->getDoctrine()->getRepository(Post::class)->find($postId);

        if($post !== null){

            $entityManager = $this->getDoctrine()->getManager();

            $comment = new Comment();
            $comment->setComment(nl2br($comment_text));
            $comment->setUser($currentUser);
            $comment->setPost($post);
            $comment->setCreatedAtAutomatically();
    
            $entityManager->persist($comment);
            $entityManager->flush();

            $response = $comment;

        }else{

            $response = array("code" => "action_not_allowed");

        }
       
       return new JsonResponse($response);
    }

    public function newCommentReponseAction(Request $request)
    {

        $data = $request->getContent();
        $data = json_decode($data, true);

        $commentId = $data['commentId'];
        $commentResponse_text = $data['commentResponse'];

        $currentUser = $this->getUser();

        $comment = $this->getDoctrine()->getRepository(Comment::class)->find($commentId);

        if($comment !== null){

            $entityManager = $this->getDoctrine()->getManager();

            $commentReponse = new CommentResponse();
            $commentReponse->setResponse(nl2br($commentResponse_text));
            $commentReponse->setUser($currentUser);
            $commentReponse->setComment($comment);
            $commentReponse->setCreatedAtAutomatically();

            $comment->addCommentResponse($commentReponse);
    
            $entityManager->persist($comment);
            $entityManager->flush();

            $response = $comment;

        }else{

            $response = array("code" => "action_not_allowed");

        }
       
       return new JsonResponse($response);
    }
    
    public function getCommentsAction(Request $request){

        $response = array();
        $postResponses = array();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $jsonManager = $this->get("ionicapi.jsonManager");

        //make sure nothing is missing inside data
        $data = $jsonManager->getInclude($data);

        //get restriction
        $filtersInclude = $data["filters"]["include"];

        $postId = $filtersInclude['postId'];

        $post = $this->getDoctrine()->getRepository(Post::class)->find($postId);

        $em = $this->getDoctrine()->getRepository(Comment::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WherePost($qb, $post);

        if (array_key_exists("order", $data)) {
            $qb = $em->OrderByJson($qb, $data["order"]);
        }

        $comments = $jsonManager->setQueryLimit($qb, $filtersInclude);

        for($i = 0; $i < count($comments); $i++){

            $comment = $comments[$i];

            $postResponses[] = $this->getDoctrine()->getRepository(CommentResponse::class)->findBy(["comment" => $comment]);

        }

        $response = array(
            "comments" => $comments,
            "responses" => $postResponses
        );


        return new JsonResponse($response);


    }


}