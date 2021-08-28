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
     * @var string
     *
     * @ORM\Column(name="file_path", type="string", length=255, unique=false, nullable=false)
     */
    private $filePath;

    /**
     * @var string
     *
     * @ORM\Column(name="file_type", type="string", length=255, unique=false, nullable=false)
     */
    private $fileType;

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
     * Set filePath.
     *
     * @param string $filePath
     *
     */
    public function setFilePath(string $filePath)
    {
        $this->filePath = $filePath;

    }

    /**
     * Get filePath.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Set fileType.
     *
     * @param string $fileType
     *
     */
    public function setFileType(string $fileType)
    {
        $this->fileType = $fileType;

    }

    /**
     * Get fileType.
     *
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
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

        if (!$entityClass instanceof Story || in_array("filePat", $include)) {
            $json["filePath"] = $this->filePath;
        }


        if (!$entityClass instanceof Story || in_array("fileType", $include)) {
            $json["fileType"] = $this->fileType;
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