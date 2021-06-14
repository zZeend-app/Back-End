<?php


namespace ZzeendBundle\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use UserBundle\Entity\AccountVerification;
use JsonSerializable;
use UserBundle\Entity\User;

/**
 * User
 *
 * @ORM\Table(name="`request`")
 * @ORM\Entity(repositoryClass="ZzeendBundle\Repository\RequestRepository")
 */


class Request implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="request")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="request")
     * @ORM\JoinColumn(name="receiver_id", referencedColumnName="id")
     */
    private $receiver;

    /**
     * @var boolean
     *
     * @ORM\Column(name="accepted", type="boolean", unique=false, nullable=true)
     */
    private $accepted;

    /**
     * @var boolean
     *
     * @ORM\Column(name="rejected", type="boolean", unique=false, nullable=true)
     */
    private $rejected;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * Set users.
     *
     * @param User $user_sender
     * @param User $user_receiver
     *
     * @return void
     */
    public function setUsers($sender, $receiver)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
    }

    /**
     * Get users.
     *
     * @return array
     */
    public function getUsers()
    {
        return array('sender' => $this->sender, 'receiver' => $this->receiver);
    }

    /**
     * Set accepted.
     *
     *
     * @return Request
     */
    public function setAccepted($flag)
    {
        $this->accepted = $flag;

        return $this;
    }

    /**
     * Get accepted.
     *
     * @return boolean
     */
    public function getAccepted()
    {
        return $this->accepted;
    }

    /**
     * Set rejected.
     *
     *
     * @return Request
     */
    public function setRejected($flag)
    {
        $this->rejected = $flag;

        return $this;
    }

    /**
     * Get rejected.
     *
     * @return boolean
     */
    public function getRejected()
    {
        return $this->rejected;
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

        if(!$entityClass instanceof Service || in_array("sender",$include)){
            $json["sender"] = $this->sender;
        }

        if(!$entityClass instanceof Service || in_array("receiver",$include)){
            $json["receiver"] = $this->receiver;
        }

        if(!$entityClass instanceof Service || in_array("accepted",$include)){
            $json["accepted"] = $this->accepted;
        }

        if(!$entityClass instanceof Service || in_array("rejected",$include)){
            $json["rejected"] = $this->rejected;
        }

        if(!$entityClass instanceof Service || in_array("createdAt",$include)){
            $json["createdAt"] = $this->rejected;
        }

        return $json;
    }





}