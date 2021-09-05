<?php


namespace ApiBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Device
 * @ORM\Entity
 * @ORM\Table(name="device", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\DeviceRepository")
 */


class Device implements JsonSerializable
{


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="text", nullable=false)
     */
    private $token;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="devices", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set token.
     *
     * @param string $token
     *
     * @return Device
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set user.
     *
     * @param \UserBundle\Entity\User|null $user
     *
     * @return Device
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \UserBundle\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    public function jsonSerialize($entityClass = null,$include = [])
    {
        $json = [
            "id" => $this->id,
        ];

        if(!$entityClass instanceof Device || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof Device || in_array("token",$include)){
            $json["token"] = $this->token;
        }

        return $json;

    }


}