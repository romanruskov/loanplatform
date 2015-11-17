<?php

namespace PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

use FOS\ElasticaBundle\Annotation\Search;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Table(name="loan")
 * @ORM\Entity(repositoryClass="PlatformBundle\Entity\Repository\InvestorRepository")
 * @ORM\HasLifecycleCallbacks
 * @Search(repositoryClass="PlatformBundle\Entity\SearchRepository\LoanRepository")
 * @ExclusionPolicy("all")
 */
class Loan
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type = "float",
     *     message = "loan.amount.type"
     * )
     * @Assert\Range(
     *      min = 0.01,
     *      max = 99999999.99,
     *      minMessage = "loan.amount.min_range",
     *      maxMessage = "loan.amount.max_range"
     * )
     * @Expose
     */
    private $amount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type = "float",
     *     message = "loan.available_for_investments.type"
     * )
     * @Assert\Range(
     *      min = 0,
     *      max = 99999999.99,
     *      minMessage = "loan.available_for_investments.min_range",
     *      maxMessage = "loan.available_for_investments.max_range"
     * )
     * @Expose
     */
    private $availableForInvestments;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Expose
     */
    protected $description;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="Investment", mappedBy="loan")
     */
    private $investments;

    public function __construct()
    {
        $this->investments = new ArrayCollection();
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
     * Set amount
     *
     * @param string $amount
     *
     * @return Loan
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set availableForInvestments
     *
     * @param string $availableForInvestments
     *
     * @return Loan
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
     * Set description
     *
     * @param string $description
     *
     * @return Loan
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Loan
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Add investment
     *
     * @param Investment $investment
     *
     * @return Loan
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
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
    }
}
