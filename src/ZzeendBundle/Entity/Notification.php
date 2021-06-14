<?php


namespace ZzeendBundle\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use UserBundle\Entity\AccountVerification;
use JsonSerializable;
use UserBundle\Entity\User;

/**
 * User
 *
 * @ORM\Table(name="`notification`")
 * @ORM\Entity(repositoryClass="ZzeendBundle\Repository\NotificationRepository")
 */


class Notification implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="ZzeendBundle\Entity\NotificationType", inversedBy="notification")
     * @ORM\JoinColumn(name="notification_type_id", referencedColumnName="id")
     */
    private $notificationType;

    /**
     * @ORM\ManyToOne(targetEntity="ZzeendBundle\Entity\Request", inversedBy="notification")
     * @ORM\JoinColumn(name="related_id", referencedColumnName="id")
     */
    private $related;

    /**
     * @var boolean
     *
     * @ORM\Column(name="viewed", type="boolean", unique=false, nullable=true)
     */
    private $viewed;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * Set notificationType.
     *
     *
     * @return void
     */
    public function setNotificationType($notificationType)
    {
        $this->notificationType = $notificationType;
    }

    /**
     * Get notificationType.
     *
     * @return NotificationType
     */
    public function getNotificationType()
    {
        return $this->notificationType;
    }

    /**
     * Set related.
     *
     *
     * @return void
     */
    public function setRelated($related)
    {
        $this->related = $related;
    }

    /**
     * Get related.
     *
     * @return Request
     */
    public function getRelated(): Request
    {
        return $this->related;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set viewed.
     *
     *
     * @return void
     */
    public function setViewed($flag)
    {
        $this->viewed = $flag;
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
     * @inheritDoc
     */
    public function jsonSerialize($entityClass = null,$include = [])
    {
        $json = [
            "id" => $this->id,
        ];

        if(!$entityClass instanceof Service || in_array("notificationType",$include)){
            $json["notificationType"] = $this->notificationType;
        }

        if(!$entityClass instanceof Service || in_array("related",$include)){
            $json["related"] = $this->related;
        }

        if(!$entityClass instanceof Service || in_array("viewed",$include)){
            $json["viewed"] = $this->related;
        }

        if(!$entityClass instanceof Service || in_array("createdAt",$include)){
            $json["createdAt"] = $this->viewed;
        }

        return $json;
    }
}