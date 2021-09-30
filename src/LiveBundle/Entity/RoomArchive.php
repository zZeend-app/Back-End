<?php


namespace LiveBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use UserBundle\Entity\AccountVerification;
use JsonSerializable;
use UserBundle\Entity\User;

/**
 * RoomArchive
 * @ORM\Entity
 * @ORM\Table(name="`room_archive`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="LiveBundle\Repository\RoomArchiveRepository")
 */

class RoomArchive implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="LiveBundle\Entity\Room", inversedBy="rooms")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     */
    private $room;

    /**
     * @var string
     *
     * @ORM\Column(name="archive_id", type="string", length=255, unique=false, nullable=false)
     */
    private $archiveId;


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

    /**
     * Set archiveId.
     *
     * @param string $archiveId
     */
    public function setArchiveId(string $archiveId)
    {
        $this->archiveId = $archiveId;

    }


    /**
     * Get archiveId.
     *
     * @return string
     */
    public function getArchiveId()
    {
        return $this->archiveId;
    }


    public function jsonSerialize($entityClass = null,$include = [])
    {
        $json = [
            "id" => $this->id,
        ];

        if(!$entityClass instanceof RoomArchive || in_array("room",$include)){
            $json["room"] = $this->room;
        }

        if(!$entityClass instanceof RoomArchive || in_array("archiveId",$include)){
            $json["archiveId"] = $this->archiveId;
        }

        return $json;
    }
}