<?php


namespace ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use UserBundle\Entity\AccountVerification;
use JsonSerializable;
use UserBundle\Entity\User;

/**
 * ZzeendService
 * @ORM\Entity
 * @ORM\Table(name="`zzeend_service`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\ZzeendServiceRepository")
 */
class ZzeendService implements JsonSerializable
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
     * @ORM\Column(name="start_fees", type="float", length=255, unique=false, nullable=false)
     */
    private $startFees;

    /**
     * @var float
     *
     * @ORM\Column(name="end_fees", type="float", length=255, unique=false, nullable=false)
     */
    private $endFees;

    /**
     * @var float
     *
     * @ORM\Column(name="application_fees", type="float", length=255, unique=false, nullable=false)
     */
    private $applicationFees;

    /**
     * @var float
     *
     * @ORM\Column(name="zZeendPoint", type="float", length=255, unique=false, nullable=false)
     */
    private $zZeendPoint;

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
     * Get startFees.
     *
     * @return double
     */
    public function getStartFees()
    {
        return $this->startFees;
    }

    /**
     * Set startFees.
     */
    public function setStartFees($startFees)
    {
        $this->startFees = $startFees;
    }

    /**
     * Get endFees.
     *
     * @return double
     */
    public function getEndFees()
    {
        return $this->endFees;
    }

    /**
     * Set endFees.
     */
    public function setEndFees($endFees)
    {
        $this->endFees = $endFees;
    }

    /**
     * Get applicationFees.
     *
     * @return double
     */
    public function getApplicationFees()
    {
        return $this->applicationFees;
    }

    /**
     * Set applicationFees.
     */
    public function setApplicationFees($applicationFees)
    {
        $this->applicationFees = $applicationFees;
    }


    /**
     * Get zZeendPoint.
     *
     * @return integer
     */
    public function getZzeendPoint()
    {
        return $this->zZeendPoint;
    }

    /**
     * Set zZeendPoint.
     */
    public function setZzeendPoint($zZeendPoint)
    {
        $this->zZeendPoint = $zZeendPoint;
    }


    public function jsonSerialize($entityClass = null, $include = [])
    {
        $json = [
            "id" => $this->id,
        ];

        if (!$entityClass instanceof ZzeendService || in_array("startFees", $include)) {
            $json["startFees"] = number_format($this->startFees, 2);
        }

        if (!$entityClass instanceof ZzeendService || in_array("endFees", $include)) {
            $json["endFees"] = number_format($this->endFees, 2);
        }

        if (!$entityClass instanceof ZzeendService || in_array("applicationFees", $include)) {
            $json["applicationFees"] = number_format($this->applicationFees, 2);
        }

        if (!$entityClass instanceof ZzeendService || in_array("zZeendPoint", $include)) {
            $json["zZeendPoint"] = $this->zZeendPoint;
        }

        return $json;
    }
}