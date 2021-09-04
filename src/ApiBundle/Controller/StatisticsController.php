<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Chat;
use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Like;
use ApiBundle\Entity\Notification;
use ApiBundle\Entity\PaymentMethod;
use ApiBundle\Entity\Post;
use ApiBundle\Entity\Rate;
use ApiBundle\Entity\Zzeend;
use ApiBundle\Entity\ZzeendStatus;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StatisticsController extends Controller
{

    public function getMenuStatsAction()
    {
        $response = array();

        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getRepository(Zzeend::class);
        $qb = $em->GetQueryBuilder();

        $status = $this->getDoctrine()->getRepository(ZzeendStatus::class)->find(1);
        $qb = $em->OrWhereUser($qb, $currentUser);
        $qb = $em->GetCount($qb, false, $status);
        $nbZzeends = $qb->getQuery()->getSingleScalarResult();

        $em = $this->getDoctrine()->getRepository(\ApiBundle\Entity\Request::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->GetCount($qb);
        $qb = $em->WhereUserReceiver($qb, $currentUser);
        $qb = $em->RequestStateIsNull($qb);
        $nbRequests = $qb->getQuery()->getSingleScalarResult();


        $em = $this->getDoctrine()->getRepository(Notification::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereViewed($qb, false);

        $notifications = $qb->getQuery()->getResult();

        $nbNotifications = 0;

        for ($i = 0; $i < count($notifications); $i++) {

            $notification = $notifications[$i];

            $notificationType = $notification->getNotificationType();
            $notificationTypeId = $notificationType->getId();

            $relatedId = $notification->getRelatedId();

            //zZeend notification
            if ($notificationTypeId == 1 || $notificationTypeId == 2 || $notificationTypeId == 3 || $notificationTypeId == 4 || $notificationTypeId == 5) {

                $zZeend = $this->getDoctrine()->getRepository(Zzeend::class)->find($relatedId);

                $zZeendCreator = $zZeend->getUser();
                $zZeendAssignedUser = $zZeend->getUserAssigned();

                if ($notificationTypeId == 1 || $notificationTypeId == 4 || $notificationTypeId == 5) {

                    //if am not the one who created the current so, add this notification
                    if ($zZeendCreator !== $currentUser && $zZeendAssignedUser == $currentUser) {
                        $nbNotifications++;
                    }

                } else {

                    if ($zZeendCreator === $currentUser && $zZeendAssignedUser !== $currentUser) {
                        $nbNotifications++;
                    }
                }


            } // request notification
            else if ($notificationTypeId == 6 || $notificationTypeId == 7) {

                $request = $this->getDoctrine()->getRepository(\ApiBundle\Entity\Request::class)->find($relatedId);



                $sender = $request->getSender();

                if ($sender == $currentUser) {
                    $nbNotifications++;
                }

            } else {

                if ($notificationTypeId == 8) {
                    $rate = $this->getDoctrine()->getRepository(Rate::class)->find($relatedId);

                    $ratedUser = $rate->getRatedUser();

                    if ($ratedUser == $currentUser) {

                        $nbNotifications++;

                    }
                }

            }


        }


        $em = $this->getDoctrine()->getRepository(PaymentMethod::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->GetCount($qb);
        $qb = $em->WhereUser($qb, $currentUser);
        $nbPaymentMethods = $qb->getQuery()->getSingleScalarResult();


        $nbUnViewed = 0;
        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'SELECT chat.contact_id FROM chat INNER JOIN contact WHERE (contact.main_user_id = :main_user_id OR contact.second_user_id = :main_user_id) AND chat.contact_id = contact.id GROUP BY chat.contact_id ORDER BY chat.id;';

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('main_user_id', $currentUser->getId());
        $statement->execute();

        $chatContactIds = $statement->fetchAll();

        $unViewedChatContact = 0;
        for ($i = 0; $i < count($chatContactIds); $i++) {
            $chatContactId = intval($chatContactIds[$i]["contact_id"]);

            $contact = $this->getDoctrine()->getRepository(Contact::class)->find($chatContactId);

            $em = $this->getDoctrine()->getRepository(Chat::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->GetCountForEachChatContact($qb, $contact, false, $currentUser);

            $nbUnViewed = $qb->getQuery()->getSingleScalarResult();

            if ($nbUnViewed > 0) {
                $unViewedChatContact += 1;
            }

        }

        $response = array("zZeends" => intval($nbZzeends),
            "notifications" => intval($nbNotifications),
            "requests" => intval($nbRequests),
            "events" => '',
            "paymentMethods" => intval($nbPaymentMethods),
            "chats" => $unViewedChatContact);

        return new JsonResponse($response);
    }

}