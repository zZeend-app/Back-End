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
     * @ORM\JoinColumn(name="user_rated_id", referencedColumnName="id")
     */
    private $userRated;

    /**
     * @var int
     *
     * @ORM\Column(name="star", type="integer", length=255, unique=false, nullable=false)
     */
    private $star;

    /**
     * @var string
     *
     * @ORM\Column(name="point_of_view", type="string", length=255, unique=false, nullable=false)
     */
    private $pontOfView;

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
     * Set userRated.
     *
     * @param User $userRated
     *
     * @return void
     */
    public function setUserRated($userRated)
    {
        $this->userRated = $userRated;
    }

    /**
     * Get userRated.
     *
     * @return void
     */
    public function getUserRated()
    {
        return $this->userRated;
    }

    /**
     * Set star.
     *
     * @param Post $star
     *
     * @return void
     */
    public function setPost($star)
    {
        $this->star = $star;
    }

    /**
     * Get star.
     *
     * @return integer
     */
    public function getStar()
    {
        return $this->star;
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

        if(!$entityClass instanceof Rate || in_array("userRated",$include)){
            $json["userRated"] = $this->userRated;
        }

        if(!$entityClass instanceof Rate || in_array("star",$include)){
            $json["star"] = $this->star;
        }

        if(!$entityClass instanceof Rate || in_array("pontOfView",$include)){
            $json["pontOfView"] = $this->pontOfView;
        }

        if(!$entityClass instanceof Rate || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        return $json;
    }
}