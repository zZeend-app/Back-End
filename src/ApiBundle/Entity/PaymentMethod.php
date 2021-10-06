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
 * @ORM\Table(name="`payment_method`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\PaymentMethodRepository")
 */

class PaymentMethod implements JsonSerializable
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
     * @ORM\Column(name="brand", type="string", length=255, unique=false, nullable=false)
     */
    private $brand;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, unique=false, nullable=false)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="last_four_digit", type="string", length=255, unique=false, nullable=false)
     */
    private $lastFourDigit;

    /**
     * @var string
     *
     * @ORM\Column(name="exp_month", type="string", length=255, unique=false, nullable=false)
     */
    private $expMonth;

    /**
     * @var string
     *
     * @ORM\Column(name="exp_year", type="string", length=255, unique=false, nullable=false)
     */
    private $expYear;

    /**
     * @var string
     *
     * @ORM\Column(name="funding", type="string", length=255, unique=false, nullable=false)
     */
    private $funding;

    /**
     * @var string
     *
     * @ORM\Column(name="csv", type="string", length=255, unique=false, nullable=false)
     */
    private $csv;

    /**
     * @var boolean
     *
     * @ORM\Column(name="main", type="boolean", length=255, unique=false, nullable=false)
     */
    private $main;


    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="services")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * Set brand.
     *
     * @param string $brand
     *
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }



    /**
     * Get brand.
     *
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set token.
     *
     * @param string $token
     *
     */
    public function setToken($token)
    {
        $this->token = $token;
    }



    /**
     * Get token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }


    /**
     * Set lastFourDigit.
     *
     * @param string $lastFourDigit
     *
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
     * Set expMonth.
     *
     * @param string $exp_month
     *
     */
    public function setExpMonth($expMonth)
    {
        $this->expMonth = $expMonth;
    }



    /**
     * Get expMonth.
     *
     * @return string
     */
    public function getExpMonth()
    {
        return $this->expMonth;
    }


    /**
     * Set expYear.
     *
     * @param string $expYear
     *
     */
    public function setExpYear($expYear)
    {
        $this->expYear = $expYear;
    }



    /**
     * Get expYear.
     *
     * @return string
     */
    public function getExpYear()
    {
        return $this->expYear;
    }

    /**
     * Set funding.
     *
     * @param string $funding
     *
     */
    public function setFunding($funding)
    {
        $this->funding = $funding;
    }



    /**
     * Get funding.
     *
     * @return string
     */
    public function getFunding()
    {
        return $this->funding;
    }

    /**
     * Set csv.
     *
     * @param string $csv
     *
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
     * Set main.
     *
     * @param string $main
     *
     */
    public function setMain($mainFlag)
    {
        $this->main = $mainFlag;
    }



    /**
     * Get main.
     *
     * @return boolean
     */
    public function getMain()
    {
        return $this->main;
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

        if(!$entityClass instanceof PaymentMethod || in_array("brand",$include)){
            $json["brand"] = $this->brand;
        }

        if(!$entityClass instanceof PaymentMethod || in_array("token",$include)){
            $json["token"] = $this->token;
        }

        if(!$entityClass instanceof PaymentMethod || in_array("lastFourDigit",$include)){
            $json["lastFourDigit"] = $this->lastFourDigit;
        }

        if(!$entityClass instanceof PaymentMethod || in_array("expMonth",$include)){
            $json["expMonth"] = $this->expMonth;
        }

        if(!$entityClass instanceof PaymentMethod || in_array("expYear",$include)){
            $json["expYear"] = $this->expYear;
        }

        if(!$entityClass instanceof PaymentMethod || in_array("csv",$include)){
            $json["csv"] = $this->csv;
        }

        if(!$entityClass instanceof PaymentMethod || in_array("funding",$include)){
            $json["funding"] = $this->funding;
        }

        if(!$entityClass instanceof PaymentMethod || in_array("main",$include)){
            $json["main"] = $this->main;
        }

        if(!$entityClass instanceof PaymentMethod || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof PaymentMethod || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        if(!$entityClass instanceof PaymentMethod || in_array("updatedAt",$include)){
            $json["updatedAt"] = $this->updatedAt;
        }

        return $json;
    }
}