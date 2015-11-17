<?php

namespace PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="investment")
 * @ORM\Entity(repositoryClass="PlatformBundle\Entity\Repository\InvestmentRepository")
 */
class Investment
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type = "float",
     *     message = "investment.amount.type"
     * )
     * @Assert\Range(
     *      min = 0.01,
     *      max = 99999999.99,
     *      minMessage = "investment.amount.min_range",
     *      maxMessage = "investment.amount.max_range"
     * )
     */
    private $amount;

    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThan(
     *     value = 0
     * )
     */
    private $plainLoanId;

    /**
     * Loan search query
     */
    private $loanSearchQuery;

    /**
     * @ORM\ManyToOne(targetEntity="Loan", inversedBy="investments")
     * @ORM\JoinColumn(name="loan_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $loan;

    /**
     * @ORM\ManyToOne(targetEntity="Investor", inversedBy="investments")
     * @ORM\JoinColumn(name="investor_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $investor;

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
     * @return Investment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get plainLoanId
     *
     * @return int
     */
    public function getPlainLoanId()
    {
        return $this->plainLoanId;
    }

    /**
     * Set plainLoanId
     *
     * @param int $loanId
     *
     * @return Investment
     */
    public function setPlainLoanId($loanId)
    {
        $this->plainLoanId = $loanId;

        return $this;
    }

    /**
     * Get loanSearchQuery
     *
     * @return string
     */
    public function getLoanSearchQuery()
    {
        return $this->loanSearchQuery;
    }

    /**
     * Set loanSearchQuery
     *
     * @param string $loanSearchQuery
     *
     * @return Investment
     */
    public function setLoanSearchQuery($loanSearchQuery)
    {
        $this->loanSearchQuery = $loanSearchQuery;

        return $this;
    }

    /**
     * Set loan
     *
     * @param \PlatformBundle\Entity\Loan $loan
     *
     * @return Investment
     */
    public function setLoan(\PlatformBundle\Entity\Loan $loan = null)
    {
        $this->loan = $loan;

        return $this;
    }

    /**
     * Get loan
     *
     * @return \PlatformBundle\Entity\Loan
     */
    public function getLoan()
    {
        return $this->loan;
    }

    /**
     * Set investor
     *
     * @param \PlatformBundle\Entity\Investor $investor
     *
     * @return Investment
     */
    public function setInvestor(\PlatformBundle\Entity\Investor $investor = null)
    {
        $this->investor = $investor;

        return $this;
    }

    /**
     * Get investor
     *
     * @return \PlatformBundle\Entity\Investor
     */
    public function getInvestor()
    {
        return $this->investor;
    }
}
