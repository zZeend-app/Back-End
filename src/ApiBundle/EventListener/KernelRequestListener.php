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
    private $em;
    private $api_keys;


    public function __construct($apiKeys)
    {
        $this->api_keys = $apiKeys;
    }


    public function onKernelRequest(GetResponseEvent $event)
    {

        $response = array("code" => "auth/unknown_source");
        $headers = $event->getRequest()->headers;

        if ($headers->has('ApiKey')) {

            $ApiKey = $headers->get('ApiKey');
            $app_authentication_api = $this->api_keys['app_authentication_api'];

            if ($ApiKey === $app_authentication_api) {
                return $event;
            }

        }

        $event->setResponse(new JsonResponse( $response, 403));

    }


    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse', 10),
        );
    }


}
