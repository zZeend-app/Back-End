<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Like;
use ApiBundle\Entity\Post;
use ApiBundle\Entity\View;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class PostController extends Controller
{

    public function newPostAction(Request $request)
    {
        $response = array();
        $currentUser = $this->getUser();

        if ($currentUser !== null) {

            $data = $request->getContent();
            $data = json_decode($data, true);

            $file_path = null;
            $file_type = null;

            $entityManager = $this->getDoctrine()->getManager();

            $post = new Post();

            $post->setUser($currentUser);

            if (array_key_exists('text', $data)) {
                $text = $data['text'];
                $post->setText($text);
            } else {
                $post->setText(null);
            }

            if (array_key_exists('filePath', $data)) {
                $filePath = $data['filePath'];
                $post->setFilePath($filePath);
            } else {
                $post->setFilePath($file_path);
            }

            if (array_key_exists('fileType', $data)) {
                $fileType = $data['fileType'];
                $post->setFileType($fileType);
            } else {
                $post->setFileType($file_type);
            }

            if (array_key_exists('link', $data)) {
                $link = $data['link'];
                $post->setLink($link);
            } else {
                $post->setLink(null);
            }

            if (array_key_exists('tags', $data)) {
                $tags = $data['tags'];
                $post->setTags($tags);
            } else {
                $post->setLink(null);
            }



            $post->setShare(null);
            $post->setCreatedAtAutomatically();

            if (array_key_exists('is_profile_related', $data)) {
                $is_profile_related = $data['is_profile_related'];
                $post->setIsProfileRelated($is_profile_related);
            } else {
                $post->setIsProfileRelated(false);
            }

            $entityManager->persist($post);
            $entityManager->flush();

            $response = array("code" => "post_added");
        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

    public function getAllPostAction(Request $request)
    {

        $response = array();

        $data = $request->getContent();

        $currentUser = $this->getUser();

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

        $posts = $jsonManager->setQueryLimit($qb, $filtersInclude);


        for ($i = 0; $i < count($posts); $i++) {
            $sharedContent = null;
            $post = $posts[$i];

            $em = $this->getDoctrine()->getRepository(Like::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->GetLikesCount($qb, $post);
            $nbLikes = $qb->getQuery()->getSingleScalarResult();

            $em = $this->getDoctrine()->getRepository(View::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->GetViewsCount($qb, $post);
            $nbViews = $qb->getQuery()->getSingleScalarResult();


            if ($post->getShare() !== null) {
                $shareTypeId = $post->getShare()->getShareType()->getId();

                $relatedId = $post->getShare()->getRelatedId();


                if ($shareTypeId == 1) {
                    $sharedContent = $this->getDoctrine()->getRepository(Post::class)->find($relatedId);
                } else if ($shareTypeId == 2) {
                    $sharedContent = $this->getDoctrine()->getRepository(User::class)->find($relatedId);
                }
            }

            $em = $this->getDoctrine()->getRepository(Like::class);
            $qb = $em->GetQueryBuilder();
            $qb =  $em->WhereUserLikesPost($qb, $currentUser, $post);
            $postLikeState = $qb->getQuery()->getResult();

            $response[] = array(
                "post" => $post,
                "postLikeState" => $postLikeState,
                "likes" => intval($nbLikes),
                "views" => intval($nbViews),
                "sharedContent" => $sharedContent
            );


        }

        return new JsonResponse($response);
    }

    public function getCurrentUserAllPostsAction(Request $request)
    {

        $response = array();

        $data = $request->getContent();

        $data = json_decode($data, true);

        $jsonManager = $this->get("ionicapi.jsonManager");

        $currentUser = $this->getUser();

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
        $qb = $em->WhereUser($qb, $currentUser);

        if (array_key_exists("order", $data)) {
            $qb = $em->OrderByJson($qb, $data["order"]);
        }

//        $posts = $qb->getQuery()->getResult();

        $posts = $jsonManager->setQueryLimit($qb, $filtersInclude);


        for ($i = 0; $i < count($posts); $i++) {
            $sharedContent = null;
            $post = $posts[$i];

            $em = $this->getDoctrine()->getRepository(Like::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->GetLikesCount($qb, $post);
            $nbLikes = $qb->getQuery()->getSingleScalarResult();

            $em = $this->getDoctrine()->getRepository(View::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->GetViewsCount($qb, $post);
            $nbLViews = $qb->getQuery()->getSingleScalarResult();

            if ($post->getShare() !== null) {
                $shareTypeId = $post->getShare()->getShareType()->getId();

                $relatedId = $post->getShare()->getRelatedId();


                if ($shareTypeId == 1) {
                    $sharedContent = $this->getDoctrine()->getRepository(Post::class)->find($relatedId);
                } else if ($shareTypeId == 2) {
                    $sharedContent = $this->getDoctrine()->getRepository(User::class)->find($relatedId);
                }
            }

            $response[] = array(
                "post" => $post,
                "likes" => intval($nbLikes),
                "views" => intval($nbLViews),
                "sharedContent" => $sharedContent
            );


        }

        return new JsonResponse($response);
    }

    public function getPostAction($postId)
    {

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