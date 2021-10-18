<?php


namespace ApiBundle\DataFixtures\ORM;


use ApiBundle\Entity\PaymentType;
use ApiBundle\Entity\Plan;
use ApiBundle\Entity\ZzeendService;
use ApiBundle\Entity\ZzeendStatus;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadZzeendService implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {


        $zZeendService = new ZzeendService();
        $zZeendService->setStartFees('10');
        $zZeendService->setEndFees('300');
        $zZeendService->setApplicationFees('5');
        $zZeendService->setNbZzeendPoint('10');
        $manager->persist($zZeendService);


        $zZeendService = new ZzeendService();
        $zZeendService->setStartFees('301');
        $zZeendService->setEndFees('700');
        $zZeendService->setApplicationFees('15');
        $zZeendService->setNbZzeendPoint('25');
        $manager->persist($zZeendService);


        $zZeendService = new ZzeendService();
        $zZeendService->setStartFees('701');
        $zZeendService->setEndFees('1000');
        $zZeendService->setApplicationFees('20');
        $zZeendService->setNbZzeendPoint('35');
        $manager->persist($zZeendService);


        $zZeendService = new ZzeendService();
        $zZeendService->setStartFees('1001');
        $zZeendService->setEndFees('2500');
        $zZeendService->setApplicationFees('30');
        $zZeendService->setNbZzeendPoint('50');
        $manager->persist($zZeendService);


        $zZeendService = new ZzeendService();
        $zZeendService->setStartFees('2501');
        $zZeendService->setEndFees('5000');
        $zZeendService->setApplicationFees('80');
        $zZeendService->setZzeendPoint('70');
        $manager->persist($zZeendService);

        $zZeendService = new ZzeendService();
        $zZeendService->setStartFees('5001');
        $zZeendService->setEndFees('1');
        $zZeendService->setApplicationFees('150');
        $zZeendService->setZzeendPoint('95');
        $manager->persist($zZeendService);

        $manager->flush();


    }
}