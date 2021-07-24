<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Service;
use ApiBundle\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TagController extends Controller
{

    public function addTagsAction(Request $request)
    {
        $response = array();
        $currentUser = $this->getUser();

        $data = $request->getContent();

        $tags = json_decode($data, true);

        $entityManager = $this->getDoctrine()->getManager();
        for ($i = 0; $i < count($tags); $i++) {
            $tag = $tags[$i];

            $tag = new Tag();
            $tag->setUser($currentUser);
            $tag->setTitle($tag);
            $tag->setCreatedAtAutomatically();
            $tag->setUpdatedAtAutomatically();

            $entityManager->persist($tag);

        }

        $entityManager->flush();


        return new JsonResponse($response);
    }

    public function getAllTagsAction()
    {

        $response = array();

        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getRepository(Tag::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereUser($qb, $currentUser);

        $response = $qb->getQUery()->getResult();

        return new JsonResponse($response);

    }

    public function editTagAction(Request $request)
    {
        $response = array();
        $updated = array();

        $currentUser = $this->getUser();

        $data = $request->getContent();

        $data = json_decode($data, true);

        $tag_id = $data['tag_id'];
        $tag_title = $data['tag_title'];

        $entityManager = $this->getDoctrine()->getManager();
        $tag = $this->getDoctrine()->getRepository(Tag::class)->find($tag_id);

        if ($tag !== null) {

            $tag->setTitle($tag_title);
            $tag->setUpdatedAtAutomatically();

            $entityManager->persist($tag);
            $entityManager->flush();

            $updated = "tag";

            $response = array("updated" => $updated);

        } else {

            $response = array("code" => "action_not_allowed");

        }

        return new JsonResponse($response);

    }

    public function deleteTagAction($tagId)
    {
        $response = array();

        $currentUser = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        $tag = $this->getDoctrine()->getRepository(Tag::class)->find($tagId);

        if ($tag !== null) {

            $entityManager->remove($tag);
            $entityManager->flush();

        } else {

            $response = array("code" => "action_not_allowed");

        }

        return new JsonResponse($response);
    }

    public function changePosition(Request $request){

//        $response = array();
//        $data = $request->getContent();
//        $data = json_decode($data, true);
//
//        $fromId = $data['fromId'];
//        $toId = $data['toId'];
//
//        $currentService = null;
//
//        $entityManager = $this->getDoctrine()->getManager();
//
//        $fromService = $this->getDoctrine()->getRepository(Service::class)->find($fromId);
//        $toService = $this->getDoctrine()->getRepository(Service::class)->find($toId);
//        $cool = '';
//        if ($fromService !== null && $toService !== null) {
//
//            $services = $this->getDoctrine()->getRepository(Service::class)->findAll();
//
//            if($fromId < $toId){
//                $services = array_reverse($services);
//            }
//
//            $temp_to = null;
//            $temp_fromId = 0;
//            $precedent = null;
//
//            for($i = 0; $i < count($services); $i++){
//
//                if($services[$i]->getId() == $toId){
//
//                    $precedent = $services[$i];
//                    $temp_to = $services[$i];
//
//                }else{
//
//                    if($precedent !== null){
//
//                        if($temp_to !== null){
//
//                            if($services[$i]->getId() == $fromId){
//
//                                $values[] = $precedent;
//                                $clonedService = clone $services[$i];
//                                $services[$i]->setService($precedent->getService());
//                                $entityManager->persist($services[$i]);
//                                $entityManager->flush();
//
//                                $temp_from = $services[$i]->getId();
//                                $entityManager = $this->getDoctrine()->getManager();
//                                $toObject = $this->getDoctrine()->getRepository(Service::class)->find($toId);
//                                $toObject->setService($clonedService->getService());
//                                $entityManager->persist($services[$i]);
//                                $entityManager->flush();
//                                break;
//
//                            }else{
//
//                                $values[] = $precedent;
//                                $clonedService = clone $services[$i];
//                                $services[$i]->setService($precedent->getService());
//                                $entityManager->persist($services[$i]);
//                                $entityManager->flush();
//
//                                $precedent = $clonedService;
//                            }
//                        }
//                    }else {
//                        $precedent = $services[$i];
//                    }
//                }
//            }
//
//            $response = array('code' => 'service_position_changed');
//
//        }else{
//            $response = array("code" => "action_not_allowed");
//        }
//
//        return new JsonResponse($response);


    }

}