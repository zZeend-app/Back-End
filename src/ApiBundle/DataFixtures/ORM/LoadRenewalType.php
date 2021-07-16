<?php


namespace ApiBundle\DataFixtures\ORM;


use ApiBundle\Entity\PaymentType;
use ApiBundle\Entity\RenewalType;
use ApiBundle\Entity\ZzeendStatus;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadRenewalType implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $paymentTypes = array(
            'auto',
            'self'
        );

        foreach ($paymentTypes as $paymentType) {

            $renewalEntity = new RenewalType();
            $renewalEntity->setTitle($paymentType);

            $manager->persist($renewalEntity);
        }

        $manager->flush();


    }
}