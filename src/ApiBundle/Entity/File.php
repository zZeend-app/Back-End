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
 * File
 * @ORM\Entity
 * @ORM\Table(name="`file`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\FileRepository")
 */

class File implements JsonSerializable
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
     * @ORM\Column(name="file_path", type="string", length=255, unique=false, nullable=false)
     */
    private $filePath;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=255, unique=false, nullable=false)
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(name="file_type", type="string", length=255, unique=false, nullable=false)
     */
    private $fileType;

    /**
     * @var int
     *
     * @ORM\Column(name="file_size", type="integer", length=255, unique=false, nullable=false)
     */
    private $fileSize;

    /**
     * @var string
     *
     * @ORM\Column(name="thumbnail", type="string", length=255, unique=false, nullable=false)
     */
    private $thumbnail;



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
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * Set fileName .
     *
     * @param string $fileName
     *
     */
    public function setFileName(string $fileName)
    {
        $this->fileName = $fileName;

    }

    /**
     * Get fileName.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set fileType .
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
     * Set fileSize .
     *
     * @param integer $fileSize
     *
     */
    public function setFileSize(int $fileSize)
    {
        $this->fileSize = $fileSize;

    }

    /**
     * Get fileSize.
     *
     * @return integer
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * Set thumbnail .
     *
     * @param string $thumbnail
     *
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

    }

    /**
     * Get thumbnail.
     *
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
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

        if(!$entityClass instanceof File || in_array("filePath",$include)){
            $json["filePath"] = $this->filePath;
        }

        if(!$entityClass instanceof File || in_array("fileName",$include)){
            $json["fileName"] = $this->fileName;
        }

        if(!$entityClass instanceof File || in_array("fileType",$include)){
            $json["fileType"] = $this->fileType;
        }

        if(!$entityClass instanceof File || in_array("fileSize",$include)){
            $json["fileSize"] = $this->fileSize;
        }

        if(!$entityClass instanceof File || in_array("thumbnail",$include)){
            $json["thumbnail"] = $this->thumbnail;
        }

        if(!$entityClass instanceof File || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        return $json;
    }
}