<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Service;
use ApiBundle\Entity\Tag;
use ApiBundle\Entity\ZzeendService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ZzeendServicesController extends Controller
{

    public function getServicesAction(Request $request)
    {
        $response = array('michel');

        $zZeendServices = $this->getDoctrine()->getRepository(ZzeendService::class)->findAll();
        return new JsonResponse($zZeendServices);


    }

}