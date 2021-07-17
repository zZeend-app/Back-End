<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Event;
use ApiBundle\Entity\Plan;
use ApiBundle\Entity\PlanSubscriptionPayment;
use ApiBundle\Entity\RenewalType;
use ApiBundle\Entity\Subscription;
use ApiBundle\Entity\Transaction;
use ApiBundle\Entity\Zzeend;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class PlanController extends Controller
{

    public function getPlansAction()
    {
        $plans = $this->getDoctrine()->getRepository(Plan::class)->findAll();

        return new JsonResponse($plans);
    }

    public function getUserSubscriptionAction()
    {
        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getRepository(Subscription::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereUser($qb, $currentUser);

        $subscription = $qb->getQuery()->getOneOrNullResult();

        return new JsonResponse($subscription);
    }

    public function planSubscribeAction(Request $request)
    {

        $response = array();

        $currentUser = $this->getUser();
        if ($currentUser !== null) {
            $data = $request->getContent();
            $data = json_decode($data, true);

            $plan_id = $data['plan_id'];
            $renewal_type_id = $data['renewal_id'];

            $plan = $this->getDoctrine()->getRepository(Plan::class)->find($plan_id);

            $renewalType = $this->getDoctrine()->getRepository(RenewalType::class)->find($renewal_type_id);

            if ($plan !== null && $renewalType !== null) {

                $entityManager = $this->getDoctrine()->getManager();

                $subscription = new Subscription();

                $subscription->setUser($currentUser);
                $subscription->setPlan($plan);
                $subscription->setRenewalType($renewalType);
                $subscription->setActive(true);
                $subscription->setCreatedAtAutomatically();
                $subscription->setUpdatedAtAutomatically();

                $entityManager->persist($subscription);
                $entityManager->flush();

                $response = array("code" => "subscribed");

            } else {
                $response = array("code" => "action_not_allowed");
            }
        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);

    }

    public function updatePlanSubscriptionAction(Request $request)
    {

        $updated = array();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $subscription_id = $data['subscription_id'];

        $entityManager = $this->getDoctrine()->getManager();

        $subscription = $this->getDoctrine()->getRepository(Subscription::class)->find($subscription_id);


        if (array_key_exists('plan_id', $data)) {
            $plan_id = $data['plan_id'];
            $plan = $this->getDoctrine()->getRepository(Plan::class)->find($plan_id);
            $subscription->setPlan($plan);
            $updated[] = "plan";
        }

        if (array_key_exists('renewal_type_id', $data)) {
            $renewal_type_id = $data['renewal_type_id'];
            $renewalType = $this->getDoctrine()->getRepository(RenewalType::class)->find($renewal_type_id);
            $subscription->setRenewalType($renewalType);
            $updated[] = "renewal";
        }

        if (array_key_exists('active', $data)) {
            $active = $data['active'];
            $subscription->setActive($active);
            $updated[] = "active";
        }

        $entityManager->persist($subscription);
        $entityManager->flush();

        return new JsonResponse($updated);

    }

    public function planSubscriptionPaymentAction(Request $request)
    {

        $currentUser = $this->getUser();

        $response = array();
        $data = $request->getContent();
        $data = json_decode($data, true);

        $subscription_id = $data['subscription_id'];

        $transaction_id = 3;

        $entityManager = $this->getDoctrine()->getManager();
        $planSubscriptionPayment = new PlanSubscriptionPayment();

        $subscription = $this->getDoctrine()->getRepository(Subscription::class)->find($subscription_id);

        //todo add transaction to plan monthly payment
//        $transaction = $this->getDoctrine()->getRepository(Transaction::class)->find($transaction_id);

        $planSubscriptionPayment->setSubscription($subscription);

        $planSubscriptionPayment->setTransaction(null); //todo
//        $planSubscriptionPayment->setTransaction($transaction);
        $planSubscriptionPayment->setCreatedAtAutomatically();

        $currentUser->setMainVisibility(true);

        $entityManager->persist($planSubscriptionPayment);
        $entityManager->persist($currentUser);
        $entityManager->flush();


        $response = array("code" => "subscription_payed");


        return new JsonResponse($response);

    }

    public function enableAccountMainVisibilityAction($userId)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        $entityManager = $this->getDoctrine()->getManager();

        $user->setMainVisibility(true);
        $entityManager->persist($user);
        $entityManager->flush();
    }

    public function disableAccountMainVisibility($userId)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        $entityManager = $this->getDoctrine()->getManager();

        $user->setMainVisibility(false);
        $entityManager->persist($user);
        $entityManager->flush();
    }

    public function makeTaxesAction(Request $request)
    {

        $response = array();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $baseAmount = $data['base_amount'];

        $baseAmount = round($baseAmount, 2);

        $tvq = round(($baseAmount * 9.975) / 100, 2);
        $tps = round(($baseAmount * 5) / 100, 2);
        $applicationFee = 0; // 2.89

        $finalPrice = $baseAmount + $tvq + $tps + $applicationFee;

        $response['baseAmount'] = $baseAmount;
        $response['tvq'] = $tvq;
        $response['tps'] = $tps;
        $response['applicationFee'] = $applicationFee;
        $response['finalPrice'] = round($finalPrice, 2);

        return new JsonResponse($response);
    }

}