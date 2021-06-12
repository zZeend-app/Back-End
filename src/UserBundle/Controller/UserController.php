<?php

namespace UserBundle\Controller;

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

    public function newUserAction($email, $password, $fullname, $accountType, $image, $country, $city, $address, $zipCode, $phoneNumber, $jobTitle, $jobDescription){

        $userManager = $this->get('fos_user.user_manager');
        $response = array();

        $em = $this->getDoctrine()->getRepository(User::class);
        $email_exist = $em->FindByEmail($email);

        if($email_exist){
            $response = array('code' => 'auth/email_in_used');
            return new JsonResponse($response);
        }

        $user = $userManager->createUser();
        $user->setfullname($fullname);
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setEmailCanonical($email);
        $user->setEnabled(0);
        $user->setPlainPassword($password);

        if($accountType == 0){
            $user->setRoles(['ROLE_SEEKER']);
        }else if($accountType == 1){
            $user->setRoles(['ROLE_OWNER']);
        }
        $user->setImage($image);
        $user->setCountry($country);
        $user->setCity($city);
        $user->setAddress($address);
        $user->setZipCode($zipCode);
        $user->setPhoneNumber($phoneNumber);
        $user->setJobTitle($jobTitle);
        $user->setJobDescription($jobDescription);

        $userManager->updateUser($user);


        //generate a codeGen for email verification
        $codeGen = $user->generateCode();

        //save codeGen
        $entityManager = $this->getDoctrine()->getManager();
        $accountVerification = new AccountVerification();
        $accountVerification->setUserId($user->getId());
        $accountVerification->setCodeGen($codeGen);

        $entityManager->persist($accountVerification);
        $entityManager->flush();

        $data = array(
            'name' => $user->getFullname(),
            'codeGen' => $codeGen,
            'baseUrl' => $this->getParameter("baseUrl")
        );

        $this->sendMail('Email verification', null, $user->getEmailCanonical(), $data, '@User/Email/emailVerification.twig');

        $response = array('code' => 'auth/registered');
        return new JsonResponse($response);
    }

    public function sendMail($subject, $from, $to, $data, $template)
    {
        //$from = array($this->getParameter('crm_mode') == 'prod' ? $this->getParameter('crm_sender_email_prod') : $this->getParameter('crm_sender_email_dev') => 'CRM Trévi');
        $message = '';
            $message = (new \Swift_Message($subject))
                ->setFrom('zZeend-noreply@zzeend.com')
                ->setTo($to)
                ->setBody(
                    $this->renderView(
                        $template,
                        $data
                    )
                    , 'text/html'
                );

        $mailer = $this->get('mailer');
        $mailer->send($message);

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
            $codeGen = $user->generateCode();

            //save codeGen
            $entityManager = $this->getDoctrine()->getManager();
            $accountVerification = new AccountVerification();
            $accountVerification->setUserId($user->getId());
            $accountVerification->setCodeGen($codeGen);

            $entityManager->persist($accountVerification);
            $entityManager->flush();

            $data = array(
                          'name' => $user->getFullname(),
                          'codeGen' => $codeGen,
                          'baseUrl' => $this->getParameter("baseUrl")
            );

            $this->sendMail('Email verification', null, $user->getEmailCanonical(), $data, '@User/Email/emailVerification.twig');

            $response = array('code' => 'auth/email_verification_sent');
            return new JsonResponse($response);
        }
    }

    public function sendVerificationMailAction($email){
        $em = $this->getDoctrine()->getRepository(User::class);
        $user = $em->FindByEmail($email);
        if($user !== null) {
            //generate a codeGen for email verification
            $codeGen = $user->generateCode();

            //save codeGen
            $entityManager = $this->getDoctrine()->getManager();
            $accountVerification = new AccountVerification();
            $accountVerification->setUserId($user->getId());
            $accountVerification->setCodeGen($codeGen);

            $entityManager->persist($accountVerification);
            $entityManager->flush();

            $data = array(
                'name' => $user->getFullname(),
                'codeGen' => $codeGen,
                'baseUrl' => $this->getParameter("baseUrl")
            );

            $this->sendMail('Email verification', null, $user->getEmailCanonical(), $data, '@User/Email/emailVerification.twig');

            $response = array('code' => 'auth/email_verification_sent');
        }else{
            $response = array('code' => 'auth/user_not_found');
        }
        return new JsonResponse($response);
    }

    public function sendPasswordForgotMailAction($email){
        $response = array();

        $em = $this->getDoctrine()->getRepository(User::class);
        $user = $em->FindByEmail($email);
        if($user !== null) {

            //generate a codeGen for email verification
            $codeGen = $user->generateCode();

            //save codeGen
            $entityManager = $this->getDoctrine()->getManager();
            $passwordForgot = new PasswordForgot();
            $passwordForgot->setUserId($user->getId());
            $passwordForgot->setCodeGen($codeGen);

            $entityManager->persist($passwordForgot);
            $entityManager->flush();

            $data = array(
                'name' => $user->getFullname(),
                'codeGen' => $codeGen,
                'baseUrl' => $this->getParameter("baseUrl")
            );

            $this->sendMail('Password recovery', null, $user->getEmailCanonical(), $data, '@User/Email/passwordForgot.twig');

            $response = array('code' => 'recovery_mail_sent');

        }else{
            $response = array('code' => 'auth/user_not_found');
        }
        return new JsonResponse($response);
    }

    public function resetPasswordAction($codeGen, $newPassword){
        $response = array();

        $entityManager = $this->getDoctrine()->getRepository(PasswordForgot::class);
        $passwordForgotObject = $entityManager->findOneBy(["codeGen" => $codeGen]);
        if($passwordForgotObject !== null) {
            $userId = $passwordForgotObject->getUserId();

            $userManager = $this->get('fos_user.user_manager');
            $user =  $userManager->findUserBy(array('id'=> $userId));
            $user->setPlainPassword($newPassword);
            $userManager->updateUser($user);

            $response = array('code' => 'auth/password_recovered');
        }else{
            $response = array('code' => 'auth/codeGen_error');
        }

        return new JsonResponse($response);
    }

    public function enableAccountAction($codeGen){
        $response = array();
        if(isset($codeGen)){
            $entityManager = $this->getDoctrine()->getRepository(AccountVerification::class);
            $accountVerificationObject = $entityManager->findOneBy(["codeGen" => $codeGen]);
            if($accountVerificationObject !== null){
                $userId = $accountVerificationObject->getUserId();

                $userManager = $this->get('fos_user.user_manager');
                $user =  $userManager->findUserBy(array('id'=> $userId));
                $user->setEnabled(1);
                $userManager->updateUser($user);

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
}
