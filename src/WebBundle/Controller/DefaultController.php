<?php

namespace WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\AccountVerification;
use UserBundle\Entity\PasswordForgot;
use UserBundle\Entity\User;

class DefaultController extends Controller
{
    public function emailVerificationAction($codeGen)
    {
        $em = $this->getDoctrine()->getRepository(AccountVerification::class);
        $codeGenExists = $em->findOneBy(['codeGen' => $codeGen]);

        if($codeGenExists){

            $userId = $codeGenExists->getUserId();

            $em = $this->getDoctrine()->getRepository(User::class);
            $user = $em->find($userId);
            $user->setEnable(1);

//            return $this->render('WebBundle:Default:email_verification.html.twig', [
//                'code' => 'auth/account_verified'
//            ]);
        }else{
//            return $this->render('WebBundle:Default:email_verification.html.twig',[
//                'code' => 'auth/account_not_exists'
//            ]);
        }

    }

    public function passwordForgot($codeGen)
    {
//        return $this->render('WebBundle:Default:new_password.html.twig',[
//            'codeGen' => $codeGen
//        ]);
    }

    public function newPassword($codeGen, $newPassword){
        $response = array();
        $em = $this->getDoctrine()->getRepository(PasswordForgot::class);
        $codeGenExists = $em->findOneBy(['codeGen' => $codeGen]);

        if($codeGenExists){
           $userId =  $codeGenExists->getUserId();
           $response = $this->forward("UserBundle:Default:newPassword", [
               $userId,
               $newPassword
           ]);
        }else{
//            return $this->render('WebBundle:Default:email_verification.html.twig',[
//                'code' => 'auth/password_recovery_failed'
//            ]);
        }

        return $response;
    }
}
