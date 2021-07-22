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
 * Service
 *
 * @ORM\Table(name="`event`")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\EventRepository")
 */

class Event implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\Zzeend", inversedBy="events")
     * @ORM\JoinColumn(name="zzeend_id", referencedColumnName="id")
     */
    private $zZeend;


    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="events")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, unique=false, nullable=false)
     */
    private $title;

    /**
     *
     * @ORM\Column(name="start_time", type="datetime", nullable=false)
     */
    private $startTime;

    /**
     *
     * @ORM\Column(name="end_time", type="datetime", nullable=false)
     */
    private $endTime;

    /**
     * @var boolean
     *
     * @ORM\Column(name="all_day", type="boolean", nullable=false)
     */
    private $allDay;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active;

    /**
     * @var integer
     *
     * @ORM\Column(name="deveice_event_id", type="integer", length=255, unique=false, nullable=false)
     */
    private $deviceEventId;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;


    /**
     * Set zZeend.
     * @param Zzeend $zZeend
     */
    public function setZzeend($zZeend)
    {
        $this->zZeend = $zZeend;

    }


    /**
     * Get zZeend.
     *
     * @return string
     */
    public function getZzeend()
    {
        return $this->zZeend;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

    }


    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set allDay.
     *
     * @param boolean $allDay
     *
     */
    public function setAllDay(bool $allDay)
    {
        $this->allDay = $allDay;

    }


    /**
     * Get allDay.
     *
     * @return boolean
     */
    public function getAllDay()
    {
        return $this->allDay;
    }

    /**
     * Set deviceEventId.
     *
     * @param integer $deviceEventId
     *
     */
    public function setDeviceEventIdy($deviceEventId)
    {
        $this->deviceEventId= $deviceEventId;

    }


    /**
     * Get deviceEventId.
     *
     * @return integer
     */
    public function getDeviceEventId()
    {
        return $this->deviceEventId;
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

    public function setStartTime(?DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function setEndTime(?DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set active.
     *
     * @param boolean $allDay
     *
     */
    public function setActive(bool $active)
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

        if(!$entityClass instanceof Event || in_array("zZeend",$include)){
            $json["zZeend"] = $this->zZeend;
        }

        if(!$entityClass instanceof Event || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof Event || in_array("title",$include)){
            $json["title"] = $this->title;
        }

        if(!$entityClass instanceof Event || in_array("startTime",$include)){
            $json["startTime"] = $this->startTime;
        }

        if(!$entityClass instanceof Event || in_array("endTime",$include)){
            $json["endTime"] = $this->endTime;
        }

        if(!$entityClass instanceof Event || in_array("allDay",$include)){
            $json["allDay"] = $this->allDay;
        }

        if(!$entityClass instanceof Event || in_array("active",$include)){
            $json["active"] = $this->active;
        }

        if(!$entityClass instanceof Event || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        return $json;
    }
}