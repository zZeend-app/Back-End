<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Chat;
use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Post;
use ApiBundle\Entity\Share;
use ApiBundle\Entity\ShareType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class ShareController extends Controller
{

    public function shareContentAction(Request $request)
    {
        $response = array();
        $currentUser = $this->getUser();

        $data = $request->getContent();

        $data = json_decode($data, true);

        $shareTypeId = $data['share_type_id'];
        $relatedId = $data['related_id'];
        $sharedDestination = $data['shared_destination'];

        $entityManager = $this->getDoctrine()->getManager();

        $shareType = $this->getDoctrine()->getRepository(ShareType::class)->find($shareTypeId);

        if ($shareType !== null) {

            $share = new Share();
            $share->setUser($currentUser);
            $share->setShareType($shareType);
            if ($shareType->getId() == 1) {
                //if the share is a post
                $post = $this->getDoctrine()->getRepository(Post::class)->find($relatedId);
                if ($post !== null) {
                    $share->setRelatedId($relatedId);
                } else {
                    return new JsonResponse(array("code" => "action_not_Allowed"));
                }
            } else if ($shareType->getId() == 2) {
                //if the share is a profile
                $chat = $this->getDoctrine()->getRepository(User::class)->find($relatedId);
                if ($chat !== null) {
                    $share->setRelatedId($relatedId);
                } else {
                    return new JsonResponse(array("code" => "action_not_Allowed"));
                }
            }


            if ($sharedDestination == 1) {
                //share to post

                $post = new Post();
                $post->setShare($share);
                $post->setCreatedAtAutomatically();
                $entityManager->persist($post);

            } else if ($sharedDestination == 2) {
                //share to chat

                $contactId = $data['contact_id'];
                $contact = $this->getDoctrine()->getRepository(Contact::class)->find($contactId);

                if($contact !== null){
                    $chat = new Chat();
                    $chat->setContact($contact);
                    $chat->setShare($share);
                    $chat->setCreatedAtAutomatically();
                    $entityManager->persist($chat);
                }else{
                    return new JsonResponse(array("code" => "action_not_allowed"));
                }

            }else{
                return new JsonResponse(array("code" => "action_not_allowed"));
            }

            $entityManager->persist($share);
            $entityManager->flush();

            $response = array("code" => "content_shared");

        } else {
            $response = array("code" => "action_not_allowed");
        }


        return new JsonResponse($response);
    }

}