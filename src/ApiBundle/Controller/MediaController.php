<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Service;
use ApiBundle\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class MediaController extends Controller
{

    public function getPhohotsAction(Request $request)
    {
        $response = array();
        $currentUser = $this->getUser();

        $data = $request->getContent();

        $data = json_decode($data, true);

//        $em = $this->getDoctrine()->getRepository(User::class);
//        $qb = $this->PhotosSelectWhereUser($currentUser);
//        $userPhotoProfile



        return new JsonResponse('am a photo');
    }

    public function getVideosAction(Request $request)
    {
        $response = array();
        $currentUser = $this->getUser();

        $data = $request->getContent();

        $data = json_decode($data, true);

    }

}