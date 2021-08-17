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
 * @ORM\Table(name="`share`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\ShareRepository")
 */

class Share implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="shares")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\ShareType", inversedBy="shares")
     * @ORM\JoinColumn(name="share_type_id", referencedColumnName="id")
     */
    private $shareType;


    /**
     * @var int
     *
     * @ORM\Column(name="related_id", type="integer", length=255, unique=false, nullable=false)
     */
    private $related_id;


    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set user.
     *
     * @param $user
     *
     * @return void
     */
    public function setUser($user)
    {
        $this->user = $user;
    }


    /**
     * @return ShareType
     */
    public function getShareType(): ShareType
    {
        return $this->shareType;
    }

    /**
     * Set shareType.
     *
     * @param $shareType
     *
     * @return void
     */
    public function setShareType($shareType)
    {
        $this->shareType = $shareType;
    }

    /**
     * Set service.
     *
     * @param integer $related_id
     *
     */
    public function setRelatedId($related_id)
    {
        $this->related_id = $related_id;

    }


    /**
     * Get related_id.
     *
     * @return string
     */
    public function getRelatedId()
    {
        return $this->related_id;
    }


    public function jsonSerialize($entityClass = null,$include = [])
    {
        $json = [
            "id" => $this->id,
        ];

        if(!$entityClass instanceof Share || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof Share || in_array("related_id",$include)){
            $json["shareType"] = $this->shareType;
        }

        if(!$entityClass instanceof Share || in_array("user",$include)){
            $json["related_id"] = $this->related_id;
        }

        return $json;
    }
}