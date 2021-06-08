<?php

namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use UserBundle\Entity\AccountVerification;
use JsonSerializable;

/**
 * User
 *
 * @ORM\Table(name="`user`")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 */
class User extends BaseUser implements JsonSerializable
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
     * @ORM\Column(name="fullname", type="string", length=255, unique=false, nullable=false)
     */
    private $fullname;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, unique=false, nullable=false)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, unique=false, nullable=false)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, unique=false, nullable=false)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, unique=false, nullable=false)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="zipCode", type="string", length=255, unique=false, nullable=false)
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="phoneNumber", type="string", length=255, unique=false, nullable=false)
     */
    private $phoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="jobTitle", type="string", length=255, unique=false, nullable=false)
     */
    private $jobTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="jobDescription", type="text", length=255, unique=false, nullable=false)
     */
    private $jobDescription;


    public function __construct()
    {
        parent::__construct();

        $this->accountVerification = new ArrayCollection();
    }


    public function isGranted($role)
    {
        return in_array($role, $this->getRoles());
    }

    function generateCode($length = 20) {
        $chars = 'abcdef#$%ghijklmno@#pqrstuvwxyzAB%CDEFG$%^KLMN22OPQRSTU()*#$VWXYZ0123456789!*_';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }


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
     * Set fullname.
     *
     * @param string $fullname
     *
     * @return User
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }


    /**
     * Get fullname.
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set nom.
     *
     * @param string $country
     *
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set city.
     *
     * @param string $city
     *
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set image.
     *
     * @param string $image
     *
     * @return User
     */
    public function setImage($mage)
    {
        $this->image = $mage;

        return $this;
    }

    /**
     * Get image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set address.
     *
     * @param string $address
     *
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set zipCode.
     *
     * @param string $zipCode
     *
     * @return User
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode.
     *
     * @return string
     */
    public function getzipCode()
    {
        return $this->zipCode;

    }

    /**
     * Set zipCode.
     *
     * @param string $phoneNumber
     *
     * @return User
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber.
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;

    }

    /**
    * Get jobTitle.
    *
    * @return string
    */
    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    /**
     * Set jobTitle.
     *
     * @param string $jobTitle
     *
     * @return User
     */
    public function setJobTitle($jobTitle)
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    /**
     * Set jobDescription.
     *
     * @param string $jobDescription
     *
     * @return User
     */
    public function setJobDescription($jobDescription)
    {
        $this->jobDescription = $jobDescription;

        return $this;
    }

    /**
     * Get jobDescription.
     *
     * @return string
     */
    public function getJobDescription()
    {
        return $this->jobDescription;
    }


    public function setEmail($email)
    {
        $email = is_null($email) ? '' : $email;
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
    }


    public function jsonSerialize($entityClass = null,$include = []){

        $json = [
            "id" => $this->id,
        ];

        if(!$entityClass instanceof User || in_array("image",$include)){
            $json["image"] = $this->image;
        }

        if(!$entityClass instanceof User || in_array("country",$include)){
            $json["country"] = $this->country;
        }

        if(!$entityClass instanceof User || in_array("city",$include)){
            $json["city"] = $this->city;
        }

        if(!$entityClass instanceof User || in_array("address",$include)){
            $json["address"] = $this->address;
        }

        if(!$entityClass instanceof User || in_array("zipCode",$include)){
            $json["zipCode"] = $this->zipCode;
        }

        if(!$entityClass instanceof User || in_array("jobTitle",$include)){
            $json["jobTitle"] = $this->jobTitle;
        }

        if(!$entityClass instanceof User || in_array("jobDescription",$include)){
            $json["jobDescription"] = $this->jobDescription;
        }

        if(!$entityClass instanceof User || in_array("phoneNumber",$include)){
            $json["phoneNumber"] = $this->phoneNumber;
        }

        return $json;
    }

}