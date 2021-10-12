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
 * Feedback
 * @ORM\Entity
 * @ORM\Table(name="`feedback`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\FeedbackRepository")
 */

class Feedback implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="feedbacks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255, unique=false, nullable=false)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255, unique=false, nullable=false)
     */
    private $message;

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
     * Set subject.
     *
     * @param string $subject
     *
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;

    }


    /**
     * Get subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set message.
     *
     * @param string $message
     *
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

    }


    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
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
     * Set user.
     *
     * @return User
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

        if(!$entityClass instanceof Feedback || in_array("subject",$include)){
            $json["subject"] = $this->subject;
        }

        if(!$entityClass instanceof Feedback || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof Feedback || in_array("message",$include)){
            $json["message"] = $this->message;
        }

        if(!$entityClass instanceof Feedback || in_array("subject",$include)){
            $json["subject"] = $this->subject;
        }

        return $json;
    }
}