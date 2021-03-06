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

/**
 * RoomGuest
 * @ORM\Entity
 * @ORM\Table(name="`room_guest`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="LiveBundle\Repository\RoomGuestRepository")
 */

class RoomGuest implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="room_guests")
     * @ORM\JoinColumn(name="guest_id", referencedColumnName="id")
     */
    private $guest;

    /**
     * @var string
     *
     * @ORM\Column(name="guest_token", type="string", length=255, unique=false, nullable=true)
     */
    private $guestToken;

    /**
     * @var string
     *
     * @ORM\Column(name="connection_id", type="string", length=255, unique=false, nullable=true)
     */
    private $connectionId;

    /**
     * @ORM\ManyToOne(targetEntity="LiveBundle\Entity\Room", inversedBy="room_guests")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     */
    private $room;


    /**
     * @ORM\Column(name="invited", type="datetime", nullable=true)
     */
    private $invited;

    /**
     * @ORM\Column(name="joined", type="datetime", nullable=true)
    */
    private $joined;

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
     * Set guest.
     *
     * @param User $guest
     *
     * @return void
     */
    public function setGuest($guest)
    {
        $this->guest = $guest;
    }

    /**
     * Get guest.
     *
     * @return User
     */
    public function getGuest()
    {
        return $this->guest;
    }

    /**
     * Set guestToken.
     *
     * @param string $guestToken
     *
     */
    public function setGuestToken(string $guestToken)
    {
        $this->guestToken = $guestToken;

    }

     /**
     * Get guestToken.
     *
     * @return string
     */
    public function getGuestTokenToken()
    {
        return $this->guestToken;
    }


    /**
     * Set connectionId.
     *
     * @param string $connectionId
     *
     */
    public function setConnectionId(string $connectionId)
    {
        $this->connectionId = $connectionId;

    }

    /**
     * Get connectionId.
     *
     * @return string
     */
    public function getConnectionId()
    {
        return $this->connectionId;
    }

    /**
     * Set room.
     *
     * @param Room $room
     *
     * @return void
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * Get room.
     *
     * @return Room
     */
    public function getRoom()
    {
        return $this->room;
    }

    public function setInvited(?DateTimeInterface $timestamp): self
    {
        $this->invited = $timestamp;
        return $this;
    }

    public function getInvited(): ?DateTimeInterface
    {
        return $this->invited;
    }

    /**
     * @ORM\PrePersist
     */
    public function setInvitedAutomatically()
    {
        if ($this->getInvited() === null) {
            $this->setInvited(new \DateTime());
        }
    }


    public function setJoined(?DateTimeInterface $timestamp): self
    {
        $this->joined = $timestamp;
        return $this;
    }

    public function getJoined(): ?DateTimeInterface
    {
        return $this->joined;
    }

    /**
     * @ORM\PrePersist
     */
    public function setJoinedAutomatically()
    {
        if ($this->getJoined() === null) {
            $this->setJoined(new \DateTime());
        }
    }

    public function jsonSerialize($entityClass = null,$include = [])
    {
        $json = [
            "id" => $this->id,
        ];

        if(!$entityClass instanceof RoomGuest || in_array("room",$include)){
            $json["room"] = $this->room;
        }

        if(!$entityClass instanceof RoomGuest || in_array("guest",$include)){
            $json["guest"] = $this->guest;
        }

        if(!$entityClass instanceof RoomGuest || in_array("guestToken",$include)){
            $json["guestToken"] = $this->guestToken;
        }

        if(!$entityClass instanceof RoomGuest || in_array("connectionId",$include)){
            $json["connectionId"] = $this->connectionId;
        }

        if(!$entityClass instanceof RoomGuest || in_array("invited",$include)){
            $json["invited"] = $this->invited;
        }

        if(!$entityClass instanceof RoomGuest || in_array("joined",$include)){
            $json["joined"] = $this->joined;
        }

        return $json;
    }
}