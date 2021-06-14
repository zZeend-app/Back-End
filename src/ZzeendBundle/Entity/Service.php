<?php


namespace ZzeendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use UserBundle\Entity\AccountVerification;
use JsonSerializable;
use UserBundle\Entity\User;

/**
 * User
 *
 * @ORM\Table(name="`service`")
 * @ORM\Entity(repositoryClass="ZzeendBundle\Repository\ServiceRepository")
 */

class Service implements JsonSerializable
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
     * @ORM\Column(name="service", type="string", length=255, unique=false, nullable=false)
     */
    private $service;


    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="services")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * Set service.
     *
     * @param string $serviceName
     *
     * @return Service
     */
    public function setService(string $serviceName)
    {
        $this->service = $serviceName;

        return $this;
    }


    /**
     * Get service.
     *
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set user.
     *
     * @param User $user_param
     *
     * @return void
     */
    public function setUser($user)
    {
        $this->user = $user;
    }


    public function jsonSerialize($entityClass = null,$include = [])
    {
        $json = [
            "id" => $this->id,
        ];

        if(!$entityClass instanceof Service || in_array("service",$include)){
            $json["service"] = $this->service;
        }

        return $json;
    }
}