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
 * @ORM\Table(name="`social_network`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\SocialNetworkRepository")
 */

class SocialNetwork implements JsonSerializable
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
     * @ORM\Column(name="link", type="string", length=255, unique=false, nullable=false)
     */
    private $link;


    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="services")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\SocialNetworkType", inversedBy="socialmedias")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $socialNetworkType;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * Set link.
     *
     * @param string $link
     *
     */
    public function setLink(string $link)
    {
        $this->link = $link;

    }


    /**
     * Get link.
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
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
     * @return void
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set socialNetworkType.
     *
     * @param SocialNetworkType $socialNetworkType
     *
     * @return void
     */
    public function setSocialNetworkType($socialNetworkType)
    {
        $this->socialNetworkType = $socialNetworkType;
    }

    /**
     * Get socialNetworkType.
     *
     * @return void
     */
    public function getSocialNetworkType()
    {
        return $this->socialNetworkType;
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

        if(!$entityClass instanceof SocialNetwork || in_array("link",$include)){
            $json["link"] = $this->link;
        }

        if(!$entityClass instanceof SocialNetwork || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof SocialNetwork || in_array("socialNetworkType",$include)){
            $json["socialNetworkType"] = $this->socialNetworkType;
        }

        if(!$entityClass instanceof SocialNetwork || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        if(!$entityClass instanceof SocialNetwork || in_array("updatedA",$include)){
            $json["updatedAt"] = $this->updatedAt;
        }

        return $json;
    }
}