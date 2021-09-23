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
 * SearchKeyword
 * @ORM\Entity
 * @ORM\Table(name="`search_keyword`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\SearchKeywordRepository")
 */

class SearchKeyword implements JsonSerializable
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
     * @ORM\Column(name="keyword", type="string", length=255, unique=false, nullable=false)
     */
    private $keyword;


    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="keywords")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
     * Set keyword.
     *
     * @param string $keyword
     *
     * @return SearchKeyword
     */
    public function setKeyword(string $keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }


    /**
     * Get keyword.
     *
     * @return string
     */
    public function getKeyword()
    {
        return $this->keyword;
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

        if(!$entityClass instanceof SearchKeyword || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof SearchKeyword || in_array("keyword",$include)){
            $json["keyword"] = $this->keyword;
        }

        if(!$entityClass instanceof SearchKeyword || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }
        

        return $json;
    }
}