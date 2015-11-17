<?php

namespace PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Table(name="investor")
 * @ORM\Entity(repositoryClass="PlatformBundle\Entity\Repository\InvestorRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields="email", message="investor.email.taken")
 * @UniqueEntity(fields="username", message="investor.username.taken")
 * @ExclusionPolicy("all")
 */
class Investor implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min=3,
     *      max=50,
     *      minMessage="investor.username.min_len",
     *      maxMessage="investor.username.max_len"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-z0-9]+$/i",
     *     htmlPattern="^[a-z0-9]+$",
     *     message="investor.username.regex"
     * )
     * @Expose
     */
    private $username;

    /**
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Length(max=4096, groups={"registration"})
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\Length(max=4096)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(
     *      min=5,
     *      max=50,
     *      minMessage="investor.email.min_length",
     *      maxMessage="investor.email.max_length"
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="float",
     *     message="investor.available_for_investments.type"
     * )
     * )
     * @Assert\Range(
     *      min=0,
     *      max=99999999.99,
     *      minMessage="investor.available_for_investments.min_value",
     *      maxMessage="investor.available_for_investments.max_value"
     * )
     */
    private $availableForInvestments;

    /**
     * @ORM\OneToOne(targetEntity="Avatar", mappedBy="investor")
     * @ORM\JoinColumn(name="avatar_id", referencedColumnName="id")
     * @Expose
     */
    private $avatar;

    /**
     * @Assert\Image(
     *     maxSize="2048k",
     *     mimeTypesMessage="avatar.file.invalid"
     * )
     */
    private $avatarFile;

    /**
     * @ORM\OneToMany(targetEntity="Investment", mappedBy="investor")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $investments;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = array();

    public function __construct()
    {
        $this->isActive = true;
        // $this->salt = md5(uniqid(null, true));
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Investor
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set plain password
     *
     * @param string $password
     *
     * @return Investor
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * Get plain password
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set encoded password
     *
     * @param string $password
     *
     * @return Investor
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get encoded password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Investor
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set availableForInvestments
     *
     * @param float $availableForInvestments
     *
     * @return Investor
     */
    public function setAvailableForInvestments($availableForInvestments)
    {
        $this->availableForInvestments = $availableForInvestments;

        return $this;
    }

    /**
     * Get availableForInvestments
     *
     * @return float
     */
    public function getAvailableForInvestments()
    {
        return $this->availableForInvestments;
    }

    /**
     * Set avatar
     *
     * @param Avatar $avatar
     *
     * @return Investor
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return Avatar
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set avatar file
     *
     * @param UploadedFile $avatarFile
     *
     * @return Investor
     */
    public function setAvatarFile(UploadedFile $avatarFile)
    {
        $this->avatarFile = $avatarFile;
        if ($avatarFile->getSize()) {
            $this->avatar->upload($avatarFile);
        }

        return $this;
    }

    /**
     * Get avatar file
     *
     * @return UploadedFile
     */
    public function getAvatarFile()
    {
        return $this->avatarFile;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_INVESTOR';

        return array_unique($roles);
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return Investor
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Add investment
     *
     * @param Investment $investment
     *
     * @return Investor
     */
    public function addInvestment(Investment $investment)
    {
        $this->investments[] = $investment;

        return $this;
    }

    /**
     * Remove investment
     *
     * @param Investment $investment
     */
    public function removeInvestment(Investment $investment)
    {
        $this->investments->removeElement($investment);
    }

    /**
     * Get investments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvestments()
    {
        return $this->investments;
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $this->avatar->remove();
    }

    public function eraseCredentials()
    {
        // implements UserInterface
    }

    public function getSalt()
    {
        // implements UserInterface
        // all passwords must be hashed with a salt, but bcrypt does this internally
        return null;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->email,
            $this->availableForInvestments,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->email,
            $this->availableForInvestments
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }
}
