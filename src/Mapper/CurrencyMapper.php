<?php

namespace App\Mapper;

use App\Entity\Currency as CurrencyEntity;
use App\Manager\ORM\Proxy\ProxyFactory;
use App\Model\Currency;

class CurrencyMapper extends AbstractMapper implements MapperInterface
{
    public function __construct(ProxyFactory $proxyFactory)
    {
        parent::__construct($this->support(), $proxyFactory);
    }

    /**
     * @param Currency $currency
     */
    public function create($currency)
    {
        $currencyEntity = new CurrencyEntity();
        $currencyEntity->setCode($currency->getCode());
        $currencyEntity->setNom($currency->getCode());
        $currencyEntity->setNumeriqueIso($currency->getIso());
        $currencyEntity->setSymbole($currency->getIso());

        return $currencyEntity;
    }

    /**
     * @param Currency       $currency
     * @param CurrencyEntity $currencyEntity
     *
     * @return CurrencyEntity
     */
    public function update($currency, $currencyEntity)
    {
        $currencyEntity->setCode($currency->getCode());
        $currencyEntity->setNom($currency->getCode());
        $currencyEntity->setNumeriqueIso($currency->getIso());
        $currencyEntity->setSymbole($currency->getIso());

        return $currencyEntity;
    }

    /**
     * @param \App\Entity\Currency $entity
     *
     * @return Currency
     */
    public function reverse($entity)
    {
        $currency = $this->getProxy();

        $currency->setId($entity->getId());
        $currency->setIso($entity->getNumeriqueIso());
        $currency->setCode($entity->getCode());

        $this->initProxy($currency);

        return $currency;
    }

    public function support(): string
    {
        return Currency::class;
    }
}
