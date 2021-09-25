<?php

namespace LiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GuestController extends Controller
{
    public function indexAction()
    {
        return $this->render('LiveBundle:Default:index.html.twig');
    }
}
