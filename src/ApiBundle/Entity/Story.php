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
 * @ORM\Table(name="`story`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\StoryRepository")
 */
class Story implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\File", inversedBy="files")
     * @ORM\JoinColumn(name="file", referencedColumnName="id")
     */
    private $file;

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
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * Set file.
     *
     * @param File $file
     *
     * @return void
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }


    /**
     * Get active
     * @return string
     */
    public function getActive()
    {
        return $this->active;
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


    public function jsonSerialize($entityClass = null, $include = [])
    {
        $json = [
            "id" => $this->id,
        ];

        if (!$entityClass instanceof Story || in_array("user", $include)) {
            $json["user"] = $this->user;
        }

        if (!$entityClass instanceof Story || in_array("file", $include)) {
            $json["file"] = $this->file;
        }

        if (!$entityClass instanceof Story || in_array("active", $include)) {
            $json["active"] = $this->active;
        }


        if (!$entityClass instanceof Story || in_array("createdAt", $include)) {
            $json["createdAt"] = $this->createdAt;
        }


        return $json;
    }
}