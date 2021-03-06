<?php

namespace UserBundle\Controller;

use ApiBundle\Entity\StripeConnectAccount;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use UserBundle\Entity\AccountVerification;
use UserBundle\Entity\PasswordForgot;
use UserBundle\Entity\User;

class UserController extends Controller
{
    public function getCurrentUserAction()
    {
        $user = $this->getUser()->jsonSerialize();
        return new JsonResponse($user);
    }

    public function newUserAction($email, $password, $fullname, $accountType, $image, $country, $city, $address, $zipCode, $phoneNumber, $jobTitle, $jobDescription, $spokenLanguages, $subLocality, $latitude, $longitude, $subAdministrativeArea, $administrativeArea, $countryCode, $lang){

        $userManager = $this->getDoctrine()->getManager();
        $response = array();

        $em = $this->getDoctrine()->getRepository(User::class);
        $email_exist = $em->FindByEmail($email);

        if($email_exist){
            $response = array('code' => 'auth/email_in_used');
            return new JsonResponse($response);
        }

        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setfullname($fullname);
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setEmailCanonical($email);
        $user->setEnabled(0);
        $user->setLang($lang);
        $user->setPlainPassword($password);

        if($accountType == 0){
            $user->setRoles(['ROLE_SEEKER']);
        }else if($accountType == 1){
            $user->setRoles(['ROLE_OWNER']);
        }
        $user->setPhoto($image);
        $user->setCountry($country);
        $user->setCity($city);
        $user->setAddress($address);
        $user->setZipCode($zipCode);
        $user->setPhoneNumber($phoneNumber);
        $user->setJobTitle($jobTitle);
        $user->setJobDescription($jobDescription);
        $user->setZzeendScore(0);
        $user->setSpokenLanguages($spokenLanguages);
        $user->setSubLocality($subLocality);
        $user->setLatitude($latitude);
        $user->setLongitude($longitude);
        $user->setSubAdministrativeArea($subAdministrativeArea);
        $user->setAdministrativeArea($administrativeArea);
        $user->setCountryCode($countryCode);
        $user->setUpdatedAtAutomatically();
        $user->setVisibility(true);
        $user->setMainVisibility(true);
        $user->setCreatedAtAutomatically();
        $user->setUpdatedAtAutomatically();

        $userManager->updateUser($user);

        //create a strip connect account if user is service owner

        if($accountType == 1){
            $this->createStripeUserConnectedAccount($user);
        }


        //generate a codeGen for email verification
        $codeGen = $this->get('ionicapi.tokenGeneratorManager')->createToken();

        //save codeGen
        $entityManager = $this->getDoctrine()->getManager();
        $accountVerification = new AccountVerification();
        $accountVerification->setUser($user);
        $accountVerification->setCodeGen($codeGen);

        $entityManager->persist($accountVerification);
        $entityManager->flush();

        $translateTo = 'en';
        if($user->getLang() !== ''){
            $translateTo = $user->getLang();
        }

        $data = array(
            'name' => $user->getFullname(),
            'codeGen' => $codeGen,
            'baseUrl' => $this->getParameter("baseUrl"),
            'lang' => $translateTo
        );

        $emailManager = $this->get('ionicapi.emailManager');
        $app_mail = $this->getParameter('app_mail');
        $emailManager->send($app_mail, $user->getEmailCanonical(), 'Email verification', '@User/Email/emailVerification.twig', $data);


        $response = array('code' => 'auth/registered', 'relatedId' => $user->getId());
        return new JsonResponse($response);
    }


    public function getUserByEmailAndPasswordAction($email, $password){
        $response = array();
        $em = $this->getDoctrine()->getRepository(User::class);
        $user = $em->FindByEmailAndPassword($email, $password);

        if($user->isGranted() and $user->isEnabled()) {
            $response = array('user' => $user);
            return new JsonResponse($response);
        }else{
            //generate a codeGen for email verification
            $codeGen = $this->get('ionicapi.tokenGeneratorManager')->createToken();

            //save codeGen
            $entityManager = $this->getDoctrine()->getManager();
            $accountVerification = new AccountVerification();
            $accountVerification->setUser($user);
            $accountVerification->setCodeGen($codeGen);

            $entityManager->persist($accountVerification);
            $entityManager->flush();

            $translateTo = 'en';
            if($user->getLang() !== ''){
                $translateTo = $user->getLang();
            }

            $data = array(
                          'name' => $user->getFullname(),
                          'codeGen' => $codeGen,
                          'baseUrl' => $this->getParameter("baseUrl"),
                          'lang' => $translateTo
            );

            $emailManager = $this->get('ionicapi.emailManager');
            $app_mail = $this->getParameter('app_mail');
            $emailManager->send($app_mail, $user->getEmailCanonical(), 'Email verification', '@User/Email/emailVerification.twig', $data);

            $response = array('code' => 'auth/email_verification_sent');
            return new JsonResponse($response);
        }
    }


