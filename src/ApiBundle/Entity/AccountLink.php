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
 * AccountLink
 * @ORM\Entity
 * @ORM\Table(name="`account_link`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\AccountLinkRepository")
 */

class AccountLink implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="account_links")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="refresh_token", type="string", length=255, unique=false, nullable=false)
     */
    private $refreshToken;

    /**
     * @var string
     *
     * @ORM\Column(name="return_token", type="string", length=255, unique=false, nullable=false)
     */
    private $returnToken;

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
     * Set refreshToken.
     *
     * @param string $refreshToken
     *
     */
    public function setRefreshToken(string $refreshToken)
    {
        $this->refreshToken = $refreshToken;

    }


    /**
     * Get refreshToken.
     *
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * Set returnToken.
     *
     * @param string $returnToken
     *
     */
    public function setReturnToken(string $returnToken)
    {
        $this->returnToken = $returnToken;

    }


    /**
     * Get returnToken.
     *
     * @return string
     */
    public function getReturnToken()
    {
        return $this->returnToken;
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
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

        if(!$entityClass instanceof AccountLink || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof AccountLink || in_array("refreshToken",$include)){
            $json["refreshToken"] = $this->refreshToken;
        }

        if(!$entityClass instanceof AccountLink || in_array("returnToken",$include)){
            $json["returnToken"] = $this->returnToken;
        }

        if(!$entityClass instanceof AccountLink || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        if(!$entityClass instanceof AccountLink || in_array("updatedAt",$include)){
            $json["updatedAt"] = $this->updatedAt;
        }

        return $json;
    }
}