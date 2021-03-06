<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Chat;
use ApiBundle\Entity\Contact;
use ApiBundle\Entity\File;
use ApiBundle\Entity\Story;
use ApiBundle\Entity\View;
use FFMpeg\Coordinate\TimeCode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class StoryController extends Controller
{

    public function getStoriesAction(Request $request)
    {
        $rt = array();
        $response = array();
        $myContactsStoriesUsers = array();
        $currentUserStoryExists = false;
        $currentUser = $this->getUser();
        $contactNotFound = false;

        $data = $request->getContent();

        $data = json_decode($data, true);

        $jsonManager = $this->get("ionicapi.jsonManager");

        //make sure nothing is missing inside data
        $data = $jsonManager->getInclude($data);

        //get restriction
        $filtersInclude = $data["filters"]["include"];

        $count = $filtersInclude['count'];
        $offset = $filtersInclude['offset'];

        $limit = $offset + 14;

        $em = $this->getDoctrine()->getManager();

        $RAW_QUERY = 'SELECT story.user_id, MAX(story.created_at) FROM story INNER JOIN contact WHERE (contact.main_user_id = :main_user_id AND contact.second_user_id = story.user_id AND story.created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)) OR (contact.main_user_id = story.user_id AND contact.second_user_id = :main_user_id AND story.created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)) GROUP BY story.user_id ORDER BY MAX(story.created_at) DESC LIMIT ' . $offset . ', ' . $limit . ' ;';

        $dataGroupedByContact = $this->contactStories($currentUser, $RAW_QUERY);


        if (count($dataGroupedByContact) == 0) {
            $contactNotFound = true;

            $RAW_QUERY = 'SELECT story.user_id, MAX(story.created_at) FROM story WHERE story.created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR) GROUP BY story.user_id ORDER BY MAX(story.created_at) DESC LIMIT ' . $offset . ', ' . $limit . ' ;';

            $dataGroupedByContact = $this->contactStories($currentUser, $RAW_QUERY);


        }


        $myContactsStoriesUsers = array_merge($myContactsStoriesUsers, $dataGroupedByContact);

        $nb_myContactsStoriesUser = count($myContactsStoriesUsers);

        if ($contactNotFound == false) {

            if ($nb_myContactsStoriesUser < $count) {

                $count = $count - count($myContactsStoriesUsers);

                $offset = count($myContactsStoriesUsers);


                $limit = $limit + count($myContactsStoriesUsers);



                $RAW_QUERY = 'SELECT story.user_id, MAX(story.created_at) FROM story WHERE story.created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR) GROUP BY story.user_id ORDER BY MAX(story.created_at) DESC LIMIT ' . $offset . ', ' . $limit . ' ;';

                $statement = $em->getConnection()->prepare($RAW_QUERY);
                $statement->bindValue('main_user_id', $currentUser->getId());
                $statement->execute();

                $results = $statement->fetchAll();
                $rt = $results;

                for ($q = 0; $q < count($myContactsStoriesUsers); $q++) {

                    $story = $myContactsStoriesUsers[$q];

                    for ($l = 0; $l < count($results); $l++) {

                        $result = $rt[$l];

                        if ($story['user_id'] == $result['user_id']) {
                            unset($results[$l]);
                        }

                    }

                }


                $myContactsStoriesUsers = array_merge($myContactsStoriesUsers, $results);


            }

        }


        for ($i = 0; $i < count($myContactsStoriesUsers); $i++) {
            $userId = intval($myContactsStoriesUsers[$i]["user_id"]);

            if ($userId == $currentUser->getId()) {
                $currentUserStoryExists = true;
            } else {

                $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

                $em = $this->getDoctrine()->getRepository(Story::class);
                $qb = $em->GetQueryBuilder();
                $qb = $em->WhereUser($qb, $user);
                $qb = $em->WhereDateIsGraterThan_24($qb);

                $stories = $qb->getQuery()->getResult();

                $storyViewsState = array();
                $allViewsExceptCurrentUser = array();
                for ($k = 0; $k < count($stories); $k++) {

                    $story = $stories[$k];

                    $em = $this->getDoctrine()->getRepository(View::class);
                    $qb = $em->GetQueryBuilder();
                    $qb = $em->WhereUserViewsStory($qb, $currentUser, $story->getId(), 3);
                    $_qb = $em->WhereAllViewsStory($qb, $story->getId(), 3);

                    $storyViewsState[] = $qb->getQuery()->getResult();
                    $allViewsExceptCurrentUser[] = $_qb->getQuery()->getResult();

                }

                if (count($allViewsExceptCurrentUser) > 0) {

                    $response[] = array("user" => $user, "stories" => $stories, 'storyViewsState' => $storyViewsState, 'views' => [$allViewsExceptCurrentUser][0]);


                } else {

                    $response[] = array("user" => $user, "stories" => $stories, 'storyViewsState' => $storyViewsState, 'views' => []);


                }


            }

        }

        shuffle($response);

        if ($currentUserStoryExists) {


            $user = $this->getDoctrine()->getRepository(User::class)->find($currentUser->getId());

            $em = $this->getDoctrine()->getRepository(Story::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->WhereUser($qb, $user);
            $qb = $em->WhereDateIsGraterThan_24($qb);

            $currentUserStories = $qb->getQuery()->getResult();

            $allViewsExceptCurrentUser = array();

            for ($j = 0; $j < count($currentUserStories); $j++) {

                $story = $currentUserStories[$j];

                $em = $this->getDoctrine()->getRepository(View::class);
                $qb = $em->GetQueryBuilder();
                $_qb = $em->WhereAllViewsStory($qb, $story->getId(), 3);
                $allViewsExceptCurrentUser[] = $_qb->getQuery()->getResult();

            }


            if ($allViewsExceptCurrentUser > 0) {

                $tempArray = array("user" => $user, "stories" => $currentUserStories, 'storyViewsState' => [], 'views' => $allViewsExceptCurrentUser[0]);


            } else {

                $tempArray = array("user" => $user, "stories" => $currentUserStories, 'storyViewsState' => [], 'views' => []);


            }


            $response = array_merge([$tempArray], $response);

        } else {



            $user = $this->getDoctrine()->getRepository(User::class)->find($currentUser->getId());

            $em = $this->getDoctrine()->getRepository(Story::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->WhereUser($qb, $user);
            $qb = $em->WhereDateIsGraterThan_24($qb);

            $currentUserStories = $qb->getQuery()->getResult();

            if(count($currentUserStories) > 0){

                $allViewsExceptCurrentUser = array();

                for ($j = 0; $j < count($currentUserStories); $j++) {

                    $story = $currentUserStories[$j];

                    $em = $this->getDoctrine()->getRepository(View::class);
                    $qb = $em->GetQueryBuilder();
                    $_qb = $em->WhereAllViewsStory($qb, $story->getId(), 3);
                    $allViewsExceptCurrentUser[] = $_qb->getQuery()->getResult();

                }


                if ($allViewsExceptCurrentUser > 0) {

                    $tempArray = array("user" => $user, "stories" => $currentUserStories, 'storyViewsState' => [], 'views' => $allViewsExceptCurrentUser[0]);


                } else {

                    $tempArray = array("user" => $user, "stories" => $currentUserStories, 'storyViewsState' => [], 'views' => []);


                }

                if(count($currentUserStories) > 0){

                    $response = array_merge([$tempArray], $response);

                }else{
                    $tempArray = array("user" => $currentUser, "stories" => [], 'storyViewsState' => []);
                    $response = array_merge([$tempArray], $response);
                }


            }else{

                $tempArray = array("user" => $currentUser, "stories" => [], 'storyViewsState' => []);
                $response = array_merge([$tempArray], $response);

            }


        }


        return new JsonResponse($response);
    }

    public function contactStories($currentUser, $RAW_QUERY)
    {
        $em = $this->getDoctrine()->getManager();
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('main_user_id', $currentUser->getId());
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getContactBySecondUserIdAction($secondUserId)
    {

        $response = array();
        $currentUser = $this->getUser();

        $secondUser = $this->getDoctrine()->getRepository(User::class)->find($secondUserId);

        if ($secondUser !== null) {

            $em = $this->getDoctrine()->getRepository(Contact::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->WhereSecondUser($qb, $currentUser, $secondUser);
            $response = $qb->getQuery()->getOneOrNullResult();

        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

    public function addStoryAction(Request $request)
    {

        $response = array();
        $currentUser = $this->getUser();

        $updated = array();
        $fileName = '';
        $data = array();

        $file_type = null;

        $fileOriginalName = '';

        $fileSize = 0;

        $prefix = '';

        $videoThumbnail = '';

        if ($currentUser !== null) {


            if (!empty($request->files->get('storyFile'))) {

                $file = $request->files->get('storyFile');

                $uploadDir = $this->getParameter('upload_dir');

                $data = json_decode($_POST['data'], true);

                $dataType = $data['dataType'];

                $fileName = $this->get('ionicapi.fileUploaderManager')->upload($file, $uploadDir, $dataType);

                $fileOriginalName = $file->getClientOriginalName();

                $fileSize = $file->getClientSize();

                $data = $data['objectData'];

                if($dataType == 'story_photos'){
                    $file_type = 'image';
                    $prefix = 'fBfqcChzEM9arevi3hQvX0GC80stybabT1uU6LXtSYqpn10934';
                }if($dataType == 'story_videos'){
                    $file_type = 'video';
                    $prefix = 'tyfBfqcChzEM9ar38sudmlevi3hQvX0GC80stybasbT1uU6LXtSYqpn10934';

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

            if ($fileName !== '') {

                $entityManager = $this->getDoctrine()->getManager();

                $story = new Story();
                $story->setUser($currentUser);
                $story->setCreatedAtAutomatically();

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


                $story->setFile($file);


                $entityManager->persist($story);
                $entityManager->flush();

                $response = $story;

            }else{
                $response = array("code" => "action_not_allowed");
            }


        }else{
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }

}