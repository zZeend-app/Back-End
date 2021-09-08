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
 * Request
 *
 * @ORM\Entity
 * @ORM\Table(name="`chat`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\ChatRepository")
 */
class Chat implements JsonSerializable
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
     * @ORM\Column(name="discussion", type="string", length=255, unique=false, nullable=true)
     */
    private $discussion;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\File", inversedBy="files")
     * @ORM\JoinColumn(name="file", referencedColumnName="id")
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="chats")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\Contact", inversedBy="chats", cascade={"persist"})
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id", nullable=true)
     */
    private $contact;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\Share", inversedBy="posts")
     * @ORM\JoinColumn(name="share_id", referencedColumnName="id")
     */
    private $share;

    /**
     * @var boolean
     *
     * @ORM\Column(name="viewed", type="boolean", length=255, unique=false, nullable=true)
     */
    private $viewed;


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
     * Set users.
     *
     * @param User $sender
     *
     * @return void
     */
    public function setUser($sender)
    {
        $this->sender = $sender;
    }

    /**
     * Get users.
     *
     * @return User
     */
    public function getUsers()
    {
        return $this->sender;
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
     * @return Chat
     */
    public function getShare()
    {
        return $this->share;
    }

    /**
     * Set viewed.
     *
     * @param $viewed
     *
     */
    public function setViewed($viewed)
    {
        $this->viewed = $viewed;
    }

    /**
     * Get viewed.
     *
     * @return boolean
     */
    public function getViewed()
    {
        return $this->viewed;
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

    /**
     * Set chat.
     *
     * @param string $discussion
     *
     * @return Chat
     */
    public function setDiscussion(string $discussion)
    {
        $this->discussion = $discussion;
    }


    /**
     * Get discussion.
     *
     * @return string
     */
    public function getDiscussion()
    {
        return $this->discussion;
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
     * Set users.
     *
     * @param Contact $contact
     *
     * @return void
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    public function jsonSerialize($entityClass = null, $include = [])
    {
        $json = [
            "id" => $this->id,
        ];

        if (!$entityClass instanceof Chat || in_array("discussion", $include)) {
            $json["discussion"] = $this->discussion;
        }

        if (!$entityClass instanceof Chat || in_array("file", $include)) {
            $json["file"] = $this->file;
        }


        if (!$entityClass instanceof Chat || in_array("mainUse", $include)) {
            $json["sender"] = $this->sender;
        }

        if (!$entityClass instanceof Chat || in_array("contact", $include)) {
            $json["contact"] = $this->contact;
        }

        if (!$entityClass instanceof Chat || in_array("share", $include)) {
            $json["share"] = $this->share;
        }

        if (!$entityClass instanceof Chat || in_array("viewed", $include)) {
            $json["viewed"] = $this->viewed;
        }


        if (!$entityClass instanceof Chat || in_array("createdAt", $include)) {
            $json["createdAt"] = $this->createdAt;
        }


        return $json;

    }

}