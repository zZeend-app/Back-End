<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\SearchKeyword;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchKeywordController extends Controller
{

    public function addKeywordAction(Request $request)
    {

        $response = array();
        $data = $request->getContent();
        $data = json_decode($data, true);

        $keyword = $data['keyword'];

        $currentUser = $this->getUser();

        //check if user already searched for this keyword

        $em = $this->getDoctrine()->getRepository(SearchKeyword::class);

        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereUser($qb, $currentUser);
        $qb = $em->WhereKeyword($qb, $keyword);

        $searchKeyword = $qb->getQuery()->getOneOrNullResult();
        
        if($searchKeyword !== null){
            $entityManager = $this->getDoctrine()->getManager();
            
            $entityManager->remove($searchKeyword);
            $entityManager->flush();

            $entityManager->persist($searchKeyword);
            $entityManager->flush();

            $response = array('code' => "keyword_added");

        }else{

            // user never search for this keyword, so add it to the database

            $entityManager = $this->getDoctrine()->getManager();

            $searchKeyword = new SearchKeyWord();
            $searchKeyword->setUser($currentUser);
            $searchKeyword->setKeyWord($keyword);
            $searchKeyword->setCreatedAtAutomatically();

            $entityManager->persist($searchKeyword);
            $entityManager->flush();

            $response = array('code' => "keyword_added");

        }
       

       return new JsonResponse($response);
    }
    
    public function getKeywordsAction(Request $request){

        $response = array();
        $postResponses = array();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $currentUser = $this->getUser();

        $jsonManager = $this->get("ionicapi.jsonManager");

        //make sure nothing is missing inside data
        $data = $jsonManager->getInclude($data);

        //get restriction
        $filtersInclude = $data["filters"]["include"];

        $em = $this->getDoctrine()->getRepository(SearchKeyword::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereUser($qb, $currentUser);

        if (array_key_exists("order", $data)) {
            $qb = $em->OrderByJson($qb, $data["order"]);
        }

        $keywords = $jsonManager->setQueryLimit($qb, $filtersInclude);


        return new JsonResponse($keywords);


    }


}