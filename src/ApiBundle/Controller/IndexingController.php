<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Indexing;
use ApiBundle\Entity\IndexingType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class IndexingController extends Controller
{

    public function createIndexingAction(Request $request){
        $response = array();
        $currentUser = $this->getUser();

        $data = $request->getContent();

        $data = json_decode($data, true);

        $related_id = $data['related_id'];
        $indexing_type_id = $data['indexing_type_id'];

        $entityManager = $this->getDoctrine()->getManager();
        $indexingType = $this->getDoctrine()->getRepository(IndexingType::class)->find($indexing_type_id);

        if($indexingType !== null){

            $indexing = new Indexing();
            $indexing->setActionedUser($currentUser);
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