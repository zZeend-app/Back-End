<?php


namespace ApiBundle\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use UserBundle\Entity\AccountVerification;
use JsonSerializable;
use UserBundle\Entity\User;

/**
 * Zzeend
 *
 * @ORM\Table(name="`zzeend`")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\ZzeendRepository")
 */

class Zzeend implements JsonSerializable
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
     * @ORM\Column(name="title", type="string", length=255, unique=false, nullable=false)
     */
    private $title;

    /**
     * @var float
     *
     * @ORM\Column(name="cost", type="float", unique=false, nullable=false)
     */
    private $cost;

    /**
     * @ORM\Column(name="zzeend_from", type="datetime", nullable=true)
     */
    private $from;

    /**
     * @ORM\Column(name="zzeend_to", type="datetime", nullable=true)
     */
    private $to;

    /**
     * @ORM\Column(name="payment_limit_date", type="datetime", nullable=true)
     */
    private $paymentLimitDate;


    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="zzeends")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="zzeends")
     * @ORM\JoinColumn(name="user_id_assigned", referencedColumnName="id")
     */
    private $userAssigned;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\ZzeendStatus", inversedBy="zzeends")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\Transaction", inversedBy="zzeends")
     * @ORM\JoinColumn(name="transaction_id", referencedColumnName="id")
     */
    private $transaction;

    /**
     * @var boolean
     *
     * @ORM\Column(name="done", type="boolean", unique=false, nullable=false)
     */
    private $done;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * Set title.
     *
     * @param string $title
     *
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

    }


    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set cost.
     *
     * @param float $cost
     *
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

    }


    /**
     * Get cost.
     *
     * @return string
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set from.
     *
     * @param datetime
     *
     */
    public function setFrom($from)
    {
        $this->from = $from;

    }


    /**
     * Get from.
     *
     * @return datetime
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to.
     *
     * @param datetime
     *
     */
    public function setTo($to)
    {
        $this->to = $to;

    }


    /**
     * Get to.
     *
     * @return datetime
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set user.
     *
     *
     * @return void
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Set user.
     *
     * @param User $user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set userAssigned.
     *
     *
     * @return void
     */
    public function setUserAssigned($userAssigned)
    {
        $this->userAssigned = $userAssigned;
    }

    /**
     * Set userAssigned.
     *
     * @param User $userAssigned
     *
     * @return User
     */
    public function getUserAssigned($userAssigned)
    {
        return $this->userAssigned = $userAssigned;
    }

    /**
     * Set user.
     *
     *
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Set status.
     *
     * @param ZzeendStatus $status
     *
     * @return ZzeendStatus
     */
    public function getStatus($status)
    {
        return $this->status = $status;
    }


    /**
     * Set to.
     *
     * @param Transaction
     *
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;

    }


    /**
     * Get to.
     *
     * @return Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set done.
     *
     * @param boolean $done
     *
     */
    public function setDone(string $done)
    {
        $this->done = $done;

    }


    /**
     * Get done.
     *
     * @return boolean
     */
    public function getDone()
    {
        return $this->done;
    }

    /**
     * Set paymentLimitDate.
     *
     * @param datetime
     *
     */
    public function setPaymentLimitDate($paymentLimitDate)
    {
        $this->paymentLimitDate = $paymentLimitDate;

    }


    /**
     * Get paymentLimitDate.
     *
     * @return datetime
     */
    public function getPaymentLimitDate()
    {
        return $this->paymentLimitDate;
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


    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $timestamp): self
    {
        $this->updatedAt = $timestamp;
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

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAtAutomatically()
    {
        $this->setUpdatedAt(new DateTime());
    }


    public function jsonSerialize($entityClass = null,$include = [])
    {
        $json = [
            "id" => $this->id,
        ];

        if(!$entityClass instanceof Zzeend || in_array("title",$include)){
            $json["title"] = $this->title;
        }

        if(!$entityClass instanceof Zzeend || in_array("cost",$include)){
            $json["cost"] = $this->cost;
        }

        if(!$entityClass instanceof Zzeend || in_array("from",$include)){
            $json["from"] = $this->from;
        }


        if(!$entityClass instanceof Zzeend || in_array("to",$include)){
            $json["to"] = $this->to;
        }

        if(!$entityClass instanceof Zzeend || in_array("paymentLimitDate",$include)){
            $json["paymentLimitDate"] = $this->paymentLimitDate;
        }

        if(!$entityClass instanceof Zzeend || in_array("title",$include)){
            $json["paymentLimitDate"] = $this->paymentLimitDate;
        }

        if(!$entityClass instanceof Zzeend || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof Zzeend || in_array("userAssigned",$include)){
            $json["userAssigned"] = $this->userAssigned;
        }

        if(!$entityClass instanceof Zzeend || in_array("status",$include)){
            $json["status"] = $this->status;
        }

        if(!$entityClass instanceof Zzeend || in_array("transaction",$include)){
            $json["transaction"] = $this->transaction;
        }

        if(!$entityClass instanceof Zzeend || in_array("done",$include)){
            $json["done"] = $this->done;
        }

        if(!$entityClass instanceof Zzeend || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        if(!$entityClass instanceof Zzeend || in_array("updatedAt",$include)){
            $json["updatedAt"] = $this->updatedAt;
        }


        return $json;
    }
}