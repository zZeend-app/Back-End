<?php


namespace ApiBundle\EventListener;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use UserBundle\Entity\User;

class KernelRequestListener
{
    private $api_keys;


    public function __construct($apiKeys)
    {
        $this->api_keys = $apiKeys;
    }


    public function onKernelRequest(GetResponseEvent $event)
    {

        if(strpos($event->getRequest()->getUri(), 'media/file') ||
            strpos($event->getRequest()->getUri(), 'refresh-link') ||
            strpos($event->getRequest()->getUri(), 'return-link') ||
            strpos($event->getRequest()->getUri(), 'password-recovery')){

            return $event;

        }else{

            $response = array("code" => "auth/unknown_source");
            $headers = $event->getRequest()->headers;

            if ($headers->has('Source-Key')) {

                $sourceKey = $headers->get('Source-Key');
                $app_source_key = $this->api_keys['app-source-key'];

                if ($sourceKey === $app_source_key) {
                    return $event;
                }

            }

            $event->setResponse(new JsonResponse( $response, 403));

        }

    }


    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelRequest', 240),
        );
    }


}
