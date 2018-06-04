<?php

namespace App\Manager\ORM;

use App\Finder\FinderFactory;
use App\Manager\ORM\Proxy\ProxyInterface;
use App\Mapper\MapperFactory;
use App\ModelRepository\ModelRepositoryFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMInvalidArgumentException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WyndpayObjectManager implements ObjectManager
{
    /**
     * @var ArrayCollection
     */
    private $objectToPersist;
    /**
     * @var ArrayCollection
     */
    private $objectToRemove;
    /**
     * @var MapperFactory
     */
    private $mapperFactory;
    /**
     * @var EntityManager
     */
    private $doctrineEntityManager;
    /**
     * @var ModelRepositoryFactory
     */
    private $repositoryFactory;
    /**
     * @var FinderFactory
     */
    private $finderFactory;
    /**
     * @var ArrayCollection
     */
    private $mappingObjectEntity;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        MapperFactory $mapperFactory,
        EntityManagerInterface $doctrineEntityManager,
        ModelRepositoryFactory $repositoryFactory,
        FinderFactory $finderFactory,
        ValidatorInterface $validator
    ) {
        $this->objectToPersist = new ArrayCollection();
        $this->objectToRemove = new ArrayCollection();
        $this->mapperFactory = $mapperFactory;
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->repositoryFactory = $repositoryFactory;
        $this->finderFactory = $finderFactory;
        $this->mappingObjectEntity = new ArrayCollection();
        $this->validator = $validator;
    }

    public function find($className, $id)
    {
        // TODO: Implement find() method.
    }

    public function persist($object)
    {
        if (!is_object($object)) {
            throw ORMInvalidArgumentException::invalidObject('EntityManager#persist()', $object);
        }

        $oid = spl_object_hash($object);

        if ($this->objectToPersist->containsKey($oid)) {
            return;
        }

        $this->objectToPersist->set($oid, $object);
    }

    public function remove($object)
    {
        if (!is_object($object)) {
            throw ORMInvalidArgumentException::invalidObject('EntityManager#persist()', $object);
        }

        $oid = spl_object_hash($object);

        if ($this->objectToRemove->containsKey($oid)) {
            return;
        }

        $this->objectToRemove->set($oid, $object);
    }

    public function merge($object)
    {
        // TODO: Implement merge() method.
    }

    public function clear($objectName = null)
    {
        // TODO: Implement clear() method.
    }

    public function detach($object)
    {
        // TODO: Implement detach() method.
    }

    public function refresh($object)
    {
        // TODO: Implement refresh() method.
    }

    public function flush()
    {
        $this->doPersist();
        $this->doRemove();

        $this->doctrineEntityManager->flush();

        $this->setIdOnObjectPersisted();

        $this->objectToPersist->clear();
        $this->objectToRemove->clear();
    }

    private function doPersist()
    {
        foreach ($this->objectToPersist as $item) {
            $errors = $this->validator->validate($item);

            if ($errors->count() && $error = $errors->get(0)) {
                throw new \RuntimeException(sprintf('Validation error for object %s : Property %s : %s', get_class($item), $error->getPropertyPath(), $error->getMessage()));
            }

            $mapper = $this->mapperFactory->getMapperFor(get_class($item));
            if (!$item->getId()) {
                $entity = $mapper->create($item);
            } elseif ($item instanceof ProxyInterface && !$item->__isChanged__() && !$item->__hasCollection__()) {
                continue;
            } else {
                if (!$legacyEntityName = $this->mapperFactory->getLegacyEntityName(get_class($item))) {
                    throw new \RuntimeException(sprintf('Legacy entity not found for model %s', get_class($item)));
                }
                $legacyEntityRepository = $this->doctrineEntityManager->getRepository($legacyEntityName);
                $entity = $legacyEntityRepository->find($item->getId());

                $mapper->update($item, $entity);
            }

            $this->mappingObjectEntity->set(get_class($item), $entity);

            $this->doctrineEntityManager->persist($entity);
        }
    }

    private function doRemove()
    {
        foreach ($this->objectToRemove as $item) {
            if (!$entity = $this->mappingObjectEntity->get(get_class($item))) {
                if (!$entity = $this->doctrineEntityManager->getRepository($this->mapperFactory->getLegacyEntityName(get_class($item)))->find($item->getId())) {
                    throw new \RuntimeException(sprintf('Entity not found for object %s', get_class($item)));
                }
            }

            $this->doctrineEntityManager->remove($entity);
        }
    }

    private function setIdOnObjectPersisted()
    {
        foreach ($this->objectToPersist as &$item) {
            if (!$entity = $this->mappingObjectEntity->get(get_class($item))) {
                continue;
            }

            $item->setId($entity->getId());
        }
    }

    public function getRepository($className)
    {
        return $this->repositoryFactory->getRepository($className);
    }

    public function getClassMetadata($className)
    {
        // TODO: Implement getClassMetadata() method.
    }

    public function getMetadataFactory()
    {
        // TODO: Implement getMetadataFactory() method.
    }

    public function initializeObject($obj)
    {
        // TODO: Implement initializeObject() method.
    }

    public function contains($object)
    {
        // TODO: Implement contains() method.
    }
}
