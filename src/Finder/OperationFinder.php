<?php

namespace App\Finder;

use App\Mapper\OperationMapper;
use App\Model\Operation;
use App\Repository\AccountOperationRepository;

class OperationFinder extends AbstractFinder
{
    public function __construct(AccountOperationRepository $accountOperationRepository, OperationMapper $operationMapper)
    {
        parent::__construct($accountOperationRepository, $operationMapper);
    }

    public function support(): string
    {
        return Operation::class;
    }
}
