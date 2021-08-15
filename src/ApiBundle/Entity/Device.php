<?php


namespace ApiBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Device
 *
 * @ORM\Table(name="device")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\DeviceRepository")
 */


class Device
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


}