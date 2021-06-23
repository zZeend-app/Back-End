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
 * Notification
 *
 * @ORM\Table(name="`notification`")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\NotificationRepository")
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
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\NotificationType", inversedBy="notification")
     * @ORM\JoinColumn(name="notification_type_id", referencedColumnName="id")
     */
    private $notificationType;

    /**
     * @ORM\Column(name="related_id", type="boolean", unique=false, nullable=true)
     */
    private $related_id;

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
     * Set related_id.
     *
     *
     * @return void
     */
    public function setRelatedId($related_id)
    {
        $this->related_id = $related_id;
    }

    /**
     * Get related_id.
     *
     * @return integer
     */
    public function getRelatedId()
    {
        return $this->related_id;
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

        if(!$entityClass instanceof Notification || in_array("notificationType",$include)){
            $json["notificationType"] = $this->notificationType;
        }

        if(!$entityClass instanceof Notification || in_array("related_id",$include)){
            $json["related_id"] = $this->related_id;
        }

        if(!$entityClass instanceof Notification || in_array("viewed",$include)){
            $json["viewed"] = $this->viewed;
        }

        if(!$entityClass instanceof Notification || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        return $json;
    }
}