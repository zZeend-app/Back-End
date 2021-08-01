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
     * @ORM\Column(name="text", type="string", length=255, unique=false, nullable=true)
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
     * @ORM\Column(name="is_profile_related", type="boolean", length=255, unique=false, nullable=true)
     */
    private $isProfileRelated;

    /**
     * @var array
     *
     * @ORM\Column(name="tags", type="array", length=255, unique=false, nullable=true)
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="ApiBundle\Entity\Like", mappedBy="post")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $likes;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\Share", inversedBy="posts")
     * @ORM\JoinColumn(name="share_id", referencedColumnName="id")
     */
    private $share;

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
     * @return User
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

    /**
     * Set share.
     *
     * @param $share
     *
     * @return void
     */
    public function setShare($share)
    {
        $this->share = $share;
    }

    /**
     * Get share.
     *
     * @return Share
     */
    public function getShare()
    {
        return $this->share;
    }

    /**
     * Set tags.
     *
     * @return void
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * Get tags.
     *
     */
    public function getTags()
    {
        return $this->tags;
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

        if(!$entityClass instanceof Post || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof Post || in_array("text",$include)){
            $json["text"] = $this->text;
        }

        if(!$entityClass instanceof Post || in_array("filePath",$include)){
            $json["filePath"] = $this->filePath;
        }

        if(!$entityClass instanceof Post || in_array("fileType",$include)){
            $json["fileType"] = $this->fileType;
        }

        if(!$entityClass instanceof Post || in_array("link",$include)){
            $json["link"] = $this->link;
        }

        if(!$entityClass instanceof Post || in_array("isProfileRelated",$include)){
            $json["isProfileRelated"] = $this->isProfileRelated;
        }

        if(!$entityClass instanceof Post || in_array("tags",$include)){
            $json["tags"] = $this->tags;
        }


        if(!$entityClass instanceof Post || in_array("share",$include)){
            $json["share"] = $this->share;
        }


        if(!$entityClass instanceof Post || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        return $json;
    }
}