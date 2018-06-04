<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * AccountOperation.
 *
 * @ORM\Table(name="comptes_ope", indexes={@ORM\Index(name="id_compte", columns={"id_compte"}), @ORM\Index(name="code", columns={"code"})})
 * @ORM\Entity
 */
class AccountOperation
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=30)
     */
    private $code;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Account", inversedBy="operations")
     * @ORM\JoinColumn(name="id_compte", referencedColumnName="id")
     */
    private $account;

    /**
     * @var int
     *
     * @ORM\Column(name="id_dmd_vir", type="integer", nullable=true, options={"unsigned"=true,"comment"="	id d'une demande de virement Wyndpay"})
     */
    private $idDmdVir;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=200)
     */
    private $label;

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
     * @var float
     *
     * @ORM\Column(name="montant", type="float", precision=10, scale=0)
     */
    private $amount;

    /**
     * @var bool
     *
     * @ORM\Column(name="supp", type="boolean", nullable=true, options={"comment"="1: opération supprimée"})
     */
    private $supp;

    /**
     * @var array
     *
     * @ORM\Column(name="data_plus", type="json", nullable=true)
     */
    private $dataPlus;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @param Account $account
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;
    }

    /**
     * @return int
     */
    public function getIdDmdVir(): int
    {
        return $this->idDmdVir;
    }

    /**
     * @param int $idDmdVir
     */
    public function setIdDmdVir(int $idDmdVir)
    {
        $this->idDmdVir = $idDmdVir;
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
     * @return \DateTime|null
     */
    public function getCreatedAt()
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
    public function getUpdatedAt(): \DateTime
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
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return bool
     */
    public function isSupp(): bool
    {
        return $this->supp;
    }

    /**
     * @param bool $supp
     */
    public function setSupp(bool $supp)
    {
        $this->supp = $supp;
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
}
