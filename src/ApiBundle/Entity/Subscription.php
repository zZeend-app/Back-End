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
 * @ORM\Entity
 * @ORM\Table(name="`subscription`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\SubscriptionRepository")
 */

class Subscription implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="subscriptions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\Plan", inversedBy="subscriptions")
     * @ORM\JoinColumn(name="plan_id", referencedColumnName="id")
     */
    private $plan;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\RenewalType", inversedBy="subscriptions")
     * @ORM\JoinColumn(name="renewal_type_id", referencedColumnName="id")
     */
    private $renewalType;


    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", length=255, unique=false, nullable=false)
     */
    private $active;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;



    /**
     * Set user.
     *
     * @param $user
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
     * Set plan.
     *
     * @param $plan
     *
     * @return void
     */
    public function setPlan($plan)
    {
        $this->plan = $plan;
    }

    /**
     * Get plan.
     *
     * @return Plan
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * Set renewalType.
     *
     * @param $renewalType
     *
     * @return void
     */
    public function setRenewalType($renewalType)
    {
        $this->renewalType = $renewalType;
    }

    /**
     * Get renewalType.
     *
     * @return RenewalType
     */
    public function getRenewalType()
    {
        return $this->renewalType;
    }

    /**
     * Set active.
     *
     * @param boolean $active
     *
     */
    public function setActive(bool $active)
    {
        $this->active = $active;

    }


    /**
     * Get active.
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
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

        if(!$entityClass instanceof Subscription || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof Subscription || in_array("plan",$include)){
            $json["plan"] = $this->plan;
        }

        if(!$entityClass instanceof Subscription || in_array("renewalType",$include)){
            $json["renewalType"] = $this->renewalType;
        }

        if(!$entityClass instanceof Subscription || in_array("active",$include)){
            $json["active"] = $this->active;
        }

        if(!$entityClass instanceof Subscription || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        if(!$entityClass instanceof Subscription || in_array("updatedAt",$include)){
            $json["updatedAt"] = $this->updatedAt;
        }

        return $json;
    }
}