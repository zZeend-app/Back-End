<?php


namespace ApiBundle\DataFixtures\ORM;


use ApiBundle\Entity\PaymentType;
use ApiBundle\Entity\Plan;
use ApiBundle\Entity\ZzeendStatus;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadPlan implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $plans = array(
            "24.99" => "1 month",
            "23.00" => "6 months",
            "21.99" => "9 months"
        );

        foreach ($plans as $price => $duration) {

            $plan = new Plan();
            $plan->setPrice($price);
            $plan->setDuration($duration);

            $manager->persist($plan);
        }

        $manager->flush();


    }
}