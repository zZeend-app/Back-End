<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\Contact;
use ApiBundle\Entity\Post;
use ApiBundle\Entity\View;
use ApiBundle\Entity\ViewType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ViewController extends Controller
{

    public function addViewAction(Request $request)
    {
        $response = array();
        $currentUser = $this->getUser();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $related_id = $data['related_id'];
        $view_type_id = $data['view_type_id'];

        $viewType = $this->getDoctrine()->getRepository(ViewType::class)->find($view_type_id);

        if ($viewType !== null) {

            $em = $this->getDoctrine()->getRepository(View::class);
            $qb = $em->GetQueryBuilder();
            $qb = $em->AndWhereUser($qb, $currentUser);
            $qb = $em->AndWhereRelatedId($qb, $related_id);
            $qb = $em->AndWhereViewType($qb, $viewType);

            $oldView = $qb->getQuery()->getOneOrNullResult();

            if ($oldView === null) {

                $entityManager = $this->getDoctrine()->getManager();

                $view = new View();
                $view->setUser($currentUser);
                $view->setRelatedId($related_id);
                $view->setViewType($viewType);
                $view->setCreatedAtAutomatically();

                $entityManager->persist($view);
                $entityManager->flush();

                $response = array("code" => "content_viwed");

            } else {
                $response = array("code" => "already_viwed");
            }

        } else {
            $response = array("code" => "action_not_allowed");
        }

        return new JsonResponse($response);
    }


}