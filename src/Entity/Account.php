<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Account.
 *
 * @ORM\Table(name="comptes", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})})
 * @ORM\Entity
 */
class Account
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=30, nullable=false)
     */
    private $code = '';

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="dt_creation", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="dt_modif", type="datetime")
     */
    private $updatedAt;

    /**
     * @var Currency
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency", cascade={"persist"})
     * @ORM\JoinColumn(name="id_devise", referencedColumnName="id")
     */
    private $currency;

    /**
     * @var float
     *
     * @ORM\Column(name="decouvert", type="float", precision=10, scale=0, nullable=false, options={"comment"="-1: découvert illimité, 0: découvert non autorisé, >0: montant du découvert"})
     */
    private $overdraft;

    /**
     * @var float
     *
     * @ORM\Column(name="solde", type="float", precision=10, scale=0, nullable=false)
     */
    private $balance = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="string", length=200, nullable=false)
     */
    private $comment = '';

    /**
     * @var array
     *
     * @ORM\Column(name="historique", type="json", nullable=true)
     */
    private $history;

    /**
     * @var array
     *
     * @ORM\Column(name="data_plus", type="json", nullable=true)
     */
    private $dataPlus;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AccountOperation", mappedBy="account", cascade={"all"}, orphanRemoval=true)
     */
    private $operations;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function __construct()
    {
        $this->operations = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code)
    {
        $this->code = $code;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return float
     */
    public function getOverdraft(): float
    {
        return $this->overdraft;
    }

    /**
     * @param float $overdraft
     */
    public function setOverdraft(float $overdraft)
    {
        $this->overdraft = $overdraft;
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     */
    public function setBalance(float $balance)
    {
        $this->balance = $balance;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return array
     */
    public function getHistory(): array
    {
        return $this->history;
    }

    /**
     * @param array $history
     */
    public function setHistory(array $history)
    {
        $this->history = $history;
    }

    /**
     * @return array
     */
    public function getDataPlus(): array
    {
        return $this->dataPlus;
    }

    /**
     * @param array $dataPlus
     */
    public function setDataPlus(array $dataPlus)
    {
        $this->dataPlus = $dataPlus;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function addOperation(AccountOperation $operation)
    {
        if (!$this->operations->contains($operation)) {
            $operation->setAccount($this);
            $this->operations->add($operation);
        }
    }

    public function getOperations(): Collection
    {
        return $this->operations;
    }

    public function removeOperation(AccountOperation $operation)
    {
        if ($this->operations->contains($operation)) {
            $this->operations->removeElement($operation);
        }
    }

    public function setOperations($operations)
    {
        $this->operations->clear();

        foreach ($operations as $operation) {
            $this->addOperation($operation);
        }
    }
}
