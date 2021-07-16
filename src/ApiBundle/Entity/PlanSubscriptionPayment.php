<?php


namespace ApiBundle\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use UserBundle\Entity\AccountVerification;
use JsonSerializable;
use UserBundle\Entity\User;

/**
 * Service
 *
 * @ORM\Table(name="`plan_subscription_payment`")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\PlanSubscriptionPaymentRepository")
 */

class PlanSubscriptionPayment implements JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\Subscription", inversedBy="planSubscriptionPayments")
     * @ORM\JoinColumn(name="subscription_id", referencedColumnName="id")
     */
    private $subscription;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\Transaction", inversedBy="planSubscriptionPayments")
     * @ORM\JoinColumn(name="transaction_id", referencedColumnName="id")
     */
    private $transaction;


    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;


    /**
     * Set subscription.
     *
     *
     * @return void
     */
    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Get subscription.
     *
     * @return Subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * Set transaction.
     *
     *
     * @return void
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get transaction.
     *
     * @return Transaction
     */
    public function getTransactionn()
    {
        return $this->transaction;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $timestamp): self
    {
        $this->createdAt = $timestamp;
        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtAutomatically()
    {
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTime());
        }
    }

    public function jsonSerialize($entityClass = null,$include = [])
    {
        $json = [
            "id" => $this->id,
        ];

        if(!$entityClass instanceof PlanSubscriptionPayment || in_array("subscription",$include)){
            $json["subscription"] = $this->subscription;
        }

        if(!$entityClass instanceof PlanSubscriptionPayment || in_array("transaction",$include)){
            $json["transaction"] = $this->transaction;
        }

        if(!$entityClass instanceof PlanSubscriptionPayment || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        return $json;
    }
}