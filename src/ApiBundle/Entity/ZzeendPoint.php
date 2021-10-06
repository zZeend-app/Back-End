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
 * ZzeendPoint
 * @ORM\Entity
 * @ORM\Table(name="`zzeend_point`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\ZzeendPointRepository")
 */

class ZzeendPoint implements JsonSerializable
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
     * @ORM\Column(name="zzeend_point", type="string", length=255, unique=false, nullable=false)
     */
    private $zZeendPoint;


    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="zZeend_points")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\Zzeend", inversedBy="zZeend_points")
     * @ORM\JoinColumn(name="zzeend_id", referencedColumnName="id")
     */
    private $zZeend;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

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
     * Set zZeendPoint.
     *
     * @param string $zZeendPoint
     *
     */
    public function setZzeendPoint($zZeendPoint)
    {
        $this->zZeendPoint = $zZeendPoint;
    }


    /**
     * Get zZeendPoint.
     *
     * @return string
     */
    public function getZzeendPoint()
    {
        return $this->zzeendPoint;
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
     * Get zZeend.
     *
     * @return string
     */
    public function getZzeend()
    {
        return $this->zZeend;
    }

    /**
     * Set zZeend.
     *
     * @param Zzeend $zZeend
     *
     * @return void
     */
    public function setZzeend($zZeend)
    {
        $this->zZeend = $zZeend;
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


    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $timestamp): self
    {
        $this->updatedAt = $timestamp;
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
     * @ORM\PreUpdate
     */

    public function setUpdatedAtAutomatically()
    {
        $this->setUpdatedAt(new DateTime());
    }


    public function jsonSerialize($entityClass = null,$include = [])
    {
        $json = [
            "id" => $this->id,
        ];

        if(!$entityClass instanceof ZzeendPoint || in_array("zZeendPoint",$include)){
            $json["zzeendPoint"] = $this->zzeendPoint;
        }

        if(!$entityClass instanceof ZzeendPoint || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof ZzeendPoint || in_array("zZeend",$include)){
            $json["zZeend"] = $this->zZeend;
        }

        if(!$entityClass instanceof ZzeendPoint || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        if(!$entityClass instanceof ZzeendPoint || in_array("updatedAt",$include)){
            $json["updatedAt"] = $this->updatedAt;
        }

        return $json;
    }
}