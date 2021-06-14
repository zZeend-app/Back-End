<?php

namespace ZzeendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use UserBundle\Entity\User;
use ZzeendBundle\Entity\Request;
use ZzeendBundle\Entity\Service;

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
            $qb = $em->WhereRoleNot($qb, "ROLE_SEEKER");
            $response = $qb->getQuery()->getResult();
        }

        return new JsonResponse($response);
    }

    public function userProfileAction($userId){
        $response = array();
        $em = $this->getDoctrine()->getRepository(User::class);
        $user = $em->find($userId);

        $em = $this->getDoctrine()->getRepository(Service::class);
        $qb = $em->GetQueryBuilder();
        $qb = $em->WhereUser($qb, $user);
        $services = $qb->getQuery()->getResult();

        $connectedUserId = $this->getUser()->getId();

        $response['user'] = $user;
        $response['services'] = $services;

        if($connectedUserId !== $userId){
            $connectedUser = $this->getUser();
            $em = $this->getDoctrine()->getRepository(Request::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->WhereUser($qb, $connectedUser);
            $requestSenderObject = $qb->getQuery()->getResult();

            if($requestSenderObject !== null){
                $response['request_already_sent'] = true;
            }
        }

        return new JsonResponse($response);
    }

}
