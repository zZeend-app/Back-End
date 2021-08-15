<?php


namespace ApiBundle\Manager;


use Psr\Container\ContainerInterface;

class EmailManager
{

    private $mailer;
    private $template;

    public function __construct(ContainerInterface $container)
    {
        $this->mailer = $container->get('mailer');
        $this->template = $container->get('templating');
    }

    public function send($from, $to, $subject, $templatePath, $arrayDatas = array()){

        $message = (new \Swift_Message($subject))->setFrom($from, 'zZeend')
            ->setTo($to)
            ->setBody( $this->template->render($templatePath, $arrayDatas), 'text/html');

        $this->mailer->send($message);

    }






}