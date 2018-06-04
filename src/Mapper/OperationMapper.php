<?php

namespace App\Mapper;

use App\Entity\AccountOperation;
use App\Manager\ORM\Proxy\ProxyFactory;
use App\Model\Operation;

class OperationMapper extends AbstractMapper implements MapperInterface
{
    public function __construct(ProxyFactory $proxyFactory)
    {
        parent::__construct($this->support(), $proxyFactory);
    }

    /**
     * @param $operation
     *
     * @return AccountOperation
     */
    public function create($operation)
    {
        $accountOperation = new AccountOperation();

        $accountOperation->setAmount($operation->getAmount());
        $accountOperation->setLabel($operation->getLabel());
        $accountOperation->setCode(uniqid());
        $accountOperation->setCreatedAt(new \DateTime());
        $accountOperation->setSupp(false);

        return $accountOperation;
    }

    /**
     * @param Operation        $operation
     * @param AccountOperation $accountOperation
     *
     * @return AccountOperation
     */
    public function update($operation, $accountOperation)
    {
        $accountOperation->setAmount($operation->getAmount());
        $accountOperation->setLabel($operation->getLabel());
        //$accountOperation->setCode(uniqid());
        if (!$accountOperation->getCreatedAt()) {
            $accountOperation->setCreatedAt(new \DateTime());
        }

        if ($operation->getUpdatedAt() && $operation->getUpdatedAt() !== $accountOperation->getUpdatedAt()) {
            $accountOperation->setUpdatedAt($operation->getUpdatedAt());
        }

        return $accountOperation;
    }

    /**
     * @param AccountOperation $accountOperation
     * @param Operation        $operation
     *
     * @return Operation
     */
    public function reverse($accountOperation)
    {
        $operation = $this->getProxy();
        $operation->setId($accountOperation->getId());
        $operation->setAmount($accountOperation->getAmount());
        $operation->setLabel($accountOperation->getLabel());
        $operation->setCreatedAt($accountOperation->getCreatedAt());
        $operation->setUpdatedAt($accountOperation->getUpdatedAt());

        $this->initProxy($operation);

        return $operation;
    }

    public function support(): string
    {
        return Operation::class;
    }
}
