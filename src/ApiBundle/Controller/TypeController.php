<?php


namespace ApiBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class TypeController
{

    public function serviceSearch(Request  $request){
        $data = $request->getContent();
        $data = json_decode($data, true);

        $keyword = $data['keyword'];
        $filter = $data['filter'];

        $country = $filter['country'];
        $city = $filter['filter'];
        $area = $filter['area'];
        $address = $filter['address'];

        $em = $this->getDoctrine()->getRepository(User::class);
        $qb = $em->GetQueryBuilder();

    }

}