<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Service;
use ApiBundle\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TagsController extends Controller
{

    public function addTagsAction(Request $request)
    {
        $response = array();
        $currentUser = $this->getUser();

        $data = $request->getContent();

        $tags = json_decode($data, true);

        $entityManager = $this->getDoctrine()->getManager();
        for ($i = 0; $i < count($tags); $i++) {
            $title = $tags[$i];

            if(trim($tags[$i]) !== ''){
                $tag = new Tag();
                $tag->setUser($currentUser);
                $tag->setTitle($title);
                $tag->setCreatedAtAutomatically();
                $tag->setUpdatedAtAutomatically();

                $entityManager->persist($tag);
            }

        }

        $response = array("code" => "tags_added");

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

            if($tag->getTitle() !== $tag_title){

                $tag->setTitle($tag_title);
                $tag->setUpdatedAtAutomatically();

                $entityManager->persist($tag);
                $entityManager->flush();

                $updated = "tag";

                $response = array("updated" => $updated);

            }else{
                $response = array("updated" => $updated);
            }


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

            $response = array("code" => "tag_deleted");

        } else {

            $response = array("code" => "action_not_allowed");

        }

        return new JsonResponse($response);
    }

    public function changePositionAction(Request $request){

        $response = array();
        $data = $request->getContent();
        $data = json_decode($data, true);

        $fromId = $data['fromId'];
        $toId = $data['toId'];

        $entityManager = $this->getDoctrine()->getManager();

        $fromTag = $this->getDoctrine()->getRepository(Tag::class)->find($fromId);
        $toTag = $this->getDoctrine()->getRepository(Tag::class)->find($toId);

        if ($fromTag !== null && $toTag !== null) {

            $tags = $this->getDoctrine()->getRepository(Tag::class)->findAll();

            if($fromId < $toId){
                $tags = array_reverse($tags);
            }

            $temp_to = null;
            $precedent = null;

            for($i = 0; $i < count($tags); $i++){

                if($tags[$i]->getId() == $toId){

                    $precedent = $tags[$i];
                    $temp_to = $tags[$i];

                }else{

                    if($precedent !== null){

                        if($temp_to !== null){

                            if($tags[$i]->getId() == $fromId){

                                $clonedTag = clone $tags[$i];
                                $tags[$i]->setTitle($precedent->getTitle());
                                $entityManager->persist($tags[$i]);
                                $entityManager->flush();

                                $entityManager = $this->getDoctrine()->getManager();
                                $toObject = $this->getDoctrine()->getRepository(Tag::class)->find($toId);
                                $toObject->setTitle($clonedTag->getTitle());
                                $entityManager->persist($tags[$i]);
                                $entityManager->flush();
                                break;

                            }else{

                                $values[] = $precedent;
                                $clonedTag = clone $tags[$i];
                                $tags[$i]->setTitle($precedent->getTitle());
                                $entityManager->persist($tags[$i]);
                                $entityManager->flush();

                                $precedent = $clonedTag;
                            }
                        }
                    }else {
                        $precedent = $tags[$i];
                    }
                }
            }

            $response = array('code' => 'tag_position_changed');

        }else{
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);


    }

}