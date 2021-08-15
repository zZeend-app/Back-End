<?php

namespace UserBundle\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\Collection;
use UserBundle\Entity\AccountVerification;
use JsonSerializable;
use ApiBundle\Entity\Service;

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

    /**
     * @ORM\OneToMany(targetEntity="ApiBundle\Entity\Service", mappedBy="user")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $services;

    /**
     * @var string
     *
     * @ORM\Column(name="zZeend_score", type="string", length=255, unique=false, nullable=false)
     */
    private $zZeendScore;

    /**
     * @var array
     *
     * @ORM\Column(name="spoken_languages", type="array", length=255, unique=false, nullable=false)
     */
    private $spokenLanguages;

    /**
     * @var string
     *
     * @ORM\Column(name="sub_locality", type="string", length=255, unique=false, nullable=false)
     */
    private $subLocality;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="string", length=255, unique=false, nullable=false)
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="string", length=255, unique=false, nullable=false)
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="sub_administrative_area", type="string", length=255, unique=false, nullable=false)
     */
    private $subAdministrativeArea;

    /**
     * @var string
     *
     * @ORM\Column(name="administrative_area", type="string", length=255, unique=false, nullable=false)
     */
    private $administrativeArea;

    /**
     * @var string
     *
     * @ORM\Column(name="country_code", type="string", length=255, unique=false, nullable=false)
     */
    private $countryCode;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visibility", type="boolean", length=255, unique=false, nullable=false)
     */
    private $visibility;

    /**
     * @var boolean
     *
     * @ORM\Column(name="main_visibility", type="boolean", length=255, unique=false, nullable=false)
     */
    private $mainVisibility;

    /**
     * @ORM\OneToMany(targetEntity="ApiBundle\Entity\Device", mappedBy="user", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $devices;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        parent::__construct();
//        $this->services = new Service();
    }

    /**
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }



    public function isGranted($role)
    {
        return in_array($role, $this->getRoles());
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
     * Set spokenLanguages.
     *
     * @param array $spokenLanguages
     *
     * @return User
     */
    public function setSpokenLanguages($spokenLanguages)
    {
        $this->spokenLanguages = $spokenLanguages;

        return $this;
    }

    /**
     * Get spokenLanguages.
     *
     * @return array
     */
    public function getSpokenLanguages()
    {
        return $this->spokenLanguages;
    }

    /**
     * Set subLocality.
     *
     * @param string $subLocality
     *
     * @return User
     */
    public function setSubLocality($subLocality)
    {
        $this->subLocality = $subLocality;

        return $this;
    }

    /**
     * Get subLocality.
     *
     * @return string
     */
    public function getSubLocality()
    {
        return $this->subLocality;
    }

    /**
     * Set latitude.
     *
     * @param string $latitude
     *
     * @return User
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude.
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude.
     *
     * @param string $longitude
     *
     * @return User
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude.
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set subAdministrativeArea.
     *
     * @param string $subAdministrativeArea
     *
     * @return User
     */
    public function setSubAdministrativeArea($subAdministrativeArea)
    {
        $this->subAdministrativeArea = $subAdministrativeArea;

        return $this;
    }

    /**
     * Get subAdministrativeArea.
     *
     * @return string
     */
    public function getSubAdministrativeArea()
    {
        return $this->subAdministrativeArea;
    }

    /**
     * Set administrativeArea.
     *
     * @param string $administrativeArea
     *
     * @return User
     */
    public function setAdministrativeArea($administrativeArea)
    {
        $this->administrativeArea = $administrativeArea;

        return $this;
    }

    /**
     * Get administrativeArea.
     *
     * @return string
     */
    public function getAdministrativeArea()
    {
        return $this->administrativeArea;
    }

    /**
     * Set countryCode.
     *
     * @param string $countryCode
     *
     * @return User
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get countryCode.
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
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
     * Set address.
     *
     * @param string $zZeendScore
     *
     * @return string
     */
    public function setZzeendScore($zZeendScore)
    {
        $this->zZeendScore = $zZeendScore;

    }

    /**
     * Get zZeendScore.
     *
     * @return string
     */
    public function getZzeendScore()
    {
        return $this->zZeendScore;
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

    /**
     * Set visibility.
     *
     * @param string $visibilityFlag
     *
     */
    public function setVisibility($visibilityFlag)
    {
        $this->visibility = $visibilityFlag;
    }

    /**
     * Get visibility.
     *
     * @return boolean
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set mainVisibility.
     *
     * @param string $visibilityFlag
     *
     */
    public function setMainVisibility($visibilityFlag)
    {
        $this->mainVisibility = $visibilityFlag;
    }

    /**
     * Get mainVisibility.
     *
     * @return boolean
     */
    public function getMainVisibility()
    {
        return $this->mainVisibility;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $timestamp): self
    {
        $this->createdAt = $timestamp;
        return $this;
    }


    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $timestamp): self
    {
        $this->updatedAt = $timestamp;
        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtAutomatically()
    {
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTime());
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAtAutomatically()
    {
        $this->setUpdatedAt(new DateTime());
    }


    /**
     * Add device.
     *
     * @param \ApiBundle\Entity\Device $device
     *
     * @return User
     */
    public function addDevice(\ApiBundle\Entity\Device $device)
    {
        $this->devices[] = $device;

        return $this;
    }

    /**
     * Remove device.
     *
     * @param \ApiBundle\Entity\Device $device
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeDevice(\ApiBundle\Entity\Device $device)
    {
        return $this->devices->removeElement($device);
    }

    /**
     * Get devices.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDevices()
    {
        return $this->devices;
    }



    public function jsonSerialize($entityClass = null,$include = []){

        $json = [
            "id" => $this->id,
        ];

        if(!$entityClass instanceof User || in_array("fullname",$include)){
            $json["fullname"] = $this->fullname;
        }

        if(!$entityClass instanceof User || in_array("email",$include)){
            $json["email"] = $this->email;
        }

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

        if(!$entityClass instanceof User || in_array("roles",$include)){
            $json["roles"] = $this->roles;
        }

        if(!$entityClass instanceof User || in_array("enabled",$include)){
            $json["enabled"] = $this->enabled;
        }

        if(!$entityClass instanceof User || in_array("zZeendScore",$include)){
            $json["zZeendScore"] = $this->zZeendScore;
        }

        if(!$entityClass instanceof User || in_array("spokenLanguages",$include)){
            $json["spokenLanguages"] = $this->spokenLanguages;
        }

        if(!$entityClass instanceof User || in_array("subLocality",$include)){
            $json["subLocality"] = $this->subLocality;
        }

        if(!$entityClass instanceof User || in_array("latitude",$include)){
            $json["latitude"] = $this->latitude;
        }

        if(!$entityClass instanceof User || in_array("longitude",$include)){
            $json["longitude"] = $this->longitude;
        }

        if(!$entityClass instanceof User || in_array("subAdministrativeArea",$include)){
            $json["subAdministrativeArea"] = $this->subAdministrativeArea;
        }

        if(!$entityClass instanceof User || in_array("administrativeArea",$include)){
            $json["administrativeArea"] = $this->administrativeArea;
        }

        if(!$entityClass instanceof User || in_array("countryCode",$include)){
            $json["countryCode"] = $this->countryCode;
        }


        if(!$entityClass instanceof User || in_array("createdAt",$include)){
            $json["createdAt"] = $this->createdAt;
        }

        if(!$entityClass instanceof User || in_array("updatedAt",$include)){
            $json["updatedAt"] = $this->updatedAt;
        }

        return $json;
    }

}