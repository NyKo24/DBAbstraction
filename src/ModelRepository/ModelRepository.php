<?php

namespace App\ModelRepository;

use App\Manager\ORM\WyndpayObjectManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\Common\Persistence\ObjectRepository;

abstract class ModelRepository implements ObjectRepository, ServiceEntityRepositoryInterface
{
    private $objectManager;
    /**
     * @var string
     */
    private $objectName;

    public function __construct(WyndpayObjectManager $objectManager, string $objectName)
    {
        $this->objectManager = $objectManager;
        $this->objectName = $objectName;
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    public function findAll()
    {
        // TODO: Implement findAll() method.
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        // TODO: Implement findBy() method.
    }

    public function findOneBy(array $criteria)
    {
        // TODO: Implement findOneBy() method.
    }

    public function getClassName()
    {
        return $this->objectName;
    }
}
