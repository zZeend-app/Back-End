<?php


namespace ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use UserBundle\Entity\AccountVerification;
use JsonSerializable;
use UserBundle\Entity\User;

/**
 * StripeConnectedAccount
 * @ORM\Entity
 * @ORM\Table(name="`stripe_connected_account`", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\StripeConnectedAccountRepository")
 */

class StripeConnectedAccount implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="services")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="stripe_account_id", type="string", length=255, unique=false, nullable=false)
     */
    private $stripeAccountId;

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
     * Set stripeAccountId.
     *
     * @param string $stripeAccountId
     *
     */
    public function setStripeAccountId(string $stripeAccountId)
    {
        $this->stripeAccountId = $stripeAccountId;

    }


    /**
     * Get stripeAccountId.
     *
     * @return string
     */
    public function getStripeAccountId()
    {
        return $this->stripeAccountId;
    }

    /**
     * Set user.
     *
     * @param User $user
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

        if(!$entityClass instanceof StripeConnectedAccount || in_array("user",$include)){
            $json["user"] = $this->user;
        }

        if(!$entityClass instanceof StripeConnectedAccount || in_array("stripeAccountId",$include)){
            $json["stripeAccountId"] = $this->stripeAccountId;
        }

        return $json;
    }
}