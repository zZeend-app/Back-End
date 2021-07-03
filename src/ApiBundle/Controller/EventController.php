<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Event;
use ApiBundle\Entity\Zzeend;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class EventController extends Controller
{

    public function newEventAction(Request $request){
        $response = array();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $title = $data['title'];
        $start_time = $data['start_time'];
        $end_date = $data['end_date'];

        $entityManager = $this->getDoctrine()->getManager();

        $event = new Event();
        $event->setTitle($title);
        $event->setStartTime(new \DateTime($start_time));
        $event->setEndTime(new \DateTime($end_date));
        $event->setActive(true);

        if(array_key_exists('user_id', $data) || array_key_exists('zZeend_id', $data)) {

            if (array_key_exists('zZeend_id', $data)) {
                $zZeend_id = $data['zZeend_id'];
                $zZeend = $this->getDoctrine()->getRepository(Zzeend::class)->find($zZeend_id);
                if ($zZeend !== null) {
                    $event->setZzeend($zZeend);
                } else {
                    $response = array("code" => "action_not_allowed");
                    return new JsonResponse($response);
                }
            } else {
                $event->setZzeend(null);
            }


            if (array_key_exists('user_id', $data)) {
                $user_id = $data['user_id'];
                $user = $this->getDoctrine()->getRepository(User::class)->find($user_id);
                if ($user !== null) {
                    $event->setUser($user);
                } else {
                    $response = array("code" => "action_not_allowed");
                    return new JsonResponse($response);
                }
            } else {
                $event->setUser(null);
            }
        }else{
            $response = array("code" => "action_not_allowed");
            return new JsonResponse($response);
        }

        if(array_key_exists('all_day', $data)){
            $all_day = $data['all_day'];
            $event->setAllDay($all_day);
        }else{
            $event->setAllDay(null);
        }

        $event->setCreatedAtAutomatically();

        $entityManager->persist($event);
        $entityManager->flush();

        $response = array("code" => "event_added");

        return new JsonResponse($response);
    }

    public function getAllEventsAction(){
        $currentUSer = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager
            ->createQuery(
                'SELECT e FROM ApiBundle:Event e
        JOIN ApiBundle:Zzeend z
        WHERE e.user = :user OR
        z.user = :user'
            )->setParameter('user', $currentUSer);

        $events = $query->getResult();

        return new JsonResponse($events);
    }

}