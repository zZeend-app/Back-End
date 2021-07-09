<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Like;
use ApiBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LikeController extends Controller
{

    public function likeStateAction(Request $request)
    {
        $response = array();
        $currentUser = $this->getUser();

        if ($currentUser !== null) {
            $data = $request->getContent();
            $data = json_decode($data, true);

            $post_id = $data['post_id'];

            $post = $this->getDoctrine()->getRepository(Post::class)->find($post_id);

            if ($post !== null) {
                $em = $this->getDoctrine()->getRepository(Like::class);
                $qb = $em->GetQueryBuilder();
                $qb = $em->AndWhereUser($qb, $currentUser);
                $qb = $em->AndWherePost($qb, $post);

                $oldLike = $qb->getQuery()->getOneOrNullResult();
                if ($oldLike) {
                    $active = !$oldLike->getActive();

                    $entityManager = $this->getDoctrine()->getManager();
                    $oldLike->setActive($active);

                    $entityManager->persist($oldLike);
                    $entityManager->flush();


                    if ($active) {
                        $response = array("code" => $this->getPostLikesCount($post));
                    } else {
                        $response = array("code" => $this->getPostLikesCount($post));
                    }

                } else {
                    $entityManager = $this->getDoctrine()->getManager();

                    $like = new Like();

                    $like->setUser($currentUser);

                    $like->setPost($post);
                    $like->setActive(true);
                    $like->setCreatedAtAutomatically();

                    $entityManager->persist($like);
                    $entityManager->flush();
                    $response = array("code" => $this->getPostLikesCount($post));

                }
            } else {
                $response = array("code" => "action_not_allowed");
            }

        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

    public function getPostLikesCount($post)
    {

        $em = $this->getDoctrine()->getRepository(Like::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->GetLikesCount($qb, $post);
        $nbLikes = $qb->getQuery()->getSingleScalarResult();

        return intval($nbLikes);
    }


}