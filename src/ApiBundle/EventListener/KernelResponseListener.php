<?php


namespace ApiBundle\EventListener;


use ApiBundle\Entity\VersionControl;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use UserBundle\Entity\User;

class KernelResponseListener
{
    private $em;
    private $tokenStorage;
    private $array;


    public function __construct(EntityManager $em, TokenStorage $tokenStorage){
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }


    public function onKernelResponse(FilterResponseEvent $event)
    {

        $versionRepo = $this->em->getRepository(VersionControl::class);

        $qbCrmVersion = $versionRepo->GetQueryBuilder();
        $appVersion = $qbCrmVersion->getQuery()->getOneOrNullResult();

        $exposeHeaders = ["app-version"];

        if($this->tokenStorage->getToken())
        {
            if($this->tokenStorage->getToken()->getUser() && $this->tokenStorage->getToken()->getUser() instanceof User)
            {
                $this->array = $this->tokenStorage->getToken()->getUser()->getRoles();
                $event->getResponse()->headers->add(["role" => $this->array[0] ]);
               $exposeHeaders[] = "role";
            }
        }

        $event->getResponse()->headers->add(["app-version" => $appVersion->getVersion()]);
        $event->getResponse()->headers->add(["access-control-expose-headers" => $exposeHeaders]);


        return $event;
    }


    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse',10),
        );
    }
}
