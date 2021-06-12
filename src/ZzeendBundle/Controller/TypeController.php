<?php

namespace ZzeendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TypeController extends Controller
{
    public function indexAction()
    {
        return $this->render('ZzeendBundle:Default:index.html.twig');
    }
}
