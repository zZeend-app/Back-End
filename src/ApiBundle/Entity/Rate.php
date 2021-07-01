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
 * @ORM\Table(name="`rate`")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\RateRepository")
 */

class Rate implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="rates")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="rates")
     * @ORM\JoinColumn(name="rated_user_id", referencedColumnName="id")
     */
    private $ratedUser;

    /**
     * @var int
     *
     * @ORM\Column(name="stars", type="integer", length=255, unique=false, nullable=false)
     */
    private $stars;

    /**
     * @var string
     *
     * @ORM\Column(name="point_of_view", type="string", length=255, unique=false, nullable=false)
     */
    private $pointOfView;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

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
     * @return void
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set ratedUser.
     *
     * @param User $ratedUser
     *
     * @return void
     */
    public function setRatedUser($ratedUser)
    {
        $this->ratedUser = $ratedUser;
    }

    /**
     * Get ratedUser.
     *
     * @return void
     */
    public function getRatedUser()
    {
        return $this->ratedUser;
    }

    /**
     * Set stars.
     *
     * @param integer $stars
     *
     * @return void
     */
    public function setStars($stars)
    {
        $this->stars = $stars;
    }

    /**
     * Get stars.
     *
     * @return integer
     */
    public function getStars()
    {
        return $this->stars;
    }

    /**
     * Set pointOfView.
     *
     * @param string $pointOfView
     *
     * @return void
     */
    public function setPointOfView($pointOfView)
    {
        $this->pointOfView = $pointOfView;
    }

    /**
     * Get pointOfView.
     *
     * @return string
     */
    public function getPointOfView()
    {
        return $this->pointOfView;
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

        if(!$entityClass instanceof Rate || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof Rate || in_array("ratedUser",$include)){
            $json["ratedUser"] = $this->ratedUser;
        }

        if(!$entityClass instanceof Rate || in_array("stars",$include)){
            $json["stars"] = $this->stars;
        }

        if(!$entityClass instanceof Rate || in_array("pointOfView",$include)){
            $json["pointOfView"] = $this->pointOfView;
        }

        if(!$entityClass instanceof Rate || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        return $json;
    }
}