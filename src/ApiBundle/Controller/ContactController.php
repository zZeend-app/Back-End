<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class ContactController extends Controller
{

    public function getContactsAction(Request $request)
    {
        $response = array();
        $currentUser = $this->getUser();

        $data = $request->getContent();

        $data = json_decode($data, true);

        $jsonManager = $this->get("ionicapi.jsonManager");

        //make sure nothing is missing inside data
        $data = $jsonManager->getInclude($data);

        //get restriction
        $filtersInclude = $data["filters"]["include"];

        $em = $this->getDoctrine()->getRepository(Contact::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->OrWhereUser($qb, $currentUser);
        $qb = $em->OrderById($qb);

        $contacts = $jsonManager->setQueryLimit($qb, $filtersInclude);
        $response = $contacts;

        return new JsonResponse($response);
    }

    public function getContactBySecondUserIdAction($secondUserId)
    {

        $response = array();
        $currentUser = $this->getUser();

        $secondUser = $this->getDoctrine()->getRepository(User::class)->find($secondUserId);

        if ($secondUser !== null) {

            $em = $this->getDoctrine()->getRepository(Contact::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->WhereSecondUser($qb, $currentUser, $secondUser);
            $response = $qb->getQuery()->getOneOrNullResult();

        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

}