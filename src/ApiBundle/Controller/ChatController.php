<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Chat;
use ApiBundle\Entity\Contact;
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

class ChatController extends Controller
{
    public function sendChatAction(Request $request)
    {

        $prefix = '';
        $response = array();
        $currentUser = $this->getUser();

        $updated = array();
        $fileName = '';
        $data = array();

        $file_type = null;

        $fileOriginalName = '';

        $fileSize = 0;

        $videoThumbnail = '';

        $chatFromClient = '';


        if (!empty($request->files->get('chatFile'))) {

            $file = $request->files->get('chatFile');

            $uploadDir = $this->getParameter('upload_dir');

            $data = json_decode($_POST['data'], true);

            $dataType = $data['dataType'];

            $fileName = $this->get('ionicapi.fileUploaderManager')->upload($file, $uploadDir, $dataType);

            $fileOriginalName = $file->getClientOriginalName();

            $fileSize = $file->getClientSize();

            $data = $data['objectData'];

//                $file_type = 'image';

            if($dataType == 'chat_photos'){
                $file_type = 'image';
                $prefix = 'cfBfqcChzEM9ai3hQvX0GaC80KibabT1uUdf6LXtSYqpn1h';
            }if($dataType == 'chat_videos'){
                $file_type = 'video';
                $prefix = 'afBfqcChzEM9ai3hdQvX0GC80KibabT1uU6LviXtSYqpn1ZdeoC3653sndkxn22e01996';

                $ffmpeg = \FFMpeg\FFMpeg::create([
                    'ffmpeg.binaries'  => 'C:/FFmpeg/bin/ffmpeg.exe',
                    'ffprobe.binaries' => 'C:/FFmpeg/bin/ffprobe.exe'
                ]);
                $video = $ffmpeg->open($uploadDir . '/'.$dataType.'/'.$fileName);
                $frame = $video->frame(TimeCode::fromSeconds(0));
                $frame->save($uploadDir . '/'.$dataType.'/'.$fileName.'.jpg');

                $videoThumbnail = $uploadDir . '/'.$dataType.'/'.$fileName.'.jpg';

            }

        }


        if ($fileName == '') {

            //if no upload has made
            $data = $request->getContent();
            $data = json_decode($data, true);
            $chatFromClient = $data['chat'];
        }


        $contactId = $data['contactId'];


        $currentUser = $this->getUser();

        $contact = $this->getDoctrine()->getRepository(Contact::class)->find($contactId);

        $em = $this->getDoctrine()->getRepository(Contact::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereContactId($qb, $contactId);

        $contactFound = $qb->getQuery()->getResult();
        if (count($contactFound) > 0) {

            $entityManager = $this->getDoctrine()->getManager();
            $chat = new Chat();
            $chat->setDiscussion(nl2br($chatFromClient));
            $chat->setContact($contact);
            $chat->setCreatedAtAutomatically();

            if ($fileName !== '') {

                $fileEntityManager = $this->getDoctrine()->getManager();

                $file = new File();
                $file->setUser($currentUser);
                $file->setFilePath($prefix. '/' . $fileName);

                if ($file_type !== null) {
                    $file->setFileType($file_type);
                } else {
                    $file->setFileType('');
                }

                $file->setFileSize($fileSize);

                if($videoThumbnail !== ''){
                    $file->setThumbnail($prefix. '/' . $fileName.'.jpg');
                }
                $file->setFileName($fileOriginalName);
                $file->setCreatedAtAutomatically();

                $fileEntityManager->persist($file);
                $fileEntityManager->flush();


                $chat->setFile($file);
            } else {

                if(array_key_exists('fileId', $data)){

                    $fileId = $data['fileId'];
                    $file = $this->getDoctrine()->getRepository(File::class)->find($fileId);

                    if($file !== null){
                        $chat->setFile($file);
                    }else{
                        $chat->setFile(null);
                    }
                }else{
                    $chat->setFile(null);
                }

            }



            $chat->setViewed(false);
            $currentUser = $this->getUser();
            $chat->setUser($currentUser);
            $chat->setShare(null);
            $entityManager->persist($chat);
            $entityManager->flush();

            $mainUser = $contact->getUsers()['mainUser'];
            $secondUser = $contact->getUsers()['secondUser'];

            $receiver = null;

            if ($mainUser->getId() == $currentUser->getId()) {

                $receiver = $secondUser;

            } else if ($secondUser->getId() == $currentUser->getId()) {

                $receiver = $mainUser;
            }

            $translator = $this->get('translator');

            $translateTo = 'en';
            if($receiver->getLang() !== ''){
                $translateTo = $receiver->getLang();
            }

            $userFullname = $currentUser->getFullname();

            $subject = strip_tags($chat->getDiscussion());
            //send notification
            $pushNotificationManager = $this->get('ionicapi.push.notification.manager');
            $data = array("type" => 9,
                "chat" => $chat);
            $pushNotificationManager->sendNotification($receiver, $translator->trans('%userFullname% sent a chat', ['%userFullname%' => $userFullname], null, $translateTo), $subject, $data, $currentUser->getPhoto() !== null ? $currentUser->getPhoto()->getFilePath() : null);


            $response = array("chat" => $chat);
        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

    public function getChatAction(Request $request)
    {
        $response = array();
        $data = $request->getContent();
        $data = json_decode($data, true);

        $jsonManager = $this->get("ionicapi.jsonManager");

        //make sure nothing is missing inside data
        $data = $jsonManager->getInclude($data);

        //get restriction
        $filtersInclude = $data["filters"]["include"];

        $contactId = $filtersInclude['contactId'];

        $contact = $this->getDoctrine()->getRepository(Contact::class)->find($contactId);

        $em = $this->getDoctrine()->getRepository(Chat::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereContact($qb, $contact);
        if (array_key_exists("order", $data)) {
            $qb = $em->OrderByJson($qb, $data["order"]);
        }
        $chats = $jsonManager->setQueryLimit($qb, $filtersInclude);

        $chats = array_reverse($chats);

        for ($i = 0; $i < count($chats); $i++) {

            $sharedContent = null;
            $nbLikes = null;
            $nbViews = null;
            $postLikeState = [];
            $chat = $chats[$i];
            $currentUser = $this->getUser();

            if ($chat->getShare() !== null) {
                $shareTypeId = $chat->getShare()->getShareType()->getId();


                $relatedId = $chat->getShare()->getRelatedId();


                if ($shareTypeId == 1) {
                    $sharedContent = $this->getDoctrine()->getRepository(Post::class)->find($relatedId);

                    $em = $this->getDoctrine()->getRepository(Like::class);
                    $qb = $em->GetQueryBuilder();
                    $qb = $em->GetLikesCount($qb, $sharedContent);
                    $nbLikes = $qb->getQuery()->getSingleScalarResult();

                    $em = $this->getDoctrine()->getRepository(View::class);
                    $qb = $em->GetQueryBuilder();
                    $qb = $em->GetViewsCount($qb, $sharedContent);
                    $nbViews = $qb->getQuery()->getSingleScalarResult();

                    $em = $this->getDoctrine()->getRepository(Like::class);
                    $qb = $em->GetQueryBuilder();
                    $qb = $em->WhereUserLikesPost($qb, $currentUser, $sharedContent);
                    $postLikeState = $qb->getQuery()->getResult();

                } else if ($shareTypeId == 2) {
                    $sharedContent = $this->getDoctrine()->getRepository(User::class)->find($relatedId);
                } else if ($shareTypeId == 3) {
                    //chat shared retrieved
                    $sharedContent = $this->getDoctrine()->getRepository(Chat::class)->find($relatedId);
                }
            }

            $response[] = array(
                "chat" => $chat,
                "sharedContent" => $sharedContent,
                "postLikeState" => $postLikeState,
                "likes" => intval($nbLikes),
                "views" => intval($nbViews)
            );

        }


        return new JsonResponse($response);
    }

    public function getChatByIdWithRequestAction($chatId)
    {

        $response = array();
        $response = $this->getChatByIdAction($chatId);

        return new JsonResponse($response);
    }

    public function getChatByIdAction($chatId)
    {
        $response = array();


        $chat = $this->getDoctrine()->getRepository(Chat::class)->find($chatId);

        $sharedContent = null;
        $nbLikes = null;
        $nbViews = null;
        $postLikeState = [];
        $currentUser = $this->getUser();

        if ($chat->getShare() !== null) {
            $shareTypeId = $chat->getShare()->getShareType()->getId();


            $relatedId = $chat->getShare()->getRelatedId();


            if ($shareTypeId == 1) {
                $sharedContent = $this->getDoctrine()->getRepository(Post::class)->find($relatedId);

                $em = $this->getDoctrine()->getRepository(Like::class);
                $qb = $em->GetQueryBuilder();
                $qb = $em->GetLikesCount($qb, $sharedContent);
                $nbLikes = $qb->getQuery()->getSingleScalarResult();

                $em = $this->getDoctrine()->getRepository(View::class);
                $qb = $em->GetQueryBuilder();
                $qb = $em->GetViewsCount($qb, $sharedContent);
                $nbViews = $qb->getQuery()->getSingleScalarResult();

                $em = $this->getDoctrine()->getRepository(Like::class);
                $qb = $em->GetQueryBuilder();
                $qb = $em->WhereUserLikesPost($qb, $currentUser, $sharedContent);
                $postLikeState = $qb->getQuery()->getResult();

            } else if ($shareTypeId == 2) {
                $sharedContent = $this->getDoctrine()->getRepository(User::class)->find($relatedId);
            } else if ($shareTypeId == 3) {
                //chat shared retrieved
                $sharedContent = $this->getDoctrine()->getRepository(Chat::class)->find($relatedId);
            }
        }

        $response = array(
            "chat" => $chat,
            "sharedContent" => $sharedContent,
            "postLikeState" => $postLikeState,
            "likes" => intval($nbLikes),
            "views" => intval($nbViews)
        );


        return $response;
    }


    public function getChatContactAction(Request $request)
    {
        $currentUser = $this->getUser();

        $data = $request->getContent();

        $data = json_decode($data, true);

        $jsonManager = $this->get("ionicapi.jsonManager");

        //make sure nothing is missing inside data
        $data = $jsonManager->getInclude($data);

        //get restriction
        $filtersInclude = $data["filters"]["include"];

        $count = $filtersInclude['count'];
        $offset = $filtersInclude['offset'];

        $limit = $offset + 19;


        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'SELECT chat.contact_id, MAX(chat.created_at) FROM chat INNER JOIN contact WHERE (contact.main_user_id = :main_user_id OR contact.second_user_id = :main_user_id) AND chat.contact_id = contact.id GROUP BY chat.contact_id ORDER BY MAX(chat.created_at) DESC LIMIT ' . $offset . ', ' . $limit . ' ;';

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('main_user_id', $currentUser->getId());
        $statement->execute();

        $chatContactIds = $statement->fetchAll();

        $contacts = [];

        for ($i = 0; $i < count($chatContactIds); $i++) {
            $chatContactId = intval($chatContactIds[$i]["contact_id"]);

            $contact = $this->getDoctrine()->getRepository(Contact::class)->find($chatContactId);

            $em = $this->getDoctrine()->getRepository(Chat::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->GetCountForEachChatContact($qb, $contact, false, $currentUser);

            $nbUnViewed = $qb->getQuery()->getSingleScalarResult();

            $contacts[] = array("contact" => $contact, "nbUnViewed" => intval($nbUnViewed));
        }


        return new JsonResponse($contacts);
    }

    public function markAsViewedAction(Request $request)
    {
        $response = array();
        $data = $request->getContent();

        $data = json_decode($data, true);

        $contactId = $data['contactId'];

        $contact = $this->getDoctrine()->getRepository(Contact::class)->find($contactId);

        if ($contact !== null) {

            $entityManager = $this->getDoctrine()->getManager();

            $em = $this->getDoctrine()->getManager();
            $RAW_QUERY = 'UPDATE chat SET chat.viewed = true WHERE chat.contact_id = :contactId;';

            $statement = $em->getConnection()->prepare($RAW_QUERY);
            $statement->bindValue('contactId', $contactId);
            $statement->execute();


            $response = array("code" => "marked_as_viewed");

        } else {

            $response = array("code" => "action_no_allowed");

        }

        return new JsonResponse($response);
    }


}