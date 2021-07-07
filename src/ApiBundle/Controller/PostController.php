<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Post;
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

            $text = $data['text'];
            $file_path = null;
            $file_type = null;
            $link = $data['link'];
            $is_profile_relted = $data['is_profile_relted'];

            $entityManager = $this->getDoctrine()->getManager();

            $post = new Post();

            $post->setUser($currentUser);
            $post->setText($text);
            $post->setFilePath($file_path);
            $post->setFileType($file_type);

            if(array_key_exists('link', $data)){
                $link = $data['link'];
                $post->setLink($link);
            }else{
                $post->setLink(null);
            }

            $post->setCreatedAtAutomatically();

            if(array_key_exists('is_profile_relted', $data)){
                $is_profile_relted = $data['is_profile_relted'];
                $post->setIsProfileRelated($is_profile_relted);
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

    public function getAllPost(){

        $this->getDoctrine()->getRepository(Post::class)
    }


}