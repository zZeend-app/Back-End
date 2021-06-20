<?php


namespace ApiBundle\DataFixtures\ORM;


use ApiBundle\Entity\PaymentType;
use ApiBundle\Entity\ZzeendStatus;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadPaymentType implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $paymentTypes = array(
            'Credit Card',
            'Paypal'
        );

        foreach ($paymentTypes as $paymentType) {

            $paymentTypeEntity = new PaymentType();
            $paymentTypeEntity->setTitle($paymentType);

            $manager->persist($paymentTypeEntity);
        }

        $manager->flush();


    }
}