<?php


namespace ApiBundle\Manager;


use ApiBundle\Entity\Chat;
use ApiBundle\Entity\Like;
use ApiBundle\Entity\Notification;
use ApiBundle\Entity\NotificationType;
use ApiBundle\Entity\Post;
use ApiBundle\Entity\View;
use Doctrine\ORM\EntityManager;
use UserBundle\Entity\User;

class ChatManager
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;

    }


    public function getChatById($chatId, $currentUser)
    {
        $response = array();

        $chat = $this->em->getRepository(Chat::class)->find($chatId);

        $sharedContent = null;
        $nbLikes = null;
        $nbViews = null;
        $postLikeState = [];

        if($chat->getShare() !== null){

            $shareTypeId = $chat->getShare()->getShareType()->getId();


            $relatedId = $chat->getShare()->getRelatedId();


            if($shareTypeId == 1) {
                $sharedContent = $this->em->getRepository(Post::class)->find(224);

                $em = $this->em->getRepository(Like::class);
                $qb = $em->GetQueryBuilder();
                $qb = $em->GetLikesCount($qb, $sharedContent);
                $nbLikes = $qb->getQuery()->getSingleScalarResult();

                $em = $this->em->getRepository(View::class);
                $qb = $em->GetQueryBuilder();
                $qb = $em->GetViewsCount($qb, $sharedContent);
                $nbViews = $qb->getQuery()->getSingleScalarResult();

                $em = $this->em->getRepository(Like::class);
                $qb = $em->GetQueryBuilder();
                $qb = $em->WhereUserLikesPost($qb, $currentUser, $sharedContent);
                $postLikeState = $qb->getQuery()->getResult();

            }


        }

        return array(
            "object" => ["chat" => $chat, "sharedContent" => array()]
        );
    }

}