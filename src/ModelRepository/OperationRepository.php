<?php

namespace App\ModelRepository;

use App\Manager\ORM\WyndpayObjectManager;
use App\Model\Operation;

class OperationRepository extends ModelRepository
{
    public function __construct(WyndpayObjectManager $objectManager)
    {
        parent::__construct($objectManager, Operation::class);
    }
}
