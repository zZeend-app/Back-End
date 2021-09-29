<?php


namespace LiveBundle\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use UserBundle\Entity\AccountVerification;
use JsonSerializable;
use UserBundle\Entity\User;
use LiveBundle\Entity\RoomType;
use LiveBundle\Entity\RoomDestinationType;

/**
 * Room
 * @ORM\Entity
 * @ORM\Table(name="`room`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\RoomRepository")
 */

class Room implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="rooms")
     * @ORM\JoinColumn(name="moderator_id", referencedColumnName="id")
     */
    private $moderator;

    /**
     * @var string
     *
     * @ORM\Column(name="moderator_token", type="string", length=255, unique=false, nullable=false)
     */
    private $moderatorToken;

    /**
     * @var int
     *
     * @ORM\Column(name="sessionId", type="integer", length=255, unique=false, nullable=false)
     */
    private $sessionId;

    /**
     * @ORM\ManyToOne(targetEntity="LiveBundle\Entity\RoomType", inversedBy="rooms")
     * @ORM\JoinColumn(name="room_type_id", referencedColumnName="id")
     */
    private $roomType;

    /**
     * @ORM\ManyToOne(targetEntity="LiveBundle\Entity\RoomTargetType", inversedBy="rooms")
     * @ORM\JoinColumn(name="room_type_id", referencedColumnName="id")
     */
    private $roomTargetType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", length=255, unique=false, nullable=false)
     */
    private $active;

    /**
     * @var boolean
     *
     * @ORM\Column(name="archived", type="boolean", length=255, unique=false, nullable=false)
     */
    private $archived;

    /**
     * @var string
     *
     * @ORM\Column(name="extra_data", type="string", length=255, unique=false, nullable=false)
     */
    private $extraData;

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
     * Set moderator.
     *
     * @param User $moderator
     *
     */
    public function setModerator(string $moderator)
    {
        $this->moderator = $moderator;

    }

     /**
     * Get moderator.
     *
     * @return User
     */
    public function getModerator()
    {
        return $this->moderator;
    }

    /**
     * Set sessionId.
     *
     * @param integer $sessionId
     *
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

    }

     /**
     * Get sessionId.
     *
     * @return integer
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set moderatorToken.
     *
     * @param string $moderatorToken
     *
     */
    public function setModeratorToken(string $moderatorToken)
    {
        $this->moderatorToken = $moderatorToken;

    }

     /**
     * Get moderatorToken.
     *
     * @return string
     */
    public function getModeratorToken()
    {
        return $this->moderatorToken;
    }


    /**
     * Set roomType.
     *
     * @param string $roomType
     *
     */
    public function setRoomType(string $roomType)
    {
        $this->roomType = $roomType;

    }

     /**
     * Get roomType.
     *
     * @return RoomType
     */
    public function getRoomType()
    {
        return $this->roomType;
    }

    /**
     * Set RoomTargetType.
     *
     * @param $roomTargetType
     *
     */
    public function setRoomTargetType($roomTargetType)
    {
        $this->roomTargetType = $roomTargetType;

    }

     /**
     * Get roomDestinationType.
     *
     * @return RoomTargetType
     */
    public function getRoomTargetType()
    {
        return $this->roomTargetType;
    }

    /**
     * Set active.
     *
     * @param boolean $active
     *
     */
    public function setActive($active)
    {
        $this->active = $active;

    }

     /**
     * Get active.
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set archived.
     *
     * @param boolean $archived
     *
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;

    }

     /**
     * Get archived.
     *
     * @return boolean
     */
    public function getArchived()
    {
        return $this->archived;
    }

    /**
     * Set extraData.
     *
     * @param string $extraData
     *
     */
    public function setExtraData($extraData)
    {
        $this->extraData = $extraData;
    }

    /**
     * Get extraData.
     *
     * @return string
     *
     */
    public function GetExtraData()
    {
        return $this->extraData;
    }

    public function setCreatedAt(?DateTimeInterface $timestamp): self
    {
        $this->createdAt = $timestamp;
        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
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

        if(!$entityClass instanceof Room || in_array("moderator",$include)){
            $json["moderator"] = $this->moderator;
        }

        if(!$entityClass instanceof Room || in_array("moderatorToken",$include)){
            $json["moderatorToken"] = $this->moderatorToken;
        }

        if(!$entityClass instanceof Room || in_array("sessionId",$include)){
            $json["sessionId"] = $this->sessionId;
        }

        if(!$entityClass instanceof Room || in_array("roomType",$include)){
            $json["roomType"] = $this->roomType;
        }

        if(!$entityClass instanceof Room || in_array("roomTargetType",$include)){
            $json["roomTargetType"] = $this->roomTargetType;
        }

        if(!$entityClass instanceof Room || in_array("active",$include)){
            $json["active"] = $this->active;
        }

        if(!$entityClass instanceof Room || in_array("archived",$include)){
            $json["archived"] = $this->archived;
        }

        if(!$entityClass instanceof Room || in_array("extraData",$include)){
            $json["extraData"] = $this->extraData;
        }

        if(!$entityClass instanceof Room || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        return $json;
    }
}