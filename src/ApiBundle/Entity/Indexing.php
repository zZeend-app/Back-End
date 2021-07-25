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
 * @ORM\Table(name="`indexing`")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\IndexingRepository")
 */

class Indexing implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="indexings")
     * @ORM\JoinColumn(name="actioned_user_id", referencedColumnName="id")
     */
    private $actionedUser;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\IndexingType", inversedBy="indexings")
     * @ORM\JoinColumn(name="indexing_type_id", referencedColumnName="id")
     */
    private $indexingType;

    /**
     * @ORM\Column(name="related_id", type="integer", unique=false, nullable=true)
     */
    private $related_id;

    /**
     * @var string
     *
     * @ORM\Column(name="duration", type="string", length=255, unique=false, nullable=false)
     */
    private $duration;


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
     * Set duration.
     *
     * @param string $duration
     *
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

    }


    /**
     * Get duration.
     *
     * @return string
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set actionedUser.
     *
     * @param User $actionedUser
     *
     * @return void
     */
    public function getActionedUser()
    {
        return $this->actionedUser;
    }

    /**
     * Set actionedUser.
     *
     * @param User $actionedUser
     *
     * @return void
     */
    public function setActionedUser($actionedUser)
    {
        $this->actionedUser = $actionedUser;
    }

    /**
     * Set indexingType.
     *
     * @return void
     */
    public function getIndexingType()
    {
        return $this->indexingType;
    }

    /**
     * Set indexingType.
     *
     * @return void
     */
    public function setIndexingType($indexingType)
    {
        $this->indexingType = $indexingType;
    }

    /**
     * Set related_id.
     *
     * @return void
     */
    public function getRelatedId()
    {
        return $this->related_id;
    }

    /**
     * Set related_id.
     *
     * @return void
     */
    public function setRelatedId($related_id)
    {
        $this->related_id = $related_id;
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

        if(!$entityClass instanceof Indexing || in_array("actionedUser",$include)){
            $json["actionedUser"] = $this->actionedUser;
        }

        if(!$entityClass instanceof Indexing || in_array("related_id",$include)){
            $json["related_id"] = $this->related_id;
        }

        if(!$entityClass instanceof Indexing || in_array("indexingType",$include)){
            $json["indexingType"] = $this->indexingType;
        }

        if(!$entityClass instanceof Indexing || in_array("duration",$include)){
            $json["duration"] = $this->duration;
        }

        if(!$entityClass instanceof Indexing || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        return $json;
    }
}