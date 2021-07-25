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

    public function editServiceAction(Request $request)
    {
        $response = array();
        $update = [];
        $data = $request->getContent();
        $data = json_decode($data, true);

        $serviceId = $data['serviceId'];
        $service = $data['service'];

        $entityManager = $this->getDoctrine()->getManager();

        $serviceObject = $this->getDoctrine()->getRepository(Service::class)->find($serviceId);

        if($serviceObject !== null){
            if($serviceObject->getService() !== $service){
                $serviceObject->setService($service);
                $entityManager->persist($serviceObject);
                $entityManager->flush();
                $update[] = "service";

                $response = array("updated" => $update);
            }else{
                $response = array("updated" => $update);
            }

        }else{
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);

    }

    public function deleteServiceAction($serviceId)
    {
        $response = array();

        $entityManager = $this->getDoctrine()->getManager();

        $serviceObject = $this->getDoctrine()->getRepository(Service::class)->find($serviceId);

        if($serviceObject !== null){
            $entityManager->remove($serviceObject);
            $entityManager->flush();

            $response = array('code' => 'service_deleted');

        }else{
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);

    }

    public function changeServicePositionAction(Request $request)
    {
        $response = array();
        $data = $request->getContent();
        $data = json_decode($data, true);

        $fromId = $data['fromId'];
        $toId = $data['toId'];

        $entityManager = $this->getDoctrine()->getManager();

        $fromService = $this->getDoctrine()->getRepository(Service::class)->find($fromId);
        $toService = $this->getDoctrine()->getRepository(Service::class)->find($toId);

        if ($fromService !== null && $toService !== null) {

            $services = $this->getDoctrine()->getRepository(Service::class)->findAll();

            if($fromId < $toId){
                $services = array_reverse($services);
            }

            $temp_to = null;
            $precedent = null;

            for($i = 0; $i < count($services); $i++){

                if($services[$i]->getId() == $toId){

                    $precedent = $services[$i];
                    $temp_to = $services[$i];

                }else{

                    if($precedent !== null){

                            if($temp_to !== null){

                                if($services[$i]->getId() == $fromId){

                                    $clonedService = clone $services[$i];
                                    $services[$i]->setService($precedent->getService());
                                    $entityManager->persist($services[$i]);
                                    $entityManager->flush();

                                    $entityManager = $this->getDoctrine()->getManager();
                                    $toObject = $this->getDoctrine()->getRepository(Service::class)->find($toId);
                                    $toObject->setService($clonedService->getService());
                                    $entityManager->persist($services[$i]);
                                    $entityManager->flush();
                                    break;

                                }else{

                                    $values[] = $precedent;
                                    $clonedService = clone $services[$i];
                                    $services[$i]->setService($precedent->getService());
                                    $entityManager->persist($services[$i]);
                                    $entityManager->flush();

                                    $precedent = $clonedService;
                                }
                            }
                    }else {
                        $precedent = $services[$i];
                    }
                }
            }

            $response = array('code' => 'service_position_changed');

        }else{
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);

    }

    public function getAllServicesAction(){

        $response = array();

        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getRepository(Service::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereUser($qb, $currentUser);
        $response = $qb->getQuery()->getResult();

        return new JsonResponse($response);

    }


}