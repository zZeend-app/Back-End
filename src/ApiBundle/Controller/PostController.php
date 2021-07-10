<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Like;
use ApiBundle\Entity\Post;
use ApiBundle\Entity\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PostController extends Controller
{

    public function newPostAction(Request $request){
        $response = array();
        $currentUser = $this->getUser();

        if($currentUser !== null){
            $data = $request->getContent();
            $data = json_decode($data, true);

            $file_path = null;
            $file_type = null;

            $entityManager = $this->getDoctrine()->getManager();

            $post = new Post();

            $post->setUser($currentUser);

            if(array_key_exists('text', $data)){
                $text = $data['text'];
                $post->setText($text);
            }else{
                $post->setText(null);
            }
            $post->setFilePath($file_path);
            $post->setFileType($file_type);

            if(array_key_exists('link', $data)){
                $link = $data['link'];
                $post->setLink($link);
            }else{
                $post->setLink(null);
            }

            $post->setCreatedAtAutomatically();

            if(array_key_exists('is_profile_related', $data)){
                $is_profile_related = $data['is_profile_related'];
                $post->setIsProfileRelated($is_profile_related);
            }else{
                $post->setIsProfileRelated(false);
            }

            $entityManager->persist($post);
            $entityManager->flush();

            $response = array("code" => "post_added");
        }else{
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

    public function getAllPostAction(Request $request){

        $response = array();

        $data = $request->getContent();

        $data = json_decode($data, true);

        $jsonManager = $this->get("ionicapi.jsonManager");

        //make sure nothing is missing inside data
        $data = $jsonManager->getInclude($data);

//        if (array_key_exists("departement", $filtersInclude)) {
//            if($filtersInclude['departement'] !== null) {
//                $qb = $tacheRepo->WhereDepartement($qb, $filtersInclude["departement"]);
//            }
//        }

        //get restriction
        $filtersInclude = $data["filters"]["include"];


        $em = $this->getDoctrine()->getRepository(Post::class);
        $qb = $em->GetQueryBuilder();

      if (array_key_exists("order", $data)) {
          $qb = $em->OrderByJson($qb, $data["order"]);
        }

//        $posts = $qb->getQuery()->getResult();

        $posts = $jsonManager->setQueryLimit($qb,$filtersInclude);


        for($i = 0; $i < count($posts); $i++){
            $post = $posts[$i];

            $em = $this->getDoctrine()->getRepository(Like::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->GetLikesCount($qb, $post);
            $nbLikes = $qb->getQuery()->getSingleScalarResult();

            $em = $this->getDoctrine()->getRepository(View::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->GetViewsCount($qb, $post);
            $nbLViews = $qb->getQuery()->getSingleScalarResult();

            $response[] = array(
                "post" => $post,
                "likes" => intval($nbLikes),
                "views" => intval($nbLViews)
                );


        }

        return new JsonResponse($response);
    }

    public function getPostAction($postId){

        $response = array();

        $post = $this->getDoctrine()->getRepository(Post::class)->find($postId);

        $em = $this->getDoctrine()->getRepository(Like::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->GetLikesCount($qb, $post);
        $nbLikes = $qb->getQuery()->getSingleScalarResult();

        $em = $this->getDoctrine()->getRepository(View::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->GetViewsCount($qb, $post);
        $nbLViews = $qb->getQuery()->getSingleScalarResult();

        $response = array(
            "post" => $post,
            "likes" => intval($nbLikes),
            "views" => intval($nbLViews)
        );

        return new JsonResponse($response);

    }


}