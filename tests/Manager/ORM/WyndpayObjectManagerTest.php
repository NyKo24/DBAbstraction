<?php
namespace Tests\Manager\ORM\Proxy;

use App\Finder\FinderFactory;
use App\Manager\ORM\Proxy\ProxyInterface;
use App\Manager\ORM\WyndpayObjectManager;
use App\Mapper\MapperFactory;
use App\Mapper\MapperInterface;
use App\ModelRepository\ModelRepositoryFactory;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMInvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WyndpayObjectManagerTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $mapperFactoryMock;
    /**
     * @var MockObject
     */
    private $doctrineEntityManager;
    /**
     * @var MockObject
     */
    private $modelRepositoryFactory;
    /**
     * @var MockObject
     */
    private $finderFactoryMock;
    /**
     * @var MockObject
     */
    private $validatorMock;

    public function setUp()
    {
        $this->mapperFactoryMock = $this->createMock(MapperFactory::class);
        $this->doctrineEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->modelRepositoryFactory = $this->createMock(ModelRepositoryFactory::class);
        $this->finderFactoryMock = $this->createMock(FinderFactory::class);
        $this->validatorMock = $this->createMock(ValidatorInterface::class);
    }

    public function init()
    {
        return new WyndpayObjectManager(
            $this->mapperFactoryMock,
            $this->doctrineEntityManager,
            $this->modelRepositoryFactory,
            $this->finderFactoryMock,
            $this->validatorMock
        );
    }

    public function testImplementObjectManage()
    {
        $this->assertInstanceOf(ObjectManager::class, $this->init());
    }

    public function testPersistNotObject()
    {
        $wyndpayObjectManager = $this->init();

        $this->expectException(ORMInvalidArgumentException::class);

        $wyndpayObjectManager->persist('toto');
    }

    public function testFlushWithInvalidObject()
    {
        $wyndpayObjectManager = $this->init();

        // I use \Exception because I need an object, I don't care object realy, I just need an instance.
        $objectToPersist = $this->createMock(\Exception::class);

        $errors = $this->createMock(ConstraintViolationListInterface::class);
        $errors->expects($this->once())->method('count')->willReturn(1);

        $error = $this->createMock(ConstraintViolationInterface::class);
        $errors->expects($this->once())->method('get')->with(0)->willReturn($error);

        $error->expects($this->once())->method('getPropertyPath')->willReturn('propertyName');
        $error->expects($this->once())->method('getMessage')->willReturn('missing value !');

        $this->validatorMock->expects($this->once())->method('validate')->with($objectToPersist)->willReturn($errors);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Validation error for object %s : Property %s : %s', get_class($objectToPersist), 'propertyName', 'missing value !'));

        $wyndpayObjectManager->persist($objectToPersist);
        $wyndpayObjectManager->flush();
    }

    public function testFlushWithNewValidObject()
    {
        $wyndpayObjectManager = $this->init();

        // I use \Exception because I need an object, I don't care object realy, I just need an instance.
        $objectToPersist = $this->getMockBuilder(\StdClass::class)->setMethods(['getId', 'setId'])->getMock();
        $entityMock = $this->getMockBuilder(\StdClass::class)->setMethods(['getId', 'setId'])->getMock();

        $errors = $this->createMock(ConstraintViolationListInterface::class);
        $errors->expects($this->once())->method('count')->willReturn(0);

        $this->validatorMock->expects($this->once())->method('validate')->with($objectToPersist)->willReturn($errors);

        $mapperMock = $this->createMock(MapperInterface::class);
        $this->mapperFactoryMock->expects($this->once())->method('getMapperFor')->with(get_class($objectToPersist))->willReturn($mapperMock);

        $objectToPersist->expects($this->once())->method('getId')->willReturn(null);
        $entityMock->expects($this->once())->method('getId')->willReturn(42);
        $objectToPersist->expects($this->once())->method('setId')->with(42);

        $mapperMock->expects($this->once())->method('create')->with($objectToPersist)->willReturn($entityMock);

        $this->doctrineEntityManager->expects($this->once())->method('persist')->with($entityMock);
        $this->doctrineEntityManager->expects($this->once())->method('flush');

        $wyndpayObjectManager->persist($objectToPersist);
        $wyndpayObjectManager->flush();
    }

    public function testFlushWithProxyNotChangedValidObject()
    {
        $wyndpayObjectManager = $this->init();

        $objectToPersist = $this->getMockBuilder(ProxyInterface::class)->setMethods(['getId'])->getMockForAbstractClass();

        $errors = $this->createMock(ConstraintViolationListInterface::class);
        $errors->expects($this->once())->method('count')->willReturn(0);

        $this->validatorMock->expects($this->once())->method('validate')->with($objectToPersist)->willReturn($errors);

        $mapperMock = $this->createMock(MapperInterface::class);
        $this->mapperFactoryMock->expects($this->once())->method('getMapperFor')->with(get_class($objectToPersist))->willReturn($mapperMock);

        $objectToPersist->expects($this->once())->method('getId')->willReturn(42);
        $objectToPersist->expects($this->once())->method('__isChanged__')->willReturn(false);
        $objectToPersist->expects($this->once())->method('__hasCollection__')->willReturn(false);

        $this->doctrineEntityManager->expects($this->once())->method('flush');

        $wyndpayObjectManager->persist($objectToPersist);
        $wyndpayObjectManager->flush();
    }

    public function testFlushWithLegacyEntityNotFound()
    {
        $wyndpayObjectManager = $this->init();

        $objectToPersist = $this->getMockBuilder(\Exception::class)->setMethods(['getId'])->getMockForAbstractClass();

        $errors = $this->createMock(ConstraintViolationListInterface::class);
        $errors->expects($this->once())->method('count')->willReturn(0);

        $this->validatorMock->expects($this->once())->method('validate')->with($objectToPersist)->willReturn($errors);

        $mapperMock = $this->createMock(MapperInterface::class);
        $this->mapperFactoryMock->expects($this->once())->method('getMapperFor')->with(get_class($objectToPersist))->willReturn($mapperMock);

        $objectToPersist->expects($this->once())->method('getId')->willReturn(42);
        $this->mapperFactoryMock->expects($this->once())->method('getLegacyEntityName')->with(get_class($objectToPersist))->willReturn(null);

        $this->expectExceptionMessage(sprintf('Legacy entity not found for model %s', get_class($objectToPersist)));
        $this->expectException(\RuntimeException::class);

        $wyndpayObjectManager->persist($objectToPersist);
        $wyndpayObjectManager->flush();
    }

    public function testFlushWithLegacyEntityFoud()
    {
        $legacyEntityName = 'OldEntity';

        $wyndpayObjectManager = $this->init();

        $objectToPersist = $this->getMockBuilder(\Exception::class)->setMethods(['getId', 'setId'])->getMockForAbstractClass();
        $repositoryMock = $this->getMockBuilder(\StdClass::class)->setMethods(['find'])->getMock();
        $errors = $this->createMock(ConstraintViolationListInterface::class);
        $mapperMock = $this->createMock(MapperInterface::class);
        $entity = $this->getMockBuilder(\StdClass::class)->setMethods(['getId'])->getMock();

        $errors->expects($this->once())->method('count')->willReturn(0);
        $this->validatorMock->expects($this->once())->method('validate')->with($objectToPersist)->willReturn($errors);

        $this->mapperFactoryMock->expects($this->once())->method('getMapperFor')->with(get_class($objectToPersist))->willReturn($mapperMock);

        $objectToPersist->expects($this->exactly(2))->method('getId')->willReturn(42);
        $this->mapperFactoryMock->expects($this->once())->method('getLegacyEntityName')->with(get_class($objectToPersist))->willReturn($legacyEntityName);

        $this->doctrineEntityManager->expects($this->once())->method('getRepository')->with($legacyEntityName)->willReturn($repositoryMock);

        $repositoryMock->expects($this->once())->method('find')->with(42)->willReturn($entity);
        $mapperMock->expects($this->once())->method('update')->with($objectToPersist, $entity);

        $this->doctrineEntityManager->expects($this->once())->method('persist')->with($entity);
        $this->doctrineEntityManager->expects($this->once())->method('flush');

        $entity->expects($this->once())->method('getId')->willReturn(99);
        $objectToPersist->expects($this->once())->method('setId')->with(99);

        $wyndpayObjectManager->persist($objectToPersist);
        $wyndpayObjectManager->flush();
    }

    public function testRemoveNotObject()
    {
        $wyndpayObjectManager = $this->init();

        $this->expectException(ORMInvalidArgumentException::class);

        $wyndpayObjectManager->remove('toto');
    }

    public function testRemoveObjectNotManagedNotFoundInDatabase()
    {
        $megacyClassName = 'OldEntity';

        $wyndpayObjectManager = $this->init();

        $objectToRemove = $this->getMockBuilder(\Exception::class)->setMethods(['getId', 'setId'])->getMockForAbstractClass();
        $repositoryMock = $this->getMockBuilder(\StdClass::class)->setMethods(['find'])->getMock();

        $repositoryMock->expects($this->once())->method('find')->with(42)->willReturn(false);
        $objectToRemove->expects($this->once())->method('getId')->willReturn(42);

        $this->mapperFactoryMock->expects($this->once())->method('getLegacyEntityName')->with(get_class($objectToRemove))->willReturn($megacyClassName);
        $this->doctrineEntityManager->expects($this->once())->method('getRepository')->with($megacyClassName)->willReturn($repositoryMock);

        $this->expectExceptionMessage(sprintf('Entity not found for object %s', get_class($objectToRemove)));
        $this->expectException(\RuntimeException::class);

        $wyndpayObjectManager->remove($objectToRemove);
        $wyndpayObjectManager->flush();
    }

    public function testRemoveObjectNotManaged()
    {
        $megacyClassName = 'OldEntity';

        $wyndpayObjectManager = $this->init();

        $objectToRemove = $this->getMockBuilder(\Exception::class)->setMethods(['getId', 'setId'])->getMockForAbstractClass();
        $repositoryMock = $this->getMockBuilder(\StdClass::class)->setMethods(['find'])->getMock();
        $entityMock = $this->getMockBuilder(\StdClass::class)->getMock();

        $repositoryMock->expects($this->once())->method('find')->with(42)->willReturn($entityMock);
        $objectToRemove->expects($this->once())->method('getId')->willReturn(42);

        $this->mapperFactoryMock->expects($this->once())->method('getLegacyEntityName')->with(get_class($objectToRemove))->willReturn($megacyClassName);
        $this->doctrineEntityManager->expects($this->once())->method('getRepository')->with($megacyClassName)->willReturn($repositoryMock);
        $this->doctrineEntityManager->expects($this->once())->method('remove')->with($entityMock);
        $this->doctrineEntityManager->expects($this->once())->method('flush');

        $wyndpayObjectManager->remove($objectToRemove);
        $wyndpayObjectManager->flush();
    }
}
