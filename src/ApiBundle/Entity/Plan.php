<?php


namespace ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use UserBundle\Entity\AccountVerification;
use JsonSerializable;
use UserBundle\Entity\User;

/**
 * Service
 * @ORM\Entity
 * @ORM\Table(name="`plan`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\PlanRepository")
 */

class Plan implements JsonSerializable
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
     * @var float
     *
     * @ORM\Column(name="price", type="float", length=255, unique=false, nullable=false)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="duration", type="string", length=255, unique=false, nullable=false)
     */
    private $duration;

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
     * Set price.
     *
     * @param float $price
     *
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * Get price.
     *
     * @return double
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set duration.
     *
     * @param string $duration
     *
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * Get duration.
     *
     * @return string
     */
    public function getDuration()
    {
        return $this->duration;
    }


    public function jsonSerialize($entityClass = null,$include = [])
    {
        $json = [
            "id" => $this->id,
        ];

        if(!$entityClass instanceof Plan || in_array("price",$include)){
            $json["price"] = number_format($this->price, 2);
        }

        if(!$entityClass instanceof Plan || in_array("duration",$include)){
            $json["duration"] = $this->duration;
        }

        return $json;
    }
}