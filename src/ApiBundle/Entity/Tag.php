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
 * @ORM\Table(name="`tag`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\TagRepository")
 */

class Tag implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="tags")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, unique=false, nullable=false)
     */
    private $title;

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
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title.
     *
     */
    public function setTitle($title)
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
     * Set active.
     *
     * @param boolean $active
     *
     */
    public function setActive($active)
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

    /**
     * Set user.
     *
     * @param User $user
     *
     * @return void
     */
    public function getUser()
    {
        return $this->user;
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

        if(!$entityClass instanceof Tag || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof Tag || in_array("title",$include)){
            $json["title"] = $this->title;
        }

        if(!$entityClass instanceof PaymentMethod || in_array("active",$include)){
            $json["active"] = $this->active;
        }

        if(!$entityClass instanceof Tag || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        if(!$entityClass instanceof Tag || in_array("updatedAt",$include)){
            $json["updatedAt"] = $this->updatedAt;
        }

        return $json;
    }
}