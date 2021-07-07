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
 * @ORM\Table(name="`view`")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\ViewRepository")
 */

class View implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="views")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\ViewType", inversedBy="views")
     * @ORM\JoinColumn(name="view_type_id", referencedColumnName="id")
     */
    private $viewType;

    /**
     * @var string
     *
     * @ORM\Column(name="related_id", type="string", length=255, unique=false, nullable=false)
     */
    private $relatedId;

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
     * Set viewType.
     *
     * @param ViewType $viewType
     *
     * @return void
     */
    public function setViewType($viewType)
    {
        $this->viewType = $viewType;
    }

    /**
     * Get viewType.
     *
     * @return void
     */
    public function getViewType()
    {
        return $this->viewType;
    }


    /**
     * Set relatedId.
     *
     * @param $relatedId
     *
     * @return void
     */
    public function setRelatedId($relatedId)
    {
        $this->relatedId = $relatedId;
    }

    /**
     * Get relatedId.
     *
     */
    public function geRelatedId()
    {
        return $this->relatedId;
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

        if(!$entityClass instanceof View || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof View || in_array("viewType",$include)){
            $json["viewType"] = $this->viewType;
        }

        if(!$entityClass instanceof View || in_array("relatedId",$include)){
            $json["relatedId"] = $this->relatedId;
        }

        if(!$entityClass instanceof View || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        return $json;
    }
}