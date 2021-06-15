<?php


namespace ApiBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use ApiBundle\Entity\Service;

class ServiceController extends Controller
{

    public function addServicesAction(Request $request){
        $data = $request->getContent();
        $data = json_decode($data, true);

        $userId = $data['userId'];
        $services = $data['services'];

        $response = array();
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        if(count($services) > 0){

            for($i = 0; $i < count($services); $i++){
                $service = new Service();
                if(trim($services[$i]) !== '') {
                    $service->setService($services[$i]);
                    $service->setUser($user);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($service);
                    $entityManager->flush();
                }
            }
        }
        $response = array("code" => "add_success");
        return new JsonResponse($response);
    }


}