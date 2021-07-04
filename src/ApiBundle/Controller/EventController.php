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
        $start_time = $data['startTime'];
        $end_date = $data['endTime'];

        $entityManager = $this->getDoctrine()->getManager();

        $event = new Event();
        $event->setTitle($title);
        $event->setStartTime(new \DateTime($start_time));
        $event->setEndTime(new \DateTime($end_date));
        $event->setActive(true);

        if(array_key_exists('userId', $data) || array_key_exists('zZeendId', $data)) {

            if (array_key_exists('zZeendId', $data)) {
                $zZeend_id = $data['zZeendId'];
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


            if (array_key_exists('userId', $data)) {
                $user_id = $data['userId'];
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

        if(array_key_exists('allDay', $data)){
            $all_day = $data['allDay'];
            $event->setAllDay($all_day);
        }else{
            $event->setAllDay(false);
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

    public function deleteEventAction(Request $request){
        $response = array();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $event_id = $data['event_id'];

        $event = $this->getDoctrine()->getRepository(Event::class)->find($event_id);

        if($event !== null){

            $event->setActive(false);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($event);
            $entityManager->flush();

            $response = array("code" => "event_off");
        }else{
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);

    }

}