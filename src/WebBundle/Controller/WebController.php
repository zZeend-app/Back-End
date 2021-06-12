<?php

namespace WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\AccountVerification;
use UserBundle\Entity\PasswordForgot;
use UserBundle\Entity\User;

class WebController extends Controller
{
    public function resetPasswordRenderAction(){
        return $this->render("@Web/new-password.html.twig");
    }
}
