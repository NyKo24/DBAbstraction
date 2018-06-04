<?php

namespace App\Finder;

use App\Mapper\CurrencyMapper;
use App\Model\Currency;
use App\Repository\CurrencyRepository;

class CurrencyFinder extends AbstractFinder
{
    public function __construct(CurrencyRepository $currencyRepository, CurrencyMapper $currenciesMapper)
    {
        parent::__construct($currencyRepository, $currenciesMapper);
    }

    public function support(): string
    {
        return Currency::class;
    }

    /**
     * @param string $code
     *
     * @return Currency|null
     */
    public function findOneByCode(string $code): ?Currency
    {
        if (!$object = $this->entityRepository->findOneByCode($code)) {
            return null;
        }

        return $this->mapper->reverse($object);
    }
}
