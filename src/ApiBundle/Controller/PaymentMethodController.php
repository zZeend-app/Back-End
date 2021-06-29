<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\PaymentMethod;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PaymentMethodController extends Controller
{

    public function addPaymentMethodAction(Request $request)
    {
        $response = array();

        $currentUser = $this->getUser();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $card = $data['card'];
        $last_four_digit = $data['last_four_digit'];
        $expiration_date = $data['expiration_date'];
        $csv = $data['csv'];

        $paymentMethod = $this->getDoctrine()->getRepository(PaymentMethod::class)->findOneBy([
            "card" => $card,
            "lastFourDigit" => $last_four_digit,
            "expirationDate" => $expiration_date,
            "csv" => $csv
        ]);

        if ($paymentMethod == null) {

            $paymentMedthods = $this->getDoctrine()->getRepository(PaymentMethod::class)->findBy([
                "user" => $currentUser
            ]);

            $main = false;

            if (count($paymentMedthods) == 0) {
                $main = true;
            }

            if (count($paymentMedthods) > 2) {

                $response = array("code" => "maximum_limit_exceeded");
                return new JsonResponse($response);
            }

            $entityManager = $this->getDoctrine()->getManager();

            $paymentMethod = new PaymentMethod();
            $paymentMethod->setUser($currentUser);
            $paymentMethod->setCard($card);
            $paymentMethod->setLastFourDigit($last_four_digit);
            $paymentMethod->setExpirationDate($expiration_date);
            $paymentMethod->setCsv($csv);
            $paymentMethod->setMain($main);
            $paymentMethod->setCreatedAtAutomatically();
            $paymentMethod->setUpdatedAtAutomatically();

            $entityManager->persist($paymentMethod);
            $entityManager->flush();


            $response = array("code" => "payment_method_added");
        } else {
            $response = array("code" => "already_added");
        }

        return new JsonResponse($response);

    }

    public function updatePaymentMethodAction(Request $request)
    {
        $response = array();
        $updated = array();

        $currentUser = $this->getUser();

        $data = $request->getContent();

        $data = json_decode($data, true);

        $payment_method_id = $data['payment_method_id'];
        $card = $data['card'];
        $last_four_digit = $data['last_four_digit'];
        $expiration_date = $data['expiration_date'];
        $csv = $data['csv'];

        $paymentMethod = $this->getDoctrine()->getRepository(PaymentMethod::class)->findOneBy(["user" => $currentUser, "id" => $payment_method_id]);

        if ($paymentMethod) {
            if ($card !== '' and $card !== $paymentMethod->getCard()) {
                $paymentMethod->setCard($card);
                $updated[] = "card";
            }

            if ($last_four_digit !== '' and $last_four_digit !== $paymentMethod->getLastFourDigit()) {
                $paymentMethod->setLastFourDigit($last_four_digit);
                $updated[] = "last_four_digit";
            }

            if ($expiration_date !== '' and $expiration_date !== $paymentMethod->getExpirationDate()) {
                $paymentMethod->setExpirationDate($expiration_date);
                $updated[] = "expiration_date";
            }

            if ($csv !== '' and $csv !== $paymentMethod->getCsv()) {
                $paymentMethod->setCsv($csv);
                $updated[] = "csv";
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($paymentMethod);
            $entityManager->flush();

            $response = array("updated" => $updated);
        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

    public function deletePaymentMethodAction(Request $request)
    {
        $response = array();

        $currentUser = $this->getUser();

        $data = $request->getContent();

        $data = json_decode($data, true);

        $payment_method_id = $data['payment_method_id'];

        $entityManager = $this->getDoctrine()->getManager();
        $paymentMethod = $entityManager->getRepository(PaymentMethod::class)->findOneBy(["user" => $currentUser, "id" => $payment_method_id]);

        if ($paymentMethod) {
            $main = $paymentMethod->getMain();
            $entityManager->remove($paymentMethod);
            $entityManager->flush();

            if($main){
                $em = $this->getDoctrine()->getRepository(PaymentMethod::class);
                $qb =  $em->GetQueryBuilder();
                $qb = $em->WhereUser($qb, $currentUser);
                $qb = $em->OrderById($qb);
                $paymentMethods = $qb->getQuery()->getResult();

                if(count($paymentMethods) > 0){

                    $entityManager = $this->getDoctrine()->getManager();
                    $paymentMethods[0]->setMain(true);
                    $entityManager->persist($paymentMethods[0]);
                    $entityManager->flush();
                }
            }

            $response = array("code" => "payment_method_deleted");
        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

    public function getAllPaymentMethodsAction()
    {
        $response = array();

        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getRepository(PaymentMethod::class);
        $qb =  $em->GetQueryBuilder();
        $qb = $em->WhereUser($qb, $currentUser);
        $qb = $em->OrderByMain($qb);


        $response = $qb->getQuery()->getResult();

        return new JsonResponse($response);
    }

    public function applyStateAction(Request $request){
        $response = array();

        $currentUser = $this->getUser();

        $data = $request->getContent();

        $data = json_decode($data, true);

        $payment_method_id = $data['payment_method_id'];

        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'UPDATE payment_method SET main = "0" where user_id = :userId;';

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('userId', $currentUser->getId());
        $statement->execute();

        $paymentMethod = $this->getDoctrine()->getRepository(PaymentMethod::class)->find($payment_method_id);

        if($paymentMethod){

           $entityManager = $this->getDoctrine()->getManager();

            $paymentMethod->setMain(true);
            $entityManager->persist($paymentMethod);
            $entityManager->flush();

            $response = array("code" => "main_applied");

        }else{
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

}