<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Currency
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max="5")
     */
    private $iso;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max="5")
     */
    private $code;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getIso(): string
    {
        return $this->iso;
    }

    /**
     * @param string $iso
     */
    public function setIso(string $iso)
    {
        $this->iso = $iso;
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
}
