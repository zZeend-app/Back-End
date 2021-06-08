<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use UserBundle\Entity\AccountVerification;
use UserBundle\Entity\User;

class DefaultController extends Controller
{
    public function getCurrentUserAction()
    {
        return $this->getUser();
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

        if($accountType == '0'){
            $user->setRoles(['ROLE_SEEKER']);
        }else if($accountType == '1'){
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

        $this->sendMail('zZeend - email verification', 'no-reply@zzeend.com', $user->getEmailCanonical(), $user->getFullname(), $codeGen, 'email_verification');

        $response = array('code' => 'auth/registered');
        return new JsonResponse($response);
    }

    public function sendMail($subject, $from, $to, $fullname, $codeGen, $type)
    {
        $message = '';
        if ($type === 'email_verification') {
            $message = (new \Swift_Message($subject))
                ->setFrom($from)
                ->setTo($to)
                ->setBody(
                    $this->renderView(
                        '@User/Email/emailVerification.twig',
                        array('email' => $to, 'codeGen' => $codeGen, 'name' => $fullname)
                    )
                    , 'text/html'
                );
        }

        if($type === 'password_forgot'){
            $message = (new \Swift_Message($subject))
                ->setFrom($from)
                ->setTo($to)
                ->setBody(
                    $this->renderView(
                        '@User/Email/passwordForgot.twig',
                        array('email' => $to, 'codeGen' => $codeGen, 'name' => $fullname)
                    )
                    , 'text/html'
                );
        }

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

            $this->sendMail('zZeend - email verification', 'no-reply@zzeend.com', $user->getEmailCanonical(), $user->getFullname(), $codeGen, 'email_verification');

            $response = array('code' => 'auth/email_verification_sent');
            return new JsonResponse($response);
        }
    }

    public function sendVerificationmail($email){
        //generate a codeGen for email verification
        $user = new User();
        $codeGen = $user->generateCode();

        //save codeGen
        $entityManager = $this->getDoctrine()->getManager();
        $accountVerification = new AccountVerification();
        $accountVerification->setUserId($user->getId());
        $accountVerification->setCodeGen($codeGen);

        $entityManager->persist($accountVerification);
        $entityManager->flush();

        $this->sendMail('zZeend - email verification', 'no-reply@zzeend.com', $user->getEmailCanonical(), $user->getFullname(), $codeGen, 'email_verification');

        $response = array('code' => 'auth/email_verification_sent');
        return new JsonResponse($response);
    }

    public function passwordForgotAction($email, $fullname){
        $response = array();

        $user = new User();
        //generate a codeGen for email verification
        $codeGen = $user->generateCode();
        $this->sendMail('zZeend - password recovery', 'no-reply@zzeend.com', $email, $fullname, $codeGen, 'password_forgot');

        $response = array('code' => 'recovery_mail_sent');
        return new JsonResponse($response);
    }

    public function newPasswordAction($userId, $newPassWord){
        $response = array();
        $em = $this->getDoctrine()->getRepository(User::class);
        $user = $em->find($userId);
        $user->setPlainPassword($newPassWord);
        $response = array('code' => 'password_recovered');

        return new JsonResponse($response);
    }
}
