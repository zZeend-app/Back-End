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
 * Service
 *
 * @ORM\Table(name="`filnance`")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\FinanceRepository")
 */

class Finance implements JsonSerializable
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
     * @var float
     *
     * @ORM\Column(name="cash", type="float", length=255, unique=false, nullable=false)
     */
    private $cash;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\FinancialStatus", inversedBy="services")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $financialStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="activity_description", type="string", length=255, unique=false, nullable=false)
     */
    private $activityDescription;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;


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
     * Set user.
     *
     * @param User $user
     *
     * @return User
     */
    public function getUser($user)
    {
        return $this->user = $user;
    }

    /**
     * Set user.
     *
     * @param FinancialStatus $financialStatus
     *
     * @return void
     */
    public function setFinancialStatus($financialStatus)
    {
        $this->financialStatus = $financialStatus;
    }

    /**
     * Set financialStatus.
     *
     * @param FinancialStatus $financialStatus
     *
     * @return FinancialStatus
     */
    public function getFinancialStatus($financialStatus)
    {
        return $this->financialStatus = $financialStatus;
    }



    public function setCash(string $cash)
    {
        $this->cash = $cash;

        return $cash;
    }


    /**
     * Get cash.
     *
     * @return float
     */
    public function getCash()
    {
        return $this->cash;
    }

    public function setActivityDescription(string $activityDescription)
    {
        $this->activityDescription = $activityDescription;

        return $activityDescription;
    }


    /**
     * Get cash.
     *
     * @return string
     */
    public function getactivityDescription()
    {
        return $this->activityDescription;
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

        if(!$entityClass instanceof Finance || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof Finance || in_array("cash",$include)){
            $json["cash"] = $this->cash;
        }

        if(!$entityClass instanceof Finance || in_array("financialStatus",$include)){
            $json["financialStatus"] = $this->financialStatus;
        }

        if(!$entityClass instanceof Finance || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        return $json;
    }
}