<?php

namespace App\Mapper;

use App\Entity\Account;
use App\Entity\AccountOperation;
use App\Manager\ORM\Proxy\ProxyFactory;
use App\Manager\ORM\Proxy\ProxyInterface;
use App\Model\Operation;
use App\Model\Wallet;
use App\Repository\AccountOperationRepository;
use App\Repository\CurrencyRepository;

class WalletMapper extends AbstractMapper implements MapperInterface
{
    /**
     * @var CurrencyMapper
     */
    private $currencyMapper;
    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;
    /**
     * @var OperationMapper
     */
    private $operationMapper;
    /**
     * @var AccountOperationRepository
     */
    private $accountOperationRepository;

    public function __construct(
        CurrencyMapper $currencyMapper,
        CurrencyRepository $currencyRepository,
        OperationMapper $operationMapper,
        AccountOperationRepository $accountOperationRepository,
        ProxyFactory $proxyFactory
    ) {
        $this->currencyMapper = $currencyMapper;
        $this->currencyRepository = $currencyRepository;
        $this->operationMapper = $operationMapper;
        $this->accountOperationRepository = $accountOperationRepository;
        parent::__construct($this->support(), $proxyFactory);
    }

    /**
     * @param Wallet $wallet
     *
     * @return Account
     */
    public function create($wallet)
    {
        $accountEntity = new Account();

        if ($wallet->getCurrency() && $wallet->getCurrency()->getId()) {
            $accountEntity->setCurrency($this->currencyMapper->update($wallet->getCurrency(), $this->currencyRepository->find($wallet->getCurrency()->getId())));
        } elseif ($wallet->getCurrency()) {
            $accountEntity->setCurrency($this->currencyMapper->create($wallet->getCurrency()));
        }
        $accountEntity->setBalance($wallet->getBalance());
        $accountEntity->setOverdraft($wallet->isOverdraft());
        $accountEntity->setCode($wallet->getUuid());
        $accountEntity->setComment($wallet->getLabel());

        /** @var Operation $operation */
        foreach ($wallet->getOperations() as $operation) {
            if ($operation instanceof ProxyInterface && !$operation->__isChanged__()) {
                continue;
            }

            if (!$operation->getId()) {
                $accountOperation = $this->operationMapper->create($operation);
                $accountEntity->addOperation($accountOperation);

                continue;
            }

            /** @var AccountOperation $accountOperation */
            foreach ($accountEntity->getOperations() as $key => $accountOperation) {
                if ($accountOperation->getId() === $operation->getId()) {
                    $accountOperation = $this->operationMapper->update($operation, $accountOperation);
                    $accountEntity->getOperations()->set($key, $accountOperation);
                    break;
                }
            }
        }

        return $accountEntity;
    }

    /**
     * @param Wallet  $wallet
     * @param Account $accountEntity
     *
     * @return Account
     */
    public function update($wallet, $accountEntity)
    {
        if ($wallet->getCurrency()->getId()) {
            $accountEntity->setCurrency($this->currencyMapper->update($wallet->getCurrency(), $this->currencyRepository->find($wallet->getCurrency()->getId())));
        } else {
            $accountEntity->setCurrency($this->currencyMapper->create($wallet->getCurrency()));
        }
        $accountEntity->setBalance($wallet->getBalance());
        $accountEntity->setOverdraft($wallet->isOverdraft());
        $accountEntity->setCode($wallet->getUuid());
        $accountEntity->setComment($wallet->getLabel());

        // remove
        /** @var AccountOperation $accountOperation */
        foreach ($accountEntity->getOperations() as $key => $accountOperation) {
            $find = false;
            /** @var Operation $operation */
            foreach ($wallet->getOperations() as $operation) {
                if ($operation->getId() === $accountOperation->getId()) {
                    $find = true;

                    break;
                }
            }

            if (!$find) {
                $accountEntity->removeOperation($accountOperation);
            }
        }

        /** @var Operation $operation */
        foreach ($wallet->getOperations() as $operation) {
            if ($operation instanceof ProxyInterface && !$operation->__isChanged__()) {
                continue;
            }

            if (!$operation->getId()) {
                $accountOperation = $this->operationMapper->create($operation);
                $accountEntity->addOperation($accountOperation);

                continue;
            }

            /** @var AccountOperation $accountOperation */
            foreach ($accountEntity->getOperations() as $key => $accountOperation) {
                if ($accountOperation->getId() === $operation->getId()) {
                    $accountOperation = $this->operationMapper->update($operation, $accountOperation);
                    $accountEntity->getOperations()->set($key, $accountOperation);
                    break;
                }
            }
        }

        return $accountEntity;
    }

    /**
     * @param Account $account
     * @param Wallet  $wallet
     *
     * @return Wallet
     */
    public function reverse($account)
    {
        $wallet = $this->getProxy();
        $wallet->setUuid($account->getCode());
        if ($wallet->getCurrency()) {
            $wallet->setCurrency($this->currencyMapper->reverse($account->getCurrency()));
        }
        $wallet->setBalance($account->getBalance());
        $wallet->setOverdraft($account->getOverdraft());
        $wallet->setLabel($account->getComment());
        $wallet->setId($account->getId());
        if ($account->getUpdatedAt()) {
            $wallet->setUpdatedAt($account->getUpdatedAt());
        }
        $wallet->setCreatedAt($account->getCreatedAt());

        foreach ($account->getOperations() as $operation) {
            $wallet->addOperation($this->operationMapper->reverse($operation));
        }

        $this->initProxy($wallet);

        return $wallet;
    }

    public function support(): string
    {
        return Wallet::class;
    }
}
