<?php


namespace ApiBundle\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use UserBundle\Entity\AccountVerification;
use JsonSerializable;
use UserBundle\Entity\User;
use ApiBundle\Entity\Post;

/**
 * Comment
 * @ORM\Entity
 * @ORM\Table(name="`comment`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\CommentRepository")
 */

class Comment implements JsonSerializable
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
     * @ORM\Column(name="comment", type="string", length=255, unique=false, nullable=false)
     */
    private $comment;


    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private $post;

        /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="ApiBundle\Entity\CommentResponse", mappedBy="comment", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $commentResponses;

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
     * Set comment.
     *
     * @param string $comment
     *
     */
    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }


    /**
     * Get comment.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
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
     *
     * @return User
     */
    public function getUser($user)
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
     * Get post.
     *
     *
     * @return Post
     */
    public function getPost($post)
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
    

    /**
     * Add commentResponse.
     *
     * @param \ApiBundle\Entity\CommentResponse $commentResponse
     *
     * @return Comment
     */
    public function addCommentResponse(\ApiBundle\Entity\CommentResponse $commentResponse)
    {
        $this->commentResponses[] = $commentResponse;

        return $this;
    }

    /**
     * Get commentResponse.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommentResponse()
    {
        return $this->commentResponses;
    }


    public function jsonSerialize($entityClass = null, $include = [])
    {
        $json = [
            "id" => $this->id,
        ];

        if(!$entityClass instanceof Comment || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof Comment || in_array("comment",$include)){
            $json["comment"] = $this->comment;
        }

        if(!$entityClass instanceof Comment || in_array("post",$include)){
            $json["post"] = $this->post;
        }

        if(!$entityClass instanceof Comment || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        return $json;
    }
}