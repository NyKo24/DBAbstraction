<?php

namespace App\ModelRepository;

use App\Finder\CurrencyFinder;
use App\Manager\ORM\WyndpayObjectManager;
use App\Model\Currency;

class CurrencyRepository extends ModelRepository
{
    /**
     * @var CurrencyFinder
     */
    private $currenciesFinder;

    public function __construct(WyndpayObjectManager $objectManager, CurrencyFinder $currenciesFinder)
    {
        parent::__construct($objectManager, Currency::class);
        $this->currenciesFinder = $currenciesFinder;
    }

    public function findOneById(int $id)
    {
        return $this->currenciesFinder->findOneById($id);
    }

    public function findOneByCode(string $code)
    {
        return $this->currenciesFinder->findOneByCode($code);
    }
}
