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
 * @ORM\Table(name="`financial_status`")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\FinancialStatusRepository")
 */

class FinancialStatus implements JsonSerializable
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


    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
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

        if(!$entityClass instanceof FinancialStatus || in_array("title",$include)){
            $json["title"] = $this->title;
        }

        return $json;
    }
}