    public function sendPasswordForgotMailAction($email){
        $response = array();

        $em = $this->getDoctrine()->getRepository(User::class);
        $user = $em->FindByEmail($email);
        if($user !== null) {

            //generate a codeGen for email verification
            $codeGen = $this->get('ionicapi.tokenGeneratorManager')->createToken();

            //save codeGen
            $entityManager = $this->getDoctrine()->getManager();
            $passwordForgot = new PasswordForgot();
            $passwordForgot->setUser($user);
            $passwordForgot->setCreatedAtAutomatically();
            $passwordForgot->setCodeGen($codeGen);

            $entityManager->persist($passwordForgot);
            $entityManager->flush();

            $translateTo = 'en';
            if($user->getLang() !== ''){
                $translateTo = $user->getLang();
            }

            $data = array(
                'name' => $user->getFullname(),
                'codeGen' => $codeGen,
                'baseUrl' => $this->getParameter("baseUrl"),
                'lang' => $translateTo
            );

            $emailManager = $this->get('ionicapi.emailManager');
            $app_mail = $this->getParameter('app_mail');
            $emailManager->send($app_mail, $user->getEmailCanonical(), 'Password recovery', '@User/Email/passwordForgot.twig', $data);

            $response = array('code' => 'recovery_mail_sent');

        }else{
            $response = array('code' => 'auth/user_not_found');
        }
        return new JsonResponse($response);
    }

    public function resetPasswordAction($codeGen, $newPassword){
        $response = array();

        $translator = $this->get('translator');
        $entityManager = $this->getDoctrine()->getRepository(PasswordForgot::class);
        $passwordForgotObject = $entityManager->findOneBy(["codeGen" => $codeGen]);
        if($passwordForgotObject !== null) {
            $userId = $passwordForgotObject->getUser()->getId();

            $userManager = $this->get('fos_user.user_manager');
            $user =  $userManager->findUserBy(array('id'=> $userId));
            $user->setPlainPassword($newPassword);
            $passwordForgotObject->setUpdatedAtAutomatically();

            $userManager->updateUser($user);

            $response = array('code' => 'auth/password_recovered');
        }else{
            $message = $translator->trans('Invalid Url. Try to make a new password request.');
            $response = array('code' => 'auth/codeGen_error', 'message' => $message);
        }

        return new JsonResponse($response);
    }

    public function enableAccountAction($codeGen){
        $response = array();
        if(isset($codeGen)){
            $entityManager = $this->getDoctrine()->getRepository(AccountVerification::class);
            $accountVerificationObject = $entityManager->findOneBy(["codeGen" => $codeGen]);
            if($accountVerificationObject !== null){
                $userId = $accountVerificationObject->getUser()->getId();

                $userManager = $this->getDoctrine()->getManager();
                $user =  $this->getDoctrine()->getRepository(User::class)->findOneBy(array('id'=> $userId));
                $user->setEnabled(1);
                $userManager->persist($user);
                $userManager->flush();

                $response = $this->render("@Web/account-enebaled.html.twig",
                ["code" => "auth/account_enabled"]);
            }else{
                $response = $this->render("@Web/account-enebaled.html.twig",
                    ["code" => "auth/codeGen_error"]);
            }
        }else{
            $response = $this->render("@Web/account-enebaled.html.twig",
                ["code" => "auth/codeGen_error"]);
        }


        return $response;
    }

    public function createStripeUserConnectedAccount($user){

        $response = array();
        $stripeSecretKey = $this->getParameter('api_keys')['stripe-secret-key'];

        $countryCode = $user->getCountryCode();

        \Stripe\Stripe::setApiKey($stripeSecretKey);

        $account = \Stripe\Account::create([
            'country' => $countryCode,
            'email' => $user->getEmail(),
            'type' => 'express',
        ]);


        if($account !== null AND  count($account) > 0){

            $stripeConnectedAccountId = $account['id'];

            $entityManager = $this->getDoctrine()->getManager();
            $stripeConnectedAccount = new StripeConnectAccount();
            $stripeConnectedAccount->setUser($user);
            $stripeConnectedAccount->setStripeAccountId($stripeConnectedAccountId);

            $entityManager->persist($stripeConnectedAccount);
            $entityManager->flush();

        }else{

            return new JsonResponse(array("code" => "action_not_allowed"));

        }


    }

}
