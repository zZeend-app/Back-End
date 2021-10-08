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
 * @ORM\Entity
 * @ORM\Table(name="`transaction`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\TransactionRepository")
 */

class Transaction implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="services")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\PaymentType", inversedBy="payment_types")
     * @ORM\JoinColumn(name="payment_type", referencedColumnName="id")
     */
    private $paymentType;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\PaymentMethod", inversedBy="payment_types")
     * @ORM\JoinColumn(name="payment_method", referencedColumnName="id")
     */
    private $paymentMethod;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_intent_id", type="string", length=255, unique=false, nullable=false)
     */
    private $paymentIntentId;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * Set PaymentType.
     *
     * @param PaymentType
     *
     * @return void
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;
    }


    /**
     * Get paymentType.
     *
     * @return PaymentType
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * Set PaymentMethod.
     *
     * @param PaymentMethod
     *
     * @return void
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }


    /**
     * Get paymentMethod.
     *
     * @return PaymentMethod
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set user.
     *
     * @param User $user
     *
     * @return void
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set paymentIntentId.
     *
     * @return void
     */
    public function setPaymentIntentId($paymentIntentId)
    {
        $this->paymentIntentId = $paymentIntentId;
    }


    /**
     * Get paymentIntentId.
     *
     * @return string
     */
    public function getPaymentIntentId()
    {
        return $this->paymentIntentId;
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


        if(!$entityClass instanceof Transaction || in_array("user",$include)){
            $json["user"] = $this->user;
        }


        if(!$entityClass instanceof Transaction || in_array("paymentIntentId",$include)){
            $json["paymentIntentId"] = $this->paymentIntentId;
        }

        if(!$entityClass instanceof Transaction || in_array("paymentMethod",$include)){
            $json["paymentMethod"] = $this->paymentMethod;
        }

        if(!$entityClass instanceof Transaction || in_array("paymentType",$include)){
            $json["paymentType"] = $this->paymentType;
        }

        if(!$entityClass instanceof Transaction || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }


        return $json;
    }
}