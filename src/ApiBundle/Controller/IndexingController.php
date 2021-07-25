<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Indexing;
use ApiBundle\Entity\IndexingType;
use ApiBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class IndexingController extends Controller
{

    public function createIndexingAction(Request $request){
        $response = array();
        $currentUser = $this->getUser();

        $data = $request->getContent();

        $data = json_decode($data, true);

        $related_id = $data['related_id'];
        $indexing_type_id = $data['indexing_type_id'];
        $duration = $data['duration'];

        $entityManager = $this->getDoctrine()->getManager();
        $indexingType = $this->getDoctrine()->getRepository(IndexingType::class)->find($indexing_type_id);

        if($indexingType !== null){

            if($indexing_type_id == 1){
                $post = $this->getDoctrine()->getRepository(Post::class)->find($related_id);

                if($post == null){
                    $response = array("code" => "action_not_allowed");
                    return new JsonResponse($response);
                }
            }else if($indexing_type_id == 2){
                $user = $this->getDoctrine()->getRepository(User::class)->find($related_id);

                if($user == null){
                    $response = array("code" => "action_not_allowed");
                    return new JsonResponse($response);
                }
            }

            $indexing = new Indexing();
            $indexing->setActionedUser($currentUser);
            $indexing->setDuration($duration);
            $indexing->setRelatedId($related_id);
            $indexing->setIndexingType($indexingType);
            $indexing->setCreatedAtAutomatically();

            $entityManager->persist($indexing);
            $entityManager->flush();

            $response = array("code" => "content_indexed");

        }else{

            $response = array("code" => "action_not_allowed");

        }


        return new JsonResponse($response);
    }

}