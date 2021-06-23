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
 * @ORM\Table(name="`like`")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\LikeRepository")
 */

class Like implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="likes")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="likes")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private $post;

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
     * Set post.
     *
     * @param Post $post
     *
     * @return void
     */
    public function setPost($post)
    {
        $this->post = $post;
    }

    /**
     * Get user.
     *
     * @return void
     */
    public function getPost()
    {
        return $this->post;
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

        if(!$entityClass instanceof Service || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof Service || in_array("post",$include)){
            $json["post"] = $this->post;
        }

        if(!$entityClass instanceof Service || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        return $json;
    }
}