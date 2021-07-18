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
 * @ORM\Table(name="`chat`")
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
     * @var string
     *
     * @ORM\Column(name="file_path", type="string", length=255, unique=false, nullable=true)
     */
    private $filePath;

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
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

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
     * @return Share
     */
    public function getShare()
    {
        return $this->share;
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
     * Set filePath.
     *
     * @param string $filePath
     *
     * @return Chat
     */
    public function setFilePath(?string $filePath)
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

        if (!$entityClass instanceof Chat || in_array("filePath", $include)) {
            $json["filePath"] = $this->filePath;
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

        if (!$entityClass instanceof Chat || in_array("createdAt", $include)) {
            $json["createdAt"] = $this->createdAt;
        }


        return $json;

    }

}