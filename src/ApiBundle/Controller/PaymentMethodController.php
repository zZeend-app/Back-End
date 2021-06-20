<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\PaymentMethod;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PaymentMethodController extends Controller
{

    public function addPaymentMethodAction(Request $request){
        $response = array();

        $currentUser = $this->getUser();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $card = $data['card'];
        $last_four_digit = $data['last_four_digit'];
        $expiration_date = $data['expiration_date'];
        $csv = $data['csv'];
        $main = $data['main'];

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

        return new JsonResponse($response);

    }

    public function updatePaymentMethodAction(Request $request){
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
        $main = $data['main'];

        $paymentMethod = $this->getDoctrine()->getRepository(PaymentMethod::class)->findOneBy(["user" => $currentUser, "id" => $payment_method_id]);

        if($paymentMethod){
            if($card !== '' AND $card !== $paymentMethod->getCard()){
                $paymentMethod->setCard($card);
                $updated[] = "card";
            }

            if($last_four_digit !== '' AND $last_four_digit !== $paymentMethod->getLastFourDigit()){
                $paymentMethod->setLastFourDigit($last_four_digit);
                $updated[] = "last_four_digit";
            }

            if($expiration_date !== '' AND $expiration_date !== $paymentMethod->getExpirationDate()){
                $paymentMethod->setExpirationDate($expiration_date);
                $updated[] = "expiration_date";
            }

            if($csv !== '' AND $csv !== $paymentMethod->getCsv()){
                $paymentMethod->setCsv($csv);
                $updated[] = "csv";
            }

            if($main !== '' AND $main !== $paymentMethod->getMain()){
                $paymentMethod->setMain($main);
                $updated[] = "main";
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($paymentMethod);
            $entityManager->flush();

            $response = array("updated" => $updated);
        }else{
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

    public function deletePaymentMethodAction(Request $request){
        $response = array();

        $currentUser = $this->getUser();

        $data = $request->getContent();

        $data = json_decode($data, true);

        $payment_method_id = $data['payment_method_id'];

        $entityManager = $this->getDoctrine()->getManager();
        $paymentMethod = $entityManager->getRepository(PaymentMethod::class)->findOneBy(["user" => $currentUser, "id" => $payment_method_id]);

        if($paymentMethod){
            $entityManager->remove($paymentMethod);
            $entityManager->flush();
            $response = array("code" => "payment_method_deleted");
        }else{
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

}