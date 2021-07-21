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
                    if($post->getShare() !== null){

                        $tempShare = $post->getShare();

                        $tempShareType = $tempShare->getShareType();

                        $share->setShareType($tempShareType);
                        $share->setRelatedId($tempShare->getRelatedId());

                    }else{
                        $share->setRelatedId($relatedId);
                    }
                } else {
                    return new JsonResponse(array("code" => "action_not_Allowed"));
                }
            } else if ($shareType->getId() == 2) {
                //if the share is a profile
                $user = $this->getDoctrine()->getRepository(User::class)->find($relatedId);
                if ($user !== null) {
                    $share->setRelatedId($relatedId);
                } else {
                    return new JsonResponse(array("code" => "action_not_Allowed"));
                }
            } else if ($shareType->getId() == 3) {
                //if the share is a chat
                $chat = $this->getDoctrine()->getRepository(Chat::class)->find($relatedId);
                if ($chat !== null) {

                    if($chat->getShare() !== null){

                        $tempShare = $chat->getShare();

                        $tempShareType = $tempShare->getShareType();

                        $share->setShareType($tempShareType);
                        $share->setRelatedId($tempShare->getRelatedId());

                    }else {

                        $share->setRelatedId($relatedId);

                    }
                } else {
                    return new JsonResponse(array("code" => "action_not_Allowed"));
                }
            }


            if ($sharedDestination == 1) {
                //share to post

                $text = '';

                if(array_key_exists('text', $data)){
                    $text = $data['text'];
                }

                $post = new Post();
                $post->setText($text);
                if($shareType->getId() == 2){
                    $post->setUser($currentUser);
                }
                $post->setShare($share);
                $post->setCreatedAtAutomatically();
                $entityManager->persist($post);

            } else if ($sharedDestination == 2) {
                //share to chat

                $contactId = $data['contactId'];
                $contact = $this->getDoctrine()->getRepository(Contact::class)->find($contactId);

                if($contact !== null){
                    $chat = new Chat();
                    $chat->setContact($contact);
                    $chat->setUser($currentUser);
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