<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Event;
use ApiBundle\Entity\Finance;
use ApiBundle\Entity\FinancialStatus;
use ApiBundle\Entity\PaymentType;
use ApiBundle\Entity\Transaction;
use ApiBundle\Entity\Zzeend;
use ApiBundle\Entity\ZzeendStatus;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class ZzeendController extends Controller
{

    public function createAction(Request $request){
        $response = array();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $title = $data['title'];
        $cost = $data['cost'];
        $user_assigned_id = $data['user_assigned_id'];
        $from = $data['from'];
        $to = $data['to'];
        $payment_limit_date = $data['payment_limit_date'];

        $currenetUser = $this->getUser();

        if($currenetUser->isGranted('ROLE_OWNER')){
            $userAssigned = $this->getDoctrine()->getRepository(User::class)->find($user_assigned_id);

            $entityManager = $this->getDoctrine()->getManager();
            $zZeend = new Zzeend();
            $zZeend->setUser($currenetUser);
            $zZeend->setTitle($title);
            $zZeend->setCost($cost);
            $zZeend->setUserAssigned($userAssigned);
            $zZeend->setFrom(new \DateTime($from));
            $zZeend->setTo(new \DateTime($to));
            $zZeend->setPaymentLimitDate(new \DateTime($payment_limit_date));
            $zZeend->setCreatedAtAutomatically();
            $zZeend->setUpdatedAtAutomatically();
            $zZeend->setDone(false);
            $zZeend->setTransaction(null);

            $zZeendStatus = $this->getDoctrine()->getRepository(ZzeendStatus::class)->find(1);
            $zZeend->setStatus($zZeendStatus);

            $entityManager->persist($zZeend);
            $entityManager->flush();

            $response = array("code" => $zZeend->getId());

        }else{
            $response = array('code' => 'action_not_allowed');
        }

       return  new JsonResponse($response);

    }

    public function getZzeendsAction(Request $request){

        $response = array();
        $currenetUser = $this->getUser();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $jsonManager = $this->get("ionicapi.jsonManager");

        //make sure nothing is missing inside data
        $data = $jsonManager->getInclude($data);

        //get restriction
        $filtersInclude = $data["filters"]["include"];

        $zZeendStatusId = $filtersInclude['zZeendStatusId'];

        $status = $this->getDoctrine()->getRepository(ZzeendStatus::class)->find($zZeendStatusId);

        if($status !== null) {

            $em = $this->getDoctrine()->getRepository(Zzeend::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->OrWhereUser($qb, $currenetUser);
            $qb = $em->WhereInProgress($qb, $status);
            $qb = $em->OrderById($qb);
            $zZeends = $jsonManager->setQueryLimit($qb, $filtersInclude);

        }else{
            $zZeends = [];
        }

        return new JsonResponse($zZeends);

    }

    public function getZzeendAction($ZzeendId){

        $response = array();
        $currenetUser = $this->getUser();

        $zZeend = $this->getDoctrine()->getRepository(Zzeend::class)->find($ZzeendId);

        return new JsonResponse($zZeend);

    }

    public function paymentAction(Request $request){
        $response = array();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $payment_type = $data['payment_type'];
        $bank_transaction_id = $data['bank_transaction_id'];
        $card = $data['card'];
        $last_four_digit = $data['last_four_digit'];
        $expiration_date = $data['expiration_date'];
        $csv = $data['csv'];
        $zZeend_id = $data['zZeend_id'];

        $currentUser = $this->getUser();

        $paymentType = $this->getDoctrine()->getRepository(PaymentType::class)->find($payment_type);

        if($paymentType){

            $entityManager = $this->getDoctrine()->getManager();
            $transaction = new Transaction();
            $transaction->setUser($currentUser);
            $transaction->setPaymentType($paymentType);
            $transaction->setBankTransactionId($bank_transaction_id);
            $transaction->setCard($card);
            $transaction->setLastFourDigit($last_four_digit);
            $transaction->setExpirationDate($expiration_date);
            $transaction->setCsv($csv);
            $transaction->setCreatedAtAutomatically();

            $entityManager->persist($transaction);

            $zZeend = $this->getDoctrine()->getRepository(Zzeend::class)->find($zZeend_id);
            if($zZeend){
                $zZeend->setTransaction($transaction);
                $zZeend->setUpdatedAtAutomatically();

                $entityManager->persist($zZeend);
                $entityManager->flush();
                $response = array("code" => "payment_success");
            }else{
                $response = array("code" => "action_not_allowed");
            }

        }else{
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

    public function doneAction(Request $request){

        $zZeendCommission = 5;
        $response = array();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $zZeend_id = $data['zZeend_id'];


        $zZeend = $this->getDoctrine()->getRepository(Zzeend::class)->find($zZeend_id);
        if($zZeend) {

            $entityManager = $this->getDoctrine()->getManager();

            $mainZzeendUser = $zZeend->getUser();
            $mainZzeendUser->setZzeendScore($mainZzeendUser->getZzeendScore() + 1);

            $entityManager->persist($mainZzeendUser);

            $zZeendCost = $zZeend->getCost();

            $finance = new Finance();
            $finance->setUser($mainZzeendUser);

            $zZeendCost  = $zZeendCost - $zZeendCommission;
            //todo trsansfer the 5$ to zZeend account (stripe)

            $financialStatus = $this->getDoctrine()->getRepository(FinancialStatus::class)->find(1);
            $finance->setFinancialStatus($financialStatus);
            $finance->setCash($zZeendCost);
            $finance->setActivityDescription('zZeend cash Drop off - PaymentMethod');
            $finance->setCreatedAtAutomatically();
            $finance->setUpdatedAtAutomatically();

            $entityManager->persist($finance);

            $zZeendStatus = $this->getDoctrine()->getRepository(ZzeendStatus::class)->find(3);
            $zZeend->setStatus($zZeendStatus);
            $zZeend->setDone(true);
            $zZeend->setUpdatedAtAutomatically();

            $entityManager->persist($zZeend);
            $entityManager->flush();

            $event = $this->getDoctrine()->getRepository(Event::class)->findOneBy(['zZeend', $zZeend]);

            if($event !== null) {

                $event->setActive(false);

                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($event);
                $entityManager->flush();

            }

            $response = array("code" => "zZeend_done");

        }else{
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

}