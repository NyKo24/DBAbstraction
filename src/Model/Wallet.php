<?php

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Wallet
{
    private $id;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     */
    private $updatedAt;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $status;

    /**
     * @var bool
     */
    private $overdraft;

    /**
     * @var float
     */
    private $balance;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var ArrayCollection|null;
     */
    private $operations;

    /**
     * @var Currency
     */
    private $currency;

    public function __construct()
    {
        $this->operations = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return nul|\DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param nul|\DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isOverdraft(): bool
    {
        return $this->overdraft;
    }

    /**
     * @param bool $overdraft
     */
    public function setOverdraft(bool $overdraft)
    {
        $this->overdraft = $overdraft;
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        return $this->balance ?? 0.00;
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
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
    }

    /**
     * @return Currency|null
     */
    public function getCurrency()
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
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function addOperation(Operation $operation)
    {
        if (!$this->operations->contains($operation)) {
            $this->operations->add($operation);
        }
    }

    public function setOperations($operations)
    {
        $this->operations->clear();

        foreach ($operations as $operation) {
            $this->addOperation($operation);
        }
    }

    public function getOperations(): Collection
    {
        return $this->operations;
    }

    ///**
    // * @ORM\ManyToOne(targetEntity="Entities", inversedBy="wallets")
    // * @ORM\JoinColumn(name="entity", referencedColumnName="id")
    // */
    //private $entity;
//
    ///**
    // * @ORM\ManyToOne(targetEntity="Customers", inversedBy="wallets")
    // * @ORM\JoinColumn(name="customer", referencedColumnName="id")
    // */
    //private $customer;
}
