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
 *
 * @ORM\Table(name="`payment_type`")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\PaymentTypeRepository")
 */

class PaymentType implements JsonSerializable
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
     * @ORM\Column(name="title", type="string", length=255, unique=false, nullable=false)
     */
    private $title;


    /**
     * Set title.
     *
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

    }


    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


    public function jsonSerialize($entityClass = null,$include = [])
    {
        $json = [
            "id" => $this->id,
        ];

        if(!$entityClass instanceof PaymentType || in_array("title",$include)){
            $json["title"] = $this->title;
        }

        return $json;
    }
}