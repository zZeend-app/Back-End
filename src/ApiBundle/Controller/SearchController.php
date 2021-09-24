<?php


namespace ApiBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class SearchController extends Controller
{
    public function serviceSearchAction(Request $request)
    {
        $data = $request->getContent();
        $data = json_decode($data, true);

        $jsonManager = $this->get("ionicapi.jsonManager");

        //make sure nothing is missing inside data
        $data = $jsonManager->getInclude($data);

        //get restriction
        $filtersInclude = $data["filters"]["include"];

        $searchObject = $filtersInclude['searchObject'];

        $userId = $searchObject['userId'];
        $keyword = $searchObject['keyword'];
        $filter = $searchObject['filter'];

        $response = array();
        if (isset($keyword) and $keyword !== '') {
            $em = $this->getDoctrine()->getRepository(User::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->WhereKeywordLike($qb, $keyword);
        }

        if (!isset($filter)) {
            $qb = $em->OrderByZzeendScore($qb, 0);
        }

        if (isset($filter)) {
            if (array_key_exists('country', $filter) and $filter['country'] !== '') {
                $qb = $em->WhereCountryLike($qb, $filter['country']);
            }

            if (array_key_exists('area', $filter) and $filter['area'] !== '') {
                $qb = $em->WhereAreaLike($qb, $filter['area']);
            }

            if (array_key_exists('specificArea', $filter) and $filter['specificArea'] !== '') {
                $qb = $em->WhereSpecificAreaLike($qb, $filter['specificArea']);

                $qb = $em->WhereAdministrativeAreaOrSubAdministrativeArea($qb, $filter['specificArea']);

                $qb = $em->WhereSubLocality($qb, $filter['specificArea']);
            }

            if (array_key_exists('zZeendScore', $filter)) {
                $qb = $em->OrderByZzeendScore($qb, $filter['zZeendScore']);
            }

            //todo filter by users who hasn't pay thier monthly plan fee (ROLE_OWNER)
        }

        if (isset($keyword) and $keyword !== '') {
            $qb = $em->WhereIdNot($qb, $userId);
            $qb = $em->WhereRoleNot($qb, "ROLE_SEEKER");
            $qb = $em->WhereAccountIsEnabled($qb, true);
            $qb = $em->WhereUserVisibility($qb, true);

            $response = $jsonManager->setQueryLimit($qb, $filtersInclude);
        }

        return new JsonResponse($response);
    }
}