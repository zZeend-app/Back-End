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
            $rate->setPointOfView(nl2br($point_of_views));
            $rate->setCreatedAtAutomatically();

            $entityManager->persist($rate);
            $entityManager->flush();

            $createNotificationManager = $this->get("ionicapi.NotificationManager");
            $createNotificationManager->newNotification(8, $rate->getId());

            $subject = $currentUser->getFullname().' just rated you.';

            //send notification
            $pushNotificationManager = $this->get('ionicapi.push.notification.manager');
            $data = array("type" => 11,
                "rating" => $rate);
            $pushNotificationManager->sendNotification($ratedUser, 'zZeend rates', $subject, $data, $currentUser->getPhoto() !== null ? $currentUser->getPhoto()->getFilePath() : null);


            $response = array("code" => "rate_added");
        } else {
            $response = array("code" => "action_not_allowed");
        }


        return new JsonResponse($response);
    }

    public function getAllRatesAction(Request $request)
    {

        $response = array();
        $data = $request->getContent();
        $data = json_decode($data, true);

        $jsonManager = $this->get("ionicapi.jsonManager");

        //make sure nothing is missing inside data
        $data = $jsonManager->getInclude($data);

        //get restriction
        $filtersInclude = $data["filters"]["include"];

        $ratedUserId = $filtersInclude['user_id'];


        $ratedUser = $this->getDoctrine()->getRepository(User::class)->find($ratedUserId);

        if ($ratedUser) {

            $em = $this->getDoctrine()->getRepository(Rate::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->WhereRatedUser($qb, $ratedUser);
            $qb = $em->OrderById($qb);

            $rates = $jsonManager->setQueryLimit($qb,$filtersInclude);

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