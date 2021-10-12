<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Chat;
use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Feedback;
use ApiBundle\Entity\File;
use ApiBundle\Entity\Like;
use ApiBundle\Entity\Notification;
use ApiBundle\Entity\Post;
use ApiBundle\Entity\View;
use Doctrine\ORM\QueryBuilder;
use FFMpeg\Coordinate\TimeCode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class FeedbackController extends Controller
{
    public function newFeedbackAction(Request $request)
    {

        $currentUser = $this->getUser();
        $response = array();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $subject = $data['subject'];
        $message = $data['message'];

        $entityManager = $this->getDoctrine()->getManager();

        $feedBack = new Feedback();
        $feedBack->setSubject($subject);
        $feedBack->setMessage($message);
        $feedBack->setUser($currentUser);
        $feedBack->setCreatedAtAutomatically();

        $entityManager->persist($feedBack);
        $entityManager->flush();

        $data = array(
            'subject' => $subject,
            'id' => $feedBack->getId(),
            'message' => $message,
            'date' => $feedBack->getCreatedAt()->format('Y-m-d H:i:s'),
            'email' => $currentUser->getEmailCanonical(),
            'fullname' => $currentUser->getFullname()
        );

        $sendEmailTo = 'michel.k@zzeend.com';

        $emailManager = $this->get('ionicapi.emailManager');
        $app_mail = $this->getParameter('app_mail');
        $emailManager->send($app_mail, $sendEmailTo, 'Feedback from a user', '@User/Email/feedback.twig', $data);

        return new JsonResponse(array("code" => "feedback_added"));

    }


}