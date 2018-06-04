<?php

namespace App\ModelRepository;

use Doctrine\Common\Collections\ArrayCollection;

class ModelRepositoryFactory
{
    private $repositories;

    public function __construct()
    {
        $this->repositories = new ArrayCollection();
    }

    public function getRepository(string $classFQCN): ModelRepository
    {
        if (!$this->repositories->containsKey($classFQCN)) {
            throw new \RuntimeException(sprintf('Repository for model %s not found in container', $classFQCN));
        }

        return $this->repositories->get($classFQCN);
    }

    public function addRepository(ModelRepository $repository)
    {
        if (!$this->repositories->containsKey($repository->getClassName())) {
            $this->repositories->set($repository->getClassName(), $repository);
        }
    }

    public static function getRepositoryClassNameForClass(string $classFQCN): string
    {
        $objectName = substr($classFQCN, strrpos($classFQCN, '\\') + 1);

        return 'App\\ModelRepository\\'.$objectName.'Repository';
    }
}
