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
use ApiBundle\Entity\Comment;

/**
 * CommentResponse
 * @ORM\Entity
 * @ORM\Table(name="`comment_response`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\CommentResponseRepository")
 */

class CommentResponse implements JsonSerializable
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
     * @ORM\Column(name="response", type="string", length=255, unique=false, nullable=false)
     */
    private $response;


    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="commentResponses")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\Comment", inversedBy="commentResponses", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $comment;

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
     * Set response.
     *
     * @param string $response
     *
     */
    public function setResponse(string $response)
    {
        $this->response = $response;
    }


    /**
     * Get response.
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
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
     * Set comment.
     *
     * @param Comment $comment
     *
     * @return void
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }


    /**
     * Get comment.
     *
     *
     * @return Comment
     */
    public function getComment($comment)
    {
        return $this->comment;
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



    public function jsonSerialize($entityClass = null, $include = [])
    {
        $json = [
            "id" => $this->id,
        ];

        if(!$entityClass instanceof CommentResponse || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof CommentResponse || in_array("response",$include)){
            $json["response"] = $this->response;
        }

        if(!$entityClass instanceof CommentResponse || in_array("comment",$include)){
            $json["comment"] = $this->comment;
        }


        if(!$entityClass instanceof CommentResponse || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        return $json;
    }
}