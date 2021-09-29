<?php


namespace LiveBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use UserBundle\Entity\AccountVerification;
use JsonSerializable;
use UserBundle\Entity\User;

/**
 * RoomTargetType
 * @ORM\Entity
 * @ORM\Table(name="`Room_target_type`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="LiveBundle\Repository\RoomTargetTypeRepository")
 */

class RoomTargetType implements JsonSerializable
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

        if(!$entityClass instanceof RoomTargetType || in_array("title",$include)){
            $json["title"] = $this->title;
        }

        return $json;
    }
}