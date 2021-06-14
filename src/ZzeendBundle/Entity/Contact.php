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
 * Request
 *
 * @ORM\Table(name="`contact`")
 * @ORM\Entity(repositoryClass="ZzeendBundle\Repository\ContactRepository")
 */


class Contact implements JsonSerializable
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
     * @ORM\JoinColumn(name="main_user_id", referencedColumnName="id")
     */
    private $mainUser;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="request")
     * @ORM\JoinColumn(name="second_user_id", referencedColumnName="id")
     */
    private $secondUser;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * Set users.
     *
     * @param User $mainUser
     * @param User $secondUser
     *
     * @return void
     */
    public function setUsers($mainUser, $secondUser)
    {
        $this->mainUser = $mainUser;
        $this->secondUser = $secondUser;
    }

    /**
     * Get users.
     *
     * @return array
     */
    public function getUsers()
    {
        return array('main_user' => $this->mainUser, 'second_user' => $this->secondUser);
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

        if (!$entityClass instanceof Service || in_array("mainUser", $include)) {
            $json["mainUser"] = $this->mainUser;
        }

        if (!$entityClass instanceof Service || in_array("secondUser", $include)) {
            $json["secondUser"] = $this->mainUser;
        }

        if (!$entityClass instanceof Service || in_array("createdAt", $include)) {
            $json["createdAt"] = $this->mainUser;
        }


        return $json;

    }

}