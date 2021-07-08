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
 * @ORM\Table(name="`post`")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\PostRepository")
 */

class Post implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=255, unique=false, nullable=false)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="file_path", type="string", length=255, unique=false, nullable=true)
     */
    private $filePath;

    /**
     * @var string
     *
     * @ORM\Column(name="file_type", type="string", length=255, unique=false, nullable=true)
     */
    private $fileType;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255, unique=false, nullable=true)
     */
    private $link;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_profile_related", type="boolean", length=255, unique=false, nullable=false)
     */
    private $isProfileRelated;

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
     * Get user.
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
     * Set text.
     *
     * @param string $text
     *
     */
    public function setText(string $text)
    {
        $this->text = $text;
    }


    /**
     * Get text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set link.
     *
     * @param string $link
     *
     */
    public function setLink($link)
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
     * Set filePath.
     *
     * @param string $filePath
     *
     */
    public function setFilePath($filePath)
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
    public function setFileType($fileType)
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
     * Set isProfileRelated.
     *
     * @param boolean $isProfileRelatedFlag
     *
     */
    public function setIsProfileRelated($isProfileRelatedFlag)
    {
        $this->isProfileRelated = $isProfileRelatedFlag;
    }


    /**
     * Get isProfileRelated.
     *
     * @return boolean
     */
    public function getIsProfileRelated()
    {
        return $this->isProfileRelated;
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

        if(!$entityClass instanceof Service || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof Service || in_array("text",$include)){
            $json["text"] = $this->text;
        }

        if(!$entityClass instanceof Service || in_array("filePath",$include)){
            $json["filePath"] = $this->filePath;
        }

        if(!$entityClass instanceof Service || in_array("fileType",$include)){
            $json["fileType"] = $this->fileType;
        }

        if(!$entityClass instanceof Service || in_array("link",$include)){
            $json["link"] = $this->link;
        }

        if(!$entityClass instanceof Service || in_array("isProfileRelated",$include)){
            $json["isProfileRelated"] = $this->isProfileRelated;
        }

        if(!$entityClass instanceof Service || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        return $json;
    }
}