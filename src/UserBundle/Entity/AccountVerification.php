<?php


namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccountVerification
 * @ORM\Entity
 * @ORM\Table(name="accountVerification")
 *
 */
class AccountVerification
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
     * @var int
     *
     * @ORM\Column(name="userId", type="integer", length=255, unique=false, nullable=false)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="codeGen", type="string", length=255, unique=false, nullable=false)
     */
    private $codeGen;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getCodeGen(): string
    {
        return $this->codeGen;
    }

    /**
     * @param string $codeGen
     */
    public function setCodeGen(string $codeGen): void
    {
        $this->codeGen = $codeGen;
    }

}