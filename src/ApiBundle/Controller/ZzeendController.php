<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Event;
use ApiBundle\Entity\Finance;
use ApiBundle\Entity\FinancialStatus;
use ApiBundle\Entity\PaymentMethod;
use ApiBundle\Entity\PaymentType;
use ApiBundle\Entity\StripeConnectAccount;
use ApiBundle\Entity\Transaction;
use ApiBundle\Entity\ViewType;
use ApiBundle\Entity\Zzeend;
use ApiBundle\Entity\ZzeendPoint;
use ApiBundle\Entity\ZzeendService;
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

        $translator = $this->get('translator');

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

            $translateTo = 'en';
            if ($userAssigned->getLang() !== '') {
                $translateTo = $userAssigned->getLang();
            }

            $subject = $translator->trans('You were assigned to a new zZeend', [], null, $translateTo) . '.';


            $emailManager = $this->get('ionicapi.emailManager');

            $status = '';
            $statusId = $zZeend->getStatus()->getId();
            if ($statusId == 1) {

                $status = $translator->trans('In progress', [], null, $translateTo);

            } else if ($statusId == 2) {

                $status = $translator->trans('Uncompleted', [], null, $translateTo);

            } else if ($statusId == 3) {

                $status = $translator->trans('Completed', [], null, $translateTo);

            }

            $userFullname = $zZeend->getUser()->getFullname();

            $data = array(
                'name' => $zZeend->getUserAssigned()->getFullname(),
                'text' => $translator->trans("%userFullname% just created a new zZeend in your name. Check this out!!!", ['%userFullname%' => $userFullname], null, $translateTo),
                'id' => 'n?? ' . $zZeend->getId(),
                'title' => $zZeend->getTitle(),
                'cost' => ' $' . $zZeend->getCost(),
                'from' => $translator->trans($zZeend->getFrom()->format('Y-m-d H:i:s'), [], null, $translateTo),
                'to' => $translator->trans($zZeend->getTo()->format('Y-m-d H:i:s'), [], null, $translateTo),
                'paymentLimitDate' => $translator->trans($zZeend->getPaymentLimitDate()->format('Y-m-d H:i:s'), [], null, $translateTo),
                'serviceOwner' => $zZeend->getUser()->getFullname(),
                'status' => $status,
                'payment' => $zZeend->getTransaction() !== null ? $translator->trans('Yes', [], null, $translateTo) : $translator->trans('Not yet', [], null, $translateTo),
                'finalized' => $zZeend->getDone() == true ? $translator->trans('Yes', [], null, $translateTo) : $translator->trans('Not yet', [], null, $translateTo),
                'canceled' => $zZeend->getCanceled() == true ? $translator->trans('Yes', [], null, $translateTo) : $translator->trans('No', [], null, $translateTo),
                'createdAt' => $translator->trans($zZeend->getCreatedAt()->format('Y-m-d H:i:s'), [], null, $translateTo),
                'lang' => $translateTo

            );

            $app_mail = $this->getParameter('app_mail');
            $emailManager->send($app_mail, $userAssigned->getEmailCanonical(), $subject, '@User/Email/zZeend.twig', $data);

            $zZeendId = $zZeend->getId();
            $title = $translator->trans("New zZeend (n?? %zZeendId%)", ['%zZeendId%' => $zZeendId], null, $translateTo);

            //send notification
            $pushNotificationManager = $this->get('ionicapi.push.notification.manager');
            $data = array("type" => 1,
                "zZeend" => $zZeend);
            $pushNotificationManager->sendNotification($userAssigned, $title, $subject, $data, $currenetUser->getPhoto() !== null ? $currenetUser->getPhoto()->getFilePath() : null);

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

        $stripeSecretKey = $this->getParameter('api_keys')['stripe-secret-key'];
        \Stripe\Stripe::setApiKey($stripeSecretKey);

        $data = $request->getContent();
        $data = json_decode($data, true);

        $payment_type = $data['payment_type'];
        $payment_methode_id = $data['payment_methode_id'];
        $zZeend_id = $data['zZeend_id'];

        $currentUser = $this->getUser();

        $paymentType = $this->getDoctrine()->getRepository(PaymentType::class)->find($payment_type);

        if ($paymentType) {

            $paymentmethod = $this->getDoctrine()->getRepository(PaymentMethod::class)->find($payment_methode_id);

            if ($paymentmethod) {

                $zZeend = $this->getDoctrine()->getRepository(Zzeend::class)->find($zZeend_id);

                if ($zZeend) {

                    $zZeendCost = $zZeend->getCost();

                    $stripe = new \Stripe\StripeClient($stripeSecretKey);

                    //charge user card and share money between stripe and zZeend account nd keep the rest to the custommer

                    try {

                        $application_fee_amount = 0;
                        $em = $this->getDoctrine()->getRepository(ZzeendService::class);
                        $qb = $em->GetQueryBuilder();
                        $qb = $em->WhereZzeendCostIsBetween($qb, $zZeendCost);

                        $zZeendService = $qb->getQuery()->getOneOrNullResult();

                        if ($zZeendService !== null) {
                            $application_fee_amount = $zZeendService->getApplicationFees();
                        } else {
                            if ($zZeendCost >= 5000) {

                                $em = $this->getDoctrine()->getRepository(ZzeendService::class);
                                $qb = $em->GetQueryBuilder();
                                $qb = $em->WhereZzeendCostIsOverThan5000($qb, 5000);

                                $zZeendService = $qb->getQuery()->getOneOrNullResult();
                                $application_fee_amount = $zZeendService->getApplicationFees();
                            }
                        }

                        if ($application_fee_amount > 0) {


                            $mainZzeendUser = $zZeend->getUser();
                            $stripeConnectedAccount = $this->getDoctrine()->getRepository(StripeConnectAccount::class)->findOneBy(['user' => $mainZzeendUser]);

                            if ($stripeConnectedAccount !== null) {
                                $serviceOwnerStripeAccountId = $stripeConnectedAccount->getStripeAccountId();

                                $payment_intent = \Stripe\PaymentIntent::create([
                                    'payment_method_types' => ['card'],
                                    'amount' => ($zZeendCost * 100),
                                    'currency' => strtolower($this->get('ionicapi.zzeendPointGeneratorManager')->getCountryCurrency($mainZzeendUser->getCountryCode())),
                                    'payment_method' => $paymentmethod->getStripePaymentMethodId(),
                                    'customer' => $paymentmethod->getCustomerId(),
                                    'application_fee_amount' => $application_fee_amount,
                                    'transfer_data' => [
                                        'destination' => $serviceOwnerStripeAccountId,
                                    ],
                                    'setup_future_usage' => 'off_session',
                                ]);


                                if ($payment_intent !== null) {

                                    $paymentIntentId = $payment_intent->id;
                                    $stripe->paymentIntents->confirm(
                                        $paymentIntentId
                                    );

                                    $entityManager = $this->getDoctrine()->getManager();
                                    $transaction = new Transaction();
                                    $transaction->setUser($currentUser);
                                    $transaction->setPaymentType($paymentType);
                                    $transaction->setPaymentMethod($paymentmethod);
                                    $transaction->setPaymentIntentId($paymentIntentId);
                                    $transaction->setCreatedAtAutomatically();

                                    $entityManager->persist($transaction);

                                    $zZeend->setTransaction($transaction);
                                    $zZeend->setUpdatedAtAutomatically();

                                    $entityManager->persist($zZeend);
                                    $entityManager->flush();

                                    $createNotificationManager = $this->get("ionicapi.NotificationManager");
                                    $createNotificationManager->newNotification(2, $zZeend->getId());

                                    $serviceOwner = $zZeend->getUser();

                                    $translator = $this->get('translator');
                                    $translateTo = 'en';
                                    if ($serviceOwner->getLang() !== '') {
                                        $translateTo = $serviceOwner->getLang();
                                    }

                                    $zZeendId = $zZeend->getId();
                                    $title = $translator->trans("zZeend paid (n?? %zZeendId%)", ['%zZeendId%' => $zZeendId], null, $translateTo);

                                    $userFullname = $currentUser->getFullname();

                                    $subject = $translator->trans("%userFullname% just made a payment.", ['%userFullname%' => $zZeendId], null, $translateTo);
                                    //send notification
                                    $pushNotificationManager = $this->get('ionicapi.push.notification.manager');
                                    $data = array("type" => 5,
                                        "zZeend" => $zZeend);
                                    $pushNotificationManager->sendNotification($serviceOwner, $title, $subject, $data, $currentUser->getPhoto() !== null ? $currentUser->getPhoto()->getFilePath() : null);

                                    $response = array("code" => "payment_success");

                                } else {
                                    $response = array("code" => "action_not_allowed");
                                }

                            } else {
                                $response = array("code" => "action_not_allowed");
                            }

                        } else {
                            $response = array("code" => "action_not_allowed");
                        }

                    } catch (\Stripe\Exception\CardException $e) {
                        return new JsonResponse(array("code" => $e->getError()->code, "payment_intent_id" => $e->getError()->payment_intent->id));
                    }

                } else {
                    $response = array("code" => "action_not_allowed");
                }


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

        $stripeSecretKey = $this->getParameter('api_keys')['stripe-secret-key'];
        \Stripe\Stripe::setApiKey($stripeSecretKey);
        $payment_intent = null;
        $stripeSecretKey = $this->getParameter('api_keys')['stripe-secret-key'];

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

            $application_fee_amount = 0;
            $zZeendPoints = 0;

            $em = $this->getDoctrine()->getRepository(ZzeendService::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->WhereZzeendCostIsBetween($qb, $zZeendCost);

            $zZeendService = $qb->getQuery()->getOneOrNullResult();

            if ($zZeendService !== null) {
                $zZeendPoints = $zZeendService->getZzeendPoint();
                $application_fee_amount = $zZeendService->getApplicationFees();
            } else {
                if ($zZeendCost >= 5000) {

                    $em = $this->getDoctrine()->getRepository(ZzeendService::class);
                    $qb = $em->GetQueryBuilder();
                    $qb = $em->WhereZzeendCostIsOverThan5000($qb, 5000);

                    $zZeendService = $qb->getQuery()->getOneOrNullResult();
                    $zZeendPoints = $zZeendService->getZzeendPoint();
                    $application_fee_amount = $zZeendService->getApplicationFees();
                }
            }

            if ($zZeendPoints > 0) {

                $stripeConnectedAccount = $this->getDoctrine()->getRepository(StripeConnectAccount::class)->findOneBy(['user' => $mainZzeendUser]);


                if ($stripeConnectedAccount !== null) {
                    $serviceOwnerStripeAccountId = $stripeConnectedAccount->getStripeAccountId();

                    $stripe = new \Stripe\StripeClient($stripeSecretKey);

                    try {

                        //make payout to the user
                        $payout = \Stripe\Payout::create([
                            'amount' => ($zZeendCost * 100) - $application_fee_amount,
                            'currency' => strtolower($this->get('ionicapi.zzeendPointGeneratorManager')->getCountryCurrency($mainZzeendUser->getCountryCode())),
                        ], [
                            'stripe_account' => $serviceOwnerStripeAccountId,
                        ]);

                        //create a payout webhook event and once the payout is done, the program does some instructions

                        $stripe = new \Stripe\StripeClient($stripeSecretKey);

                        $baseUrl = $this->getParameter('baseUrl');
                        $stripe->webhookEndpoints->create([
                            'url' => $baseUrl . '/auth/payout/' . $zZeend_id,
                            'enabled_events' => [
                                'payout.paid'
                            ]
                        ]);


                        if ($payout !== null) {

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

                            //create zZeend points each time a zZeend is finalize

                            for ($zp = 0; $zp < $zZeendPoints; $zp++) {

                                $zZeendPoint = new ZzeendPoint();
                                $zzeendPointGeneratorManager = $this->get('ionicapi.zzeendPointGeneratorManager');
                                $zZeendPoint->setZzeendPoint($zzeendPointGeneratorManager->createZzeendPoint());
                                $zZeendPoint->setUser($mainZzeendUser);
                                $zZeendPoint->setZzeend($zZeend);
                                $zZeendPoint->setCreatedAtAutomatically();

                                $entityManager->persist($zZeendPoint);

                            }

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

                            $translator = $this->get('translator');
                            $translateTo = 'en';
                            if ($serviceOwner->getLang() !== '') {
                                $translateTo = $serviceOwner->getLang();
                            }

                            $userFullname = $currentUser->getFullname();

                            $zZeendId = $zZeend->getId();
                            $title = $translator->trans("zZeend finalized (n?? %zZeendId%)", ['%zZeendId%' => $zZeendId], null, $translateTo);

                            $subject = $translator->trans('%userFullname% has finalized this zZeend.', ['%userFullname%' => $userFullname], null, $translateTo);
                            //send notification
                            $pushNotificationManager = $this->get('ionicapi.push.notification.manager');
                            $data = array("type" => 6,
                                "zZeend" => $zZeend);
                            $pushNotificationManager->sendNotification($serviceOwner, $title, $subject, $data, $currentUser->getPhoto() !== null ? $currentUser->getPhoto()->getFilePath() : null);


                            $response = array("code" => "zZeend_done");

                        }

                    } catch (\Stripe\Exception\CardException $ex) {
                        return new JsonResponse(array("code" => $ex->getError()->code));
                    }

                }

            } else {
                $response = array("code" => "action_not_allowed");
            }


        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($payment_intent);
    }

    public function cancelAction(Request $request)
    {
        $response = array();

        $stripeSecretKey = $this->getParameter('api_keys')['stripe-secret-key'];

        $data = $request->getContent();
        $data = json_decode($data, true);

        $currentUser = $this->getUser();

        $zZeendId = $data['zZeendId'];

        $zZeend = $this->getDoctrine()->getRepository(Zzeend::class)->find($zZeendId);

        if ($zZeend !== null) {

            $entityManager = $this->getDoctrine()->getManager();

            $zZeend->setCanceled(true);

            $zZeendCost = $zZeend->getCost();

            $entityManager->persist($zZeend);
            $entityManager->flush();

            $application_fee_amount = 0;
            $em = $this->getDoctrine()->getRepository(ZzeendService::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->WhereZzeendCostIsBetween($qb, $zZeendCost);

            $zZeendService = $qb->getQuery()->getOneOrNullResult();

            if ($zZeendService !== null) {
                $application_fee_amount = $zZeendService->getApplicationFees();
            } else {
                if ($zZeendCost >= 5000) {

                    $em = $this->getDoctrine()->getRepository(ZzeendService::class);
                    $qb = $em->GetQueryBuilder();
                    $qb = $em->WhereZzeendCostIsOverThan5000($qb, 5000);

                    $zZeendService = $qb->getQuery()->getOneOrNullResult();
                    $application_fee_amount = $zZeendService->getApplicationFees();
                }
            }

            $transaction = $zZeend->getTransaction();

            $paymentIntentId = $transaction->getPaymentIntentId();

            \Stripe\Stripe::setApiKey($stripeSecretKey);

            // Make the refund automatically

            $re = \Stripe\Refund::create([
                'amount' => ($zZeendCost * 100) - $application_fee_amount,
                'payment_intent' => $paymentIntentId,
            ]);


            $createNotificationManager = $this->get("ionicapi.NotificationManager");
            $createNotificationManager->newNotification(4, $zZeend->getId());

            $serviceSeeker = $zZeend->getUserAssigned();

            $translator = $this->get('translator');

            $translateTo = 'en';
            if ($serviceSeeker->getLang() !== '') {
                $translateTo = $serviceSeeker->getLang();
            }

            $userFullname = $currentUser->getFullname();

            $subject = $translator->trans('%userFullname% has canceled this zZeend.', ['%userFullname%' => $userFullname], null, $translateTo);


            $zZeendId = $zZeend->getId();
            $title = $translator->trans("zZeend canceled (n?? %zZeendId%)", ['%zZeendId%' => $zZeendId], null, $translateTo);

            //send notification
            $pushNotificationManager = $this->get('ionicapi.push.notification.manager');
            $data = array("type" => 7,
                "zZeend" => $zZeend);
            $pushNotificationManager->sendNotification($serviceSeeker, $title, $subject, $data, $currentUser->getPhoto() !== null ? $currentUser->getPhoto()->getFilePath() : null);


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

                $translator = $this->get('translator');
                $translateTo = 'en';

                if ($serviceSeeker->getLang() !== '') {
                    $translateTo = $serviceSeeker->getLang();
                }

                $userFullname = $currentUser->getFullname();


                $subject = $translator->trans('%userFullname% edited this zZeend.', ['%userFullname%' => $userFullname], null, $translateTo);

                $zZeendId = $zZeend->getId();
                $title = $translator->trans("zZeend update (n?? %zZeendId%)", ['%zZeendId%' => $zZeendId], null, $translateTo);

                //send notification
                $pushNotificationManager = $this->get('ionicapi.push.notification.manager');
                $data = array("type" => 8,
                    "zZeend" => $zZeend);
                $pushNotificationManager->sendNotification($serviceSeeker, $title, $subject, $data, $currentUser->getPhoto() !== null ? $currentUser->getPhoto()->getFilePath() : null);


            }


            $entityManager->flush();


            $response = array("updated" => $update);

        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

    public function statisticsAction()
    {

        $currentUser = $this->getUser();

        $totalZzeendPaid = 0;


        $em = $this->getDoctrine()->getRepository(Zzeend::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->GetTotalZzeendCreated($qb, $currentUser);
        $totalZzeendPaid = $qb->getQuery()->getSingleScalarResult();

        $qb = $em->GetQueryBuilder();
        $qb = $em->GetTotalBalancePaid($qb, $currentUser);
        $balanceArray = $qb->getQuery()->getResult();

        $totalBalance = 0;
        if (count($balanceArray) > 0) {
            $totalBalance = $balanceArray[0]['cost'];
        }

        return new JsonResponse(array("nbZzeend" => intval($totalZzeendPaid), "totalBalance" => floatval($totalBalance)));

    }

    public function updatezZeendStatusAction()
    {

        $stateChanged = false;
        $entityManager = $this->getDoctrine()->getManager();
        $zZeends = $this->getDoctrine()->getRepository(Zzeend::class)->findAll();

        $allUsers = [];

        $assocArray = [];

        for ($i = 0; $i < count($zZeends); $i++) {

            $zZeend = $zZeends[$i];

            $mainZzeendUser = $zZeend->getUser();
            $userAssigned = $zZeend->getUserAssigned();

            $status = $zZeend->getStatus();

            $paymentLimitDate = $zZeend->getPaymentLimitDate();

            $canceled = $zZeend->getCanceled();


            $done = $zZeend->getDone();

            $titles = array();
            $subjects = array();

            //if today's date and time is greater that the date the zZeend suppose to be payed and done still false and the zZeend is till active, not canceled,
            if (new \DateTime() > $paymentLimitDate and !$done and !$canceled and $status->getId() == 1) {

                //turn the zZeend to incomplete
                $zZeendStatus = $this->getDoctrine()->getRepository(ZzeendStatus::class)->find(2);
                $zZeend->setStatus($zZeendStatus);

                $entityManager->persist($zZeend);
                $stateChanged = true;

                $assocArray[$mainZzeendUser->getEMail()] = $mainZzeendUser;

            }

        }

        if ($stateChanged) {
            $entityManager->flush();
        }

        $translator = $this->get('translator');

        foreach ($assocArray as $user) {
            array_push($allUsers, $user);

            $translateTo = $user->getLang();

            array_push($titles, $translator->trans('zZeend(s) due to', [], null, $translateTo));
            array_push($subjects, $translator->trans('You have some incompleted zZeends. You need to check them out !!!', [], null, $translateTo));
        }

        $title = $translator->trans('zZeend(s) due to', [], null, 'en');

        $subject = $translator->trans('You have some incompleted zZeends. You need to check them out !!!', [], null, 'en');

        //send notifications

        //send notification
        $pushNotificationManager = $this->get('ionicapi.push.notification.manager');
        $data = array("type" => 19,
            "zZeend" => $zZeend);
        $pushNotificationManager->sendGroupNotification($allUsers, $title, $subject, null, null);


        return new JsonResponse(array("code" => "done"));

    }


}