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
     * @var string
     *
     * @ORM\Column(name="bank_transaction_id", type="string", length=255, unique=false, nullable=false)
     */
    private $bankTransactionId;


    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="services")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="card", type="string", length=255, unique=false, nullable=false)
     */
    private $card;

    /**
     * @var string
     *
     * @ORM\Column(name="last_four_digit", type="string", length=255, unique=false, nullable=false)
     */
    private $lastFourDigit;

    /**
     * @var string
     *
     * @ORM\Column(name="expiration_date", type="string", length=255, unique=false, nullable=false)
     */
    private $expirationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="csv", type="string", length=255, unique=false, nullable=false)
     */
    private $csv;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\PaymentType", inversedBy="payment_types")
     * @ORM\JoinColumn(name="payment_type", referencedColumnName="id")
     */
    private $paymentType;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * Set bankTransactionId.
     *
     * @param String
     *
     * @return void
     */
    public function setBankTransactionId($bankTransactionId)
    {
        $this->bankTransactionId = $bankTransactionId;
    }


    /**
     * Get bankTransactionId.
     *
     * @return string
     */
    public function getBankTransactionId()
    {
        return $this->bankTransactionId;
    }

    /**
     * Set bankTransactionId.
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
     * Set lastFourDigit.
     *
     * @param String
     *
     * @return void
     */
    public function setLastFourDigit($lastFourDigit)
    {
        $this->lastFourDigit = $lastFourDigit;
    }


    /**
     * Get lastFourDigit.
     *
     * @return string
     */
    public function getLastFourDigit()
    {
        return $this->lastFourDigit;
    }

    /**
     * Set expirationDate.
     *
     * @param String
     *
     * @return void
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
    }


    /**
     * Get expirationDate.
     *
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * Set csv.
     *
     * @param String
     *
     * @return void
     */
    public function setCsv($csv)
    {
        $this->csv = $csv;
    }


    /**
     * Get csv.
     *
     * @return string
     */
    public function getCsv()
    {
        return $this->csv;
    }

    /**
     * Set card.
     *
     * @param String
     *
     * @return void
     */
    public function setCard($card)
    {
        $this->card = $card;
    }


    /**
     * Get card.
     *
     * @return string
     */
    public function getCard()
    {
        return $this->card;
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

        if(!$entityClass instanceof Transaction || in_array("bankTransactionId",$include)){
            $json["bankTransactionId"] = $this->bankTransactionId;
        }

        if(!$entityClass instanceof Transaction || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof Transaction || in_array("lastFourDigit",$include)){
            $json["lastFourDigit"] = $this->lastFourDigit;
        }

        if(!$entityClass instanceof Transaction || in_array("expirationDate",$include)){
            $json["expirationDate"] = $this->expirationDate;
        }

        if(!$entityClass instanceof Transaction || in_array("csv",$include)){
            $json["csv"] = $this->csv;
        }

        if(!$entityClass instanceof Transaction || in_array("card",$include)){
            $json["card"] = $this->card;
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