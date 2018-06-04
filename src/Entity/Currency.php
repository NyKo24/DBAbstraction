<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Currency.
 *
 * @ORM\Table(name="devises", uniqueConstraints={@ORM\UniqueConstraint(name="U_Code", columns={"code"})})
 * @ORM\Entity(repositoryClass="App\Repository\CurrencyRepository")
 */
class Currency
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=5, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=25, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="symbole", type="string", length=3, nullable=false)
     */
    private $symbole;

    /**
     * @var string
     *
     * @ORM\Column(name="numerique_ISO", type="string", length=5, nullable=false)
     */
    private $numeriqueIso;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code)
    {
        $this->code = $code;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom)
    {
        $this->nom = $nom;
    }

    public function getSymbole(): string
    {
        return $this->symbole;
    }

    public function setSymbole(string $symbole)
    {
        $this->symbole = $symbole;
    }

    public function getNumeriqueIso(): string
    {
        return $this->numeriqueIso;
    }

    public function setNumeriqueIso(string $numeriqueIso)
    {
        $this->numeriqueIso = $numeriqueIso;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
