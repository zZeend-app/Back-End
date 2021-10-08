<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\AccountLink;
use ApiBundle\Entity\PaymentMethod;
use ApiBundle\Entity\StripeConnectAccount;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PaymentMethodController extends Controller
{

    public function addPaymentMethodAction(Request $request)
    {
        $response = array();


        $stripeSecretKey = $this->getParameter('api_keys')['stripe-secret-key'];

        $currentUser = $this->getUser();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $brand = $data['brand'];
        $card_number = $data['card_number'];
        $last_four_digit = $data['last_four_digit'];
        $exp_month = $data['exp_month'];
        $exp_year = $data['exp_year'];
        $funding = $data['funding'];
        $csv = $data['csv'];
        $token = $data['token'];

        $paymentMethod = $this->getDoctrine()->getRepository(PaymentMethod::class)->findOneBy([
            "brand" => $brand,
            "lastFourDigit" => $last_four_digit,
            "expMonth" => $exp_month,
            "expYear" => $exp_year,
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


            \Stripe\Stripe::setApiKey($stripeSecretKey);

                //add payment method to stripe

            try{

                $paymentMethod = \Stripe\PaymentMethod::create([
                    'type' => 'card',
                    'card' => [
                        'number' => $card_number,
                        'exp_month' => $exp_month,
                        'exp_year' => $exp_year,
                        'cvc' => $csv
                    ]
                ]);


                if($paymentMethod !== null){
                    $stripePaymentMethodId = $paymentMethod->id;

                    //create a customer
                    $customer = \Stripe\Customer::create([
                        'source' => $token,
                        'email' => $currentUser->getEmail()
                    ]);

                    if($customer !== null){

                        //attache a payment method to a customer
                        $stripe = new \Stripe\StripeClient($stripeSecretKey);

                        $stripe->paymentMethods->attach(
                            $stripePaymentMethodId,
                            ['customer' => $customer->id]
                        );

                        $entityManager = $this->getDoctrine()->getManager();

                        $paymentMethod = new PaymentMethod();
                        $paymentMethod->setUser($currentUser);
                        $paymentMethod->setBrand($brand);
                        $paymentMethod->setToken($token);
                        $paymentMethod->setLastFourDigit($last_four_digit);
                        $paymentMethod->setExpMonth($exp_month);
                        $paymentMethod->setExpYear($exp_year);
                        $paymentMethod->setCsv($csv);
                        $paymentMethod->setActive(true);
                        $paymentMethod->setStripePaymentMethodId($stripePaymentMethodId);
                        $paymentMethod->setFunding($funding);
                        $paymentMethod->setMain($main);
                        $paymentMethod->setCustomerId($customer->id);
                        $paymentMethod->setCreatedAtAutomatically();
                        $paymentMethod->setUpdatedAtAutomatically();

                        $entityManager->persist($paymentMethod);
                        $entityManager->flush();

                        $response = array("code" => "payment_method_added");

                    }else{
                        $response = array("code" => "error_occurred");
                    }

                }else{
                    $response = array("code" => "error_occurred");
                }

            }catch(\Stripe\Exception\CardException $e){
                return new JsonResponse(array("code" => $e->getError()->code));
            }

        } else {
            //if this payment method is active and the user still want to add it again
            if($paymentMethod->getActive()){
                $response = array("code" => "already_added");
            }else{

                //if this payment method is not active, then active it

                $entityManager = $this->getDoctrine()->getManager();
                $paymentMethod->setActive(true);

                $entityManager->persist($paymentMethod);
                $entityManager->flush();

                $response = array("code" => "payment_method_added");

            }

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

        $brand = $data['brand'];
        $last_four_digit = $data['last_four_digit'];
        $exp_month = $data['exp_month'];
        $exp_year = $data['exp_year'];
        $funding = $data['funding'];
        $csv = $data['csv'];
        $token = $data['token'];
        $payment_method_id = $data['payment_method_id'];

        $paymentMethod = $this->getDoctrine()->getRepository(PaymentMethod::class)->findOneBy(["user" => $currentUser, "id" => $payment_method_id]);

        if ($paymentMethod) {
            if ($brand !== '' and $brand !== $paymentMethod->getBrand()) {
                $paymentMethod->setCard($brand);
                $updated[] = "card";
            }

            if ($token !== '' and $token !== $paymentMethod->getToken()) {
                $paymentMethod->setToken($token);
                $updated[] = "token";
            }

            if ($last_four_digit !== '' and $last_four_digit !== $paymentMethod->getLastFourDigit()) {
                $paymentMethod->setLastFourDigit($last_four_digit);
                $updated[] = "last_four_digit";
            }

            if ($exp_month !== '' and $exp_month !== $paymentMethod->getExpMonth()) {
                $paymentMethod->setExpirationDate($exp_month);
                $updated[] = "expiration_month";
            }

            if ($exp_year !== '' and $exp_year !== $paymentMethod->getExpYear()) {
                $paymentMethod->setExpirationDate($exp_month);
                $updated[] = "expiration_year";
            }

            if ($csv !== '' and $csv !== $paymentMethod->getCsv()) {
                $paymentMethod->setCsv($csv);
                $updated[] = "csv";
            }

            if ($funding !== '' and $funding !== $paymentMethod->getFunding()) {
                $paymentMethod->setFunding($csv);
                $updated[] = "funding";
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

            //disable this payment method
            $paymentMethod->setActive(false);

            $entityManager->persist($paymentMethod);
            $entityManager->flush();

            //detach payment method from customer

            $stripeSecretKey = $this->getParameter('api_keys')['stripe-secret-key'];


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
        $qb = $em->WhereActive($qb, true);
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

    public function createStripeUserConnectedAccountAction(){

        $response = array();
        $stripeSecretKey = $this->getParameter('api_keys')['stripe-secret-key'];

        $currentUser = $this->getUser();

        $countryCode = $currentUser->getCountryCode();

        \Stripe\Stripe::setApiKey($stripeSecretKey);

        $account = \Stripe\Account::create([
            'country' => $countryCode,
             'email' => $currentUser->getEmail(),
            'type' => 'express',
        ]);


        if($account !== null AND  count($account) > 0){

            $stripeConnectedAccountId = $account['id'];

            $entityManager = $this->getDoctrine()->getManager();
            $stripeConnectedAccount = new StripeConnectAccount();
            $stripeConnectedAccount->setUser($currentUser);
            $stripeConnectedAccount->setStripeAccountId($stripeConnectedAccountId);

            $entityManager->persist($stripeConnectedAccount);
            $entityManager->flush();

            $response = $stripeConnectedAccount;


        }else{

            $response = array("code" => "action_not_allowed");

        }

        return new JsonResponse($response);


    }

    public function getUserStripeConnectedAccountAction(){

        $response = array();
        $currentUser = $this->getUser();

        $stripeConnectedAccount = $this->getDoctrine()->getRepository(StripeConnectAccount::class)->findBy(["user" => $currentUser]);

        if($stripeConnectedAccount !== null){

            $response = $stripeConnectedAccount;

        }else{

            $response = array("code" => "account_not_found");

        }

        return new JsonResponse($response);

    }

    public function createAccountLinkAction(Request $request){

        $response = array();

        $currentUser = $this->getUser();

        $stripeAccount = $this->getDoctrine()->getRepository(StripeConnectAccount::class)->findOneBy(['user' => $currentUser]);

        if($stripeAccount !== null){

            $stripeSecretKey = $this->getParameter('api_keys')['stripe-secret-key'];

            \Stripe\Stripe::setApiKey($stripeSecretKey);

            //generate a codeGen for email verification
            $refreshToken = $this->get('ionicapi.tokenGeneratorManager')->createToken();
            $returnToken = $this->get('ionicapi.tokenGeneratorManager')->createToken();

            $baseUrl = $this->getParameter('baseUrl');

            $refreshUrl = $baseUrl.'/auth/refresh-link/'.$refreshToken;
            $returnUrl = $baseUrl.'/auth/return-link/'.$returnToken;

            $account_links = \Stripe\AccountLink::create([
                'account' => $stripeAccount->getStripeAccountId(),
                'refresh_url' => $refreshUrl,
                'return_url' => $returnUrl,
                'type' => 'account_onboarding',
            ]);

            if($account_links !== null){

                $accountLink = new AccountLink();

                $entityManager = $this->getDoctrine()->getManager();

                $accountLink->setUser($currentUser);
                $accountLink->setRefreshToken($refreshToken);
                $accountLink->setReturnToken($returnToken);
                $accountLink->setCreatedAtAutomatically();

                $entityManager->persist($accountLink);
                $entityManager->flush();

                $response = $account_links;


            }else{
                $response = array("code" => "error_occurred");
            }

        }else{
            $response = array("code" => "action_not_allowed");
        }



        return new JsonResponse($response);

    }

    function getCountryCurrency($countryCode) {
        $country_currency = array(
            'AF' => 'AFN',
            'AL' => 'ALL',
            'DZ' => 'DZD',
            'AS' => 'USD',
            'AD' => 'EUR',
            'AO' => 'AOA',
            'AI' => 'XCD',
            'AQ' => 'XCD',
            'AG' => 'XCD',
            'AR' => 'ARS',
            'AM' => 'AMD',
            'AW' => 'AWG',
            'AU' => 'AUD',
            'AT' => 'EUR',
            'AZ' => 'AZN',
            'BS' => 'BSD',
            'BH' => 'BHD',
            'BD' => 'BDT',
            'BB' => 'BBD',
            'BY' => 'BYR',
            'BE' => 'EUR',
            'BZ' => 'BZD',
            'BJ' => 'XOF',
            'BM' => 'BMD',
            'BT' => 'BTN',
            'BO' => 'BOB',
            'BA' => 'BAM',
            'BW' => 'BWP',
            'BV' => 'NOK',
            'BR' => 'BRL',
            'IO' => 'USD',
            'BN' => 'BND',
            'BG' => 'BGN',
            'BF' => 'XOF',
            'BI' => 'BIF',
            'KH' => 'KHR',
            'CM' => 'XAF',
            'CA' => 'CAD',
            'CV' => 'CVE',
            'KY' => 'KYD',
            'CF' => 'XAF',
            'TD' => 'XAF',
            'CL' => 'CLP',
            'CN' => 'CNY',
            'HK' => 'HKD',
            'CX' => 'AUD',
            'CC' => 'AUD',
            'CO' => 'COP',
            'KM' => 'KMF',
            'CG' => 'XAF',
            'CD' => 'CDF',
            'CK' => 'NZD',
            'CR' => 'CRC',
            'HR' => 'HRK',
            'CU' => 'CUP',
            'CY' => 'EUR',
            'CZ' => 'CZK',
            'DK' => 'DKK',
            'DJ' => 'DJF',
            'DM' => 'XCD',
            'DO' => 'DOP',
            'EC' => 'ECS',
            'EG' => 'EGP',
            'SV' => 'SVC',
            'GQ' => 'XAF',
            'ER' => 'ERN',
            'EE' => 'EUR',
            'ET' => 'ETB',
            'FK' => 'FKP',
            'FO' => 'DKK',
            'FJ' => 'FJD',
            'FI' => 'EUR',
            'FR' => 'EUR',
            'GF' => 'EUR',
            'TF' => 'EUR',
            'GA' => 'XAF',
            'GM' => 'GMD',
            'GE' => 'GEL',
            'DE' => 'EUR',
            'GH' => 'GHS',
            'GI' => 'GIP',
            'GR' => 'EUR',
            'GL' => 'DKK',
            'GD' => 'XCD',
            'GP' => 'EUR',
            'GU' => 'USD',
            'GT' => 'QTQ',
            'GG' => 'GGP',
            'GN' => 'GNF',
            'GW' => 'GWP',
            'GY' => 'GYD',
            'HT' => 'HTG',
            'HM' => 'AUD',
            'HN' => 'HNL',
            'HU' => 'HUF',
            'IS' => 'ISK',
            'IN' => 'INR',
            'ID' => 'IDR',
            'IR' => 'IRR',
            'IQ' => 'IQD',
            'IE' => 'EUR',
            'IM' => 'GBP',
            'IL' => 'ILS',
            'IT' => 'EUR',
            'JM' => 'JMD',
            'JP' => 'JPY',
            'JE' => 'GBP',
            'JO' => 'JOD',
            'KZ' => 'KZT',
            'KE' => 'KES',
            'KI' => 'AUD',
            'KP' => 'KPW',
            'KR' => 'KRW',
            'KW' => 'KWD',
            'KG' => 'KGS',
            'LA' => 'LAK',
            'LV' => 'EUR',
            'LB' => 'LBP',
            'LS' => 'LSL',
            'LR' => 'LRD',
            'LY' => 'LYD',
            'LI' => 'CHF',
            'LT' => 'EUR',
            'LU' => 'EUR',
            'MK' => 'MKD',
            'MG' => 'MGF',
            'MW' => 'MWK',
            'MY' => 'MYR',
            'MV' => 'MVR',
            'ML' => 'XOF',
            'MT' => 'EUR',
            'MH' => 'USD',
            'MQ' => 'EUR',
            'MR' => 'MRO',
            'MU' => 'MUR',
            'YT' => 'EUR',
            'MX' => 'MXN',
            'FM' => 'USD',
            'MD' => 'MDL',
            'MC' => 'EUR',
            'MN' => 'MNT',
            'ME' => 'EUR',
            'MS' => 'XCD',
            'MA' => 'MAD',
            'MZ' => 'MZN',
            'MM' => 'MMK',
            'NA' => 'NAD',
            'NR' => 'AUD',
            'NP' => 'NPR',
            'NL' => 'EUR',
            'AN' => 'ANG',
            'NC' => 'XPF',
            'NZ' => 'NZD',
            'NI' => 'NIO',
            'NE' => 'XOF',
            'NG' => 'NGN',
            'NU' => 'NZD',
            'NF' => 'AUD',
            'MP' => 'USD',
            'NO' => 'NOK',
            'OM' => 'OMR',
            'PK' => 'PKR',
            'PW' => 'USD',
            'PA' => 'PAB',
            'PG' => 'PGK',
            'PY' => 'PYG',
            'PE' => 'PEN',
            'PH' => 'PHP',
            'PN' => 'NZD',
            'PL' => 'PLN',
            'PT' => 'EUR',
            'PR' => 'USD',
            'QA' => 'QAR',
            'RE' => 'EUR',
            'RO' => 'RON',
            'RU' => 'RUB',
            'RW' => 'RWF',
            'SH' => 'SHP',
            'KN' => 'XCD',
            'LC' => 'XCD',
            'PM' => 'EUR',
            'VC' => 'XCD',
            'WS' => 'WST',
            'SM' => 'EUR',
            'ST' => 'STD',
            'SA' => 'SAR',
            'SN' => 'XOF',
            'RS' => 'RSD',
            'SC' => 'SCR',
            'SL' => 'SLL',
            'SG' => 'SGD',
            'SK' => 'EUR',
            'SI' => 'EUR',
            'SB' => 'SBD',
            'SO' => 'SOS',
            'ZA' => 'ZAR',
            'GS' => 'GBP',
            'SS' => 'SSP',
            'ES' => 'EUR',
            'LK' => 'LKR',
            'SD' => 'SDG',
            'SR' => 'SRD',
            'SJ' => 'NOK',
            'SZ' => 'SZL',
            'SE' => 'SEK',
            'CH' => 'CHF',
            'SY' => 'SYP',
            'TW' => 'TWD',
            'TJ' => 'TJS',
            'TZ' => 'TZS',
            'TH' => 'THB',
            'TG' => 'XOF',
            'TK' => 'NZD',
            'TO' => 'TOP',
            'TT' => 'TTD',
            'TN' => 'TND',
            'TR' => 'TRY',
            'TM' => 'TMT',
            'TC' => 'USD',
            'TV' => 'AUD',
            'UG' => 'UGX',
            'UA' => 'UAH',
            'AE' => 'AED',
            'GB' => 'GBP',
            'US' => 'USD',
            'UM' => 'USD',
            'UY' => 'UYU',
            'UZ' => 'UZS',
            'VU' => 'VUV',
            'VE' => 'VEF',
            'VN' => 'VND',
            'VI' => 'USD',
            'WF' => 'XPF',
            'EH' => 'MAD',
            'YE' => 'YER',
            'ZM' => 'ZMW',
            'ZW' => 'ZWD',
        );

        return $country_currency[$countryCode];
    }

    public function createLoginLinkAction(Request $request){

        $stripeSecretKey = $this->getParameter('api_keys')['stripe-secret-key'];

        $currentUser = $this->getUser();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $stripe_account_id = $data['stripe_account_id'];

        $stripe = new \Stripe\StripeClient($stripeSecretKey);
        $loginLinkObject = $stripe->accounts->createLoginLink(
            $stripe_account_id,
            []
        );

        return new JsonResponse($loginLinkObject);

    }



}