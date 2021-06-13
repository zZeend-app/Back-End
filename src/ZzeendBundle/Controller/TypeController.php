<?php

namespace ZzeendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use UserBundle\Entity\User;

class TypeController extends Controller
{
    public function serviceSearchAction($userId, $keyword, $filter)
    {

        $response = array();
        if(isset($keyword) AND $keyword !== ''){
            $em = $this->getDoctrine()->getRepository(User::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->WhereKeywordLike($qb, $keyword);
        }

        if(isset($filter)){
            if(array_key_exists('country', $filter) and $filter['country'] !== ''){
                $qb = $em->WhereCountryLike($qb, $filter['country']);
            }

            if(array_key_exists('area', $filter) and $filter['area'] !== ''){
                $qb = $em->WhereAreaLike($qb, $filter['area']);
            }

            if(array_key_exists('specificArea', $filter) and $filter['specificArea'] !== ''){
                $qb = $em->WhereSpecificAreaLike($qb, $filter['specificArea']);
            }

            if(array_key_exists('zZeendScore', $filter)){
               // $em->WhereAreaLike($qb, $filter['zZeendScore']);
            }

            //todo filter by users who has an account enabled(0)
            //todo filter by users who are not service Owners (ROLE_OWNER)
            //todo filter by users who hasn't pay thier monthly plan fee (ROLE_OWNER)
        }

        if(isset($keyword) AND $keyword !== '') {
            $qb = $em->WhereIdNot($qb, $userId);
            $response = $qb->getQuery()->getResult();
        }

        return new JsonResponse($response);
    }

}
