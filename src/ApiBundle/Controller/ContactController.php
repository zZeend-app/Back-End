<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ContactController extends Controller
{

    public function getConactsAction(){
        $response = array();
        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getRepository(Contact::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->OrWhereUser($qb, $currentUser);
        $qb = $em->OrderById($qb);

        $contacts = $qb->getQuery()->getResult();
        $response = $contacts;

        return new JsonResponse($response);
    }

}