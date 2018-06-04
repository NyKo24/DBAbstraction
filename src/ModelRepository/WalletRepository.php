<?php

namespace App\ModelRepository;

use App\Finder\WalletFinder;
use App\Manager\ORM\WyndpayObjectManager;
use App\Model\Wallet;

class WalletRepository extends ModelRepository
{
    /**
     * @var WalletFinder
     */
    private $walletFinder;

    public function __construct(WyndpayObjectManager $objectManager, WalletFinder $walletFinder)
    {
        parent::__construct($objectManager, Wallet::class);
        $this->walletFinder = $walletFinder;
    }

    public function findOneById(int $id)
    {
        return $this->walletFinder->findOneById($id);
    }

    public function findOneByUuid(string $uuid)
    {
        return $this->walletFinder->findOneByCode($uuid);
    }
}
