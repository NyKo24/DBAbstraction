<?php

namespace App\Finder;

use Doctrine\Common\Collections\ArrayCollection;

class FinderFactory
{
    private $finders;

    public function __construct()
    {
        $this->finders = new ArrayCollection();
    }

    public function getFinderFor(string $className): FinderInterface
    {
        if (!$this->finders->containsKey($className)) {
            throw new \RuntimeException(sprintf('Finder %s for model %s not found in container', $finderClassName, $className));
        }

        return $this->finders->get($className);
    }

    public function addFinder(FinderInterface $finder)
    {
        if (!$this->finders->containsKey($finder->support())) {
            $this->finders->set($finder->support(), $finder);
        }
    }

    public function getFinderClassNameForClass(string $classFQCN): string
    {
        $objectName = substr($classFQCN, strrpos($classFQCN, '\\') + 1);

        return 'App\\Finder\\'.$objectName.'Finder';
    }
}
