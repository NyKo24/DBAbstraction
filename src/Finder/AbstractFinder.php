<?php

namespace App\Finder;

use App\Mapper\MapperInterface;
use App\ModelRepository\ModelRepository;
use Doctrine\Common\Persistence\ObjectRepository;

class AbstractFinder implements FinderInterface
{
    /**
     * @var ModelRepository
     */
    protected $entityRepository;

    /**
     * @var MapperInterface
     */
    protected $mapper;

    public function __construct(ObjectRepository $modelRepository, MapperInterface $mapper)
    {
        $this->entityRepository = $modelRepository;
        $this->mapper = $mapper;
    }

    public function support(): string
    {
        throw new \RuntimeException('You need to implement this method !');
    }

    public function find($id)
    {
        if (!$object = $this->entityRepository->find($id)) {
            return null;
        }

        return $this->mapper->reverse($object);
    }
}
