<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Chat;
use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Rate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class RateController extends Controller
{
    public function publishRateAction(Request $request)
    {
        $response = array();

        $currentUser = $this->getUser();

        $data = $request->getContent();

        $data = json_decode($data, true);

        $rated_user_id = $data['rated_user_id'];

        $stars = $data['stars'];

        $point_of_views = $data['point_of_views'];

        $entityManager = $this->getDoctrine()->getManager();

        $ratedUser = $this->getDoctrine()->getRepository(User::class)->find($rated_user_id);

        if ($ratedUser) {
            $rate = new Rate();
            $rate->setUser($currentUser);
            $rate->setRatedUser($ratedUser);
            $rate->setStars($stars);
            $rate->setPointOfView($point_of_views);
            $rate->setCreatedAtAutomatically();

            $entityManager->persist($rate);
            $entityManager->flush();

            $response = array("code" => "rate_added");
        } else {
            $response = array("code" => "action_not_allowed");
        }


        return new JsonResponse($response);
    }

    public function getAllRatesAction($ratedUserId)
    {

        $ratedUser = $this->getDoctrine()->getRepository(User::class)->find($ratedUserId);

        if ($ratedUser) {

            $em = $this->getDoctrine()->getRepository(Rate::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->WhereRatedUser($qb, $ratedUser);
            $qb = $em->OrderById($qb);

            $rates = $qb->getQuery()->getResult();

            $qb = $em->GetQueryBuilder();
            $qb = $em->GetRatesAvg($qb, $ratedUser);
            $avg = $qb->getQuery()->getSingleScalarResult();

            $response = array("rates" => $rates, "avg" => intval($avg));


        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }


}