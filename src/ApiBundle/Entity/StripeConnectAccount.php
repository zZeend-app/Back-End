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
 * StripeConnectAccount
 * @ORM\Entity
 * @ORM\Table(name="`stripe_connect_account`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\StripeConnectedAccountRepository")
 */

class StripeConnectAccount implements JsonSerializable
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
     * @var string
     *
     * @ORM\Column(name="stripe_account_id", type="string", length=255, unique=false, nullable=false)
     */
    private $stripeAccountId;

    /**
     * @ORM\Column(name="active", type="datetime", nullable=true)
     */
    private $active;

    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set stripeAccountId.
     *
     * @param string $stripeAccountId
     *
     */
    public function setStripeAccountId(string $stripeAccountId)
    {
        $this->stripeAccountId = $stripeAccountId;

    }


    /**
     * Get stripeAccountId.
     *
     * @return string
     */
    public function getStripeAccountId()
    {
        return $this->stripeAccountId;
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

    public function setActive(?DateTimeInterface $timestamp): self
    {
        $this->active = $timestamp;
        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setActiveAutomatically()
    {
        if ($this->getActive() === null) {
            $this->setActive(new \DateTime());
        }
    }

    public function getActive(): ?DateTimeInterface
    {
        return $this->active;
    }

    public function jsonSerialize($entityClass = null,$include = [])
    {
        $json = [
            "id" => $this->id,
        ];

        if(!$entityClass instanceof StripeConnectAccount || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof PaymentMethod || in_array("active",$include)){
            $json["active"] = $this->active;
        }

        if(!$entityClass instanceof StripeConnectAccount || in_array("stripeAccountId",$include)){
            $json["stripeAccountId"] = $this->stripeAccountId;
        }

        return $json;
    }
}