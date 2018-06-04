<?php

namespace App\Mapper;

use App\Manager\ORM\Proxy\ProxyFactory;
use App\Entity\Account;
use App\Entity\Currency as CurrencyEntity;
use App\Model\Currency;
use App\Model\Wallet;
use Doctrine\Common\Collections\ArrayCollection;

class MapperFactory
{
    private $mappers;

    public function __construct()
    {
        $this->mappers = new ArrayCollection();
    }

    public function getMapperFor(string $className): MapperInterface
    {
        if (!$this->mappers->containsKey($className)) {
            throw new \RuntimeException(sprintf('No mapper found for class %s', $className));
        }

        return $this->mappers->get($className);
    }

    public function addMapper(MapperInterface $mapper)
    {
        if (!$this->mappers->containsKey($mapper->support())) {
            $this->mappers->set($mapper->support(), $mapper);
            $this->mappers->set(ProxyFactory::getProxyNameForClass($mapper->support()), $mapper);
        }
    }

    public function getLegacyEntityName(string $className)
    {
        if ($parent = get_parent_class($className)) {
            $className = $parent;
        }

        $mapping = [
            Currency::class => CurrencyEntity::class,
            Wallet::class => Account::class,
        ];

        return $mapping[$className] ?? null;
    }
}
