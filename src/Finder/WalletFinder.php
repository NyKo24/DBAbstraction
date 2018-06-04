<?php

namespace App\Finder;

use App\Mapper\WalletMapper;
use App\Model\Wallet;
use App\Repository\AccountRepository;

class WalletFinder extends AbstractFinder
{
    public function __construct(AccountRepository $accountRepository, WalletMapper $walletMapper)
    {
        parent::__construct($accountRepository, $walletMapper);
    }

    public function support(): string
    {
        return Wallet::class;
    }

    public function findOneByCode(string $code)
    {
        if ($account = $this->entityRepository->findOneByCode($code)) {
            return $this->mapper->reverse($account);
        }

        return null;
    }
}
