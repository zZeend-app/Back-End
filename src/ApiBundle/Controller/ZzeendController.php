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

    public function createAction(Request $request)
    {
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

        if ($currenetUser->isGranted('ROLE_OWNER')) {
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
            $zZeend->setCanceled(false);
            $zZeend->setTransaction(null);

            $zZeendStatus = $this->getDoctrine()->getRepository(ZzeendStatus::class)->find(1);
            $zZeend->setStatus($zZeendStatus);

            $entityManager->persist($zZeend);
            $entityManager->flush();

            $createNotificationManager = $this->get("ionicapi.NotificationManager");
            $createNotificationManager->newNotification(1, $zZeend->getId());

            $subject = 'You were assigned to a new zZeend - '.$zZeend->getTitle();

            //send mail

            $emailManager = $this->get('ionicapi.emailManager');

            $status = '';
            $statusId = $zZeend->getStatus()->getId();
            if ($statusId == 1) {

                $status = 'In progress';

            } else if ($statusId == 2) {

                $status = 'Uncompleted';

            } else if ($statusId == 3) {

                $status = 'Completed';

            }

            $data = array(
                'name' => $zZeend->getUserAssigned()->getFullname(),
                'text' => $zZeend->getUser()->getFullname() . ' just created a new zZeend in your name. Check this out!!!',
                'id' => 'n° ' . $zZeend->getId(),
                'title' => $zZeend->getTitle(),
                'cost' => $zZeend->getCost() . ' $',
                'from' => $zZeend->getFrom()->format('Y-m-d H:i:s'),
                'to' => $zZeend->getTo()->format('Y-m-d H:i:s'),
                'paymentLimitDate' => $zZeend->getPaymentLimitDate()->format('Y-m-d H:i:s'),
                'serviceOwner' => $zZeend->getUser()->getFullname(),
                'status' => $status,
                'payment' => $zZeend->getTransaction() !== null ? 'Yes' : 'Not yet',
                'finalized' => $zZeend->getDone() == true ? 'Yes' : 'Not yet',
                'canceled' => $zZeend->getCanceled() == true ? 'Yes' : 'No',
                'createdAt' => $zZeend->getCreatedAt()->format('Y-m-d H:i:s')

            );

            $app_mail = $this->getParameter('app_mail');
            $emailManager->send($app_mail, $userAssigned->getEmailCanonical(), $subject, '@User/Email/zZeend.twig', $data);


            //send notification
            $pushNotificationManager = $this->get('ionicapi.push.notification.manager');
            $data = array("type" => 1,
                "zZeend" => $zZeend);
            $pushNotificationManager->sendNotification($userAssigned, 'New zZeend (n° '.$zZeend->getId().')', $subject . ' by ' . $currenetUser->getFullname(), $data);

            $response = array("code" => $zZeend->getId());

        } else {
            $response = array('code' => 'action_not_allowed');
        }

        return new JsonResponse($response);

    }

    public function getZzeendsAction(Request $request)
    {

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

        if ($status !== null) {

            $em = $this->getDoctrine()->getRepository(Zzeend::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->OrWhereUser($qb, $currenetUser);
            $qb = $em->WhereInProgress($qb, $status);
            $qb = $em->OrderById($qb);
            $zZeends = $jsonManager->setQueryLimit($qb, $filtersInclude);

        } else {
            $zZeends = [];
        }

        return new JsonResponse($zZeends);

    }

    public function getZzeendAction($ZzeendId)
    {

        $response = array();
        $currenetUser = $this->getUser();

        $zZeend = $this->getDoctrine()->getRepository(Zzeend::class)->find($ZzeendId);

        return new JsonResponse($zZeend);

    }

    public function paymentAction(Request $request)
    {
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

        if ($paymentType) {

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
            if ($zZeend) {
                $zZeend->setTransaction($transaction);
                $zZeend->setUpdatedAtAutomatically();

                $entityManager->persist($zZeend);
                $entityManager->flush();

                $createNotificationManager = $this->get("ionicapi.NotificationManager");
                $createNotificationManager->newNotification(2, $zZeend->getId());

                $serviceOwner = $zZeend->getUser();

                $subject = $currentUser->getFullname().' just made a payment for '.$zZeend->getTitle().').';
                //send notification
                $pushNotificationManager = $this->get('ionicapi.push.notification.manager');
                $data = array("type" => 5,
                    "payment" => array("message" => "This zZeend has been paid"));
                $pushNotificationManager->sendNotification($serviceOwner, 'zZeend paid (n° '.$zZeend->getId().')', $subject , $data);



                $response = array("code" => "payment_success");
            } else {
                $response = array("code" => "action_not_allowed");
            }

        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

    public function doneAction(Request $request)
    {

        $zZeendCommission = 5;
        $response = array();

        $currentUser = $this->getUser();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $zZeend_id = $data['zZeend_id'];


        $zZeend = $this->getDoctrine()->getRepository(Zzeend::class)->find($zZeend_id);
        if ($zZeend) {

            $entityManager = $this->getDoctrine()->getManager();

            $mainZzeendUser = $zZeend->getUser();
            $mainZzeendUser->setZzeendScore($mainZzeendUser->getZzeendScore() + 1);

            $entityManager->persist($mainZzeendUser);

            $zZeendCost = $zZeend->getCost();

            $finance = new Finance();
            $finance->setUser($mainZzeendUser);

            $zZeendCost = $zZeendCost - $zZeendCommission;
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

            $event = $this->getDoctrine()->getRepository(Event::class)->findOneBy(['zZeend' => $zZeend]);

            if ($event !== null) {

                $event->setActive(false);

                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($event);
                $entityManager->flush();

                $createNotificationManager = $this->get("ionicapi.NotificationManager");
                $createNotificationManager->newNotification(3, $zZeend->getId());

            }

            $serviceOwner = $zZeend->getUser();

            $subject = $currentUser->getFullname().' has finalized this zZeend - '.$zZeend->getTitle();
            //send notification
            $pushNotificationManager = $this->get('ionicapi.push.notification.manager');
            $data = array("type" => 6,
                "zZeend" => $zZeend);
            $pushNotificationManager->sendNotification($serviceOwner, 'zZeend finalized (n° '.$zZeend->getId().')', $subject , $data);


            $response = array("code" => "zZeend_done");

        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

    public function cancelAction(Request $request)
    {
        $response = array();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $currentUser = $this->getUser();

        $zZeendId = $data['zZeendId'];

        $zZeend = $this->getDoctrine()->getRepository(Zzeend::class)->find($zZeendId);

        if ($zZeend !== null) {

            $entityManager = $this->getDoctrine()->getManager();

            $zZeend->setCanceled(true);

            $entityManager->persist($zZeend);
            $entityManager->flush();

            //todo not forgot to send back client money

            $createNotificationManager = $this->get("ionicapi.NotificationManager");
            $createNotificationManager->newNotification(4, $zZeend->getId());

            $serviceSeeker = $zZeend->getUserAssigned();

            $subject = $currentUser->getFullname().' has canceled this zZeend - '.$zZeend->getTitle();
            //send notification
            $pushNotificationManager = $this->get('ionicapi.push.notification.manager');
            $data = array("type" => 7,
                "zZeend" => $zZeend);
            $pushNotificationManager->sendNotification($serviceSeeker, 'zZeend canceled (n° '.$zZeend->getId().')', $subject , $data);



            $response = array("code" => "zZeend_canceled");

        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

    public function editAction(Request $request)
    {
        $response = array();
        $update = array();

        $currentUser = $this->getUser();

        $atLeastOne = false;

        $data = $request->getContent();
        $data = json_decode($data, true);

        $zZeendId = $data['zZeendId'];

        $zZeend = $this->getDoctrine()->getRepository(Zzeend::class)->find($zZeendId);

        if ($zZeend !== null) {

            $entityManager = $this->getDoctrine()->getManager();

            if (array_key_exists("title", $data)) {
                $title = $data['title'];
                if ($zZeend->getTitle() !== $title) {
                    $zZeend->setTitle($title);
                    $entityManager->persist($zZeend);
                    $update[] = "title";
                    $atLeastOne = true;
                }
            }

            if (array_key_exists("cost", $data)) {
                $cost = $data['cost'];
                if ($zZeend->getCost() !== $cost) {
                    $zZeend->setCost($cost);
                    $entityManager->persist($zZeend);
                    $update[] = "cost";
                    $atLeastOne = true;
                }
            }

            if (array_key_exists("from", $data)) {
                $from = $data['from'];
                if ($zZeend->getFrom() !== new \DateTime($from)) {
                    $zZeend->setFrom(new \DateTime($from));
                    $entityManager->persist($zZeend);
                    $update[] = "from";
                    $atLeastOne = true;
                }
            }

            if (array_key_exists("to", $data)) {
                $to = $data['to'];
                if ($zZeend->getTo() !== new \DateTime($to)) {
                    $zZeend->setTo(new \DateTime($to));
                    $entityManager->persist($zZeend);
                    $update[] = "to";
                    $atLeastOne = true;
                }
            }

            if (array_key_exists("payment_limit_date", $data)) {
                $paymentLimitDate = $data['payment_limit_date'];
                if ($zZeend->getPaymentLimitDate() !== new \DateTime($paymentLimitDate)) {
                    $zZeend->setPaymentLimitDate(new \DateTime($paymentLimitDate));
                    $entityManager->persist($zZeend);
                    $update[] = "payment_limit_date";
                    $atLeastOne = true;
                }
            }

            if ($atLeastOne) {

                $createNotificationManager = $this->get("ionicapi.NotificationManager");
                $createNotificationManager->newNotification(5, $zZeend->getId());

                $serviceSeeker = $zZeend->getUserAssigned();

                $subject = $currentUser->getFullname().' edited this zZeend - '.$zZeend->getTitle();
                //send notification
                $pushNotificationManager = $this->get('ionicapi.push.notification.manager');
                $data = array("type" => 8,
                    "zZeend" => $zZeend);
                $pushNotificationManager->sendNotification($serviceSeeker, 'zZeend update (n° '.$zZeend->getId().')', $subject , $data);


            }


            $entityManager->flush();


            $response = array("updated" => $update);

        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

}