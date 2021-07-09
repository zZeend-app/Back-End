<?php


namespace ApiBundle\EventListener;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use UserBundle\Entity\User;

class KernelResponseListener
{
//    private $em;
//    private $tokenStorage;
//    private $currentRole;
//
//    public function __construct(EntityManager $em, TokenStorage $tokenStorage){
//        $this->em = $em;
//        $this->tokenStorage = $tokenStorage;
//    }


    public function onKernelResponse(FilterResponseEvent $event)
    {

//        $versionRepo = $this->em->getRepository('IonicAPIBundle:VersionControl');
//
//        $qbCrmVersion = $versionRepo->GetQueryBuilder();
//        $crmVersion = $qbCrmVersion->getQuery()->getOneOrNullResult();
//
//        $exposeHeaders = ["webapp-version"];
//
//        if($this->tokenStorage->getToken())
//        {
//            if($this->tokenStorage->getToken()->getUser() && $this->tokenStorage->getToken()->getUser() instanceof User)
//            {
//                $array = $this->tokenStorage->getToken()->getUser()->getRoles();
//                $this->currentRole = "ROLE_USER";
//                foreach ($array as $val)
//                {
//                    if($val === "ROLE_ADMIN"){
//                        $this->currentRole  = $val;
//                        break;
//                    }
//                    if($val === "ROLE_DIRECTEUR"){
//                        $this->currentRole  = $val;
//                    }
//                }
//
//                $event->getResponse()->headers->add(["web-app-user-role" => $this->currentRole ]);
//                $exposeHeaders[] = "web-app-user-role";
//            }
//        }
//
//
//        $event->getResponse()->headers->add(["webapp-version" => $crmVersion->getVersion()]);
//        $event->getResponse()->headers->add(["access-control-expose-headers" => $exposeHeaders]);



        return $event;
    }


    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse',10),
        );
    }
}
