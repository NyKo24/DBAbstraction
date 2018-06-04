<?php
namespace App\Tests\ModelRepository;

use App\ModelRepository\ModelRepository;
use App\ModelRepository\ModelRepositoryFactory;
use PHPUnit\Framework\TestCase;

class ModelRepositoryFactoryTest extends TestCase
{
    public function init()
    {
        return new ModelRepositoryFactory();
    }

    public function testGetRepositoryNotFound()
    {
        $modelRepositoryFactory = $this->init();

        $classFQCN = 'App\\Model\\Object';

        $this->expectExceptionMessage(sprintf('Repository for model %s not found in container', $classFQCN));
        $this->expectException(\RuntimeException::class);

        $modelRepositoryFactory->getRepository($classFQCN);
    }

    public function testGetRepository()
    {
        $modelRepositoryFactory = $this->init();

        $classFQCN = 'App\\Model\\Object';

        $modelRepositoryMock = $this->getMockBuilder(ModelRepository::class)->setMethods(['getClassName'])->disableOriginalConstructor()->getMockForAbstractClass();
        $modelRepositoryMock->method('getClassName')->willReturn($classFQCN);

        $modelRepositoryFactory->addRepository($modelRepositoryMock);
        $repository = $modelRepositoryFactory->getRepository($classFQCN);

        $this->assertInstanceOf(ModelRepository::class, $repository);
    }

    public function testAddRepository()
    {
        $modelRepositoryFactory = $this->init();
        $classFQCN = 'App\\Model\\Object';

        $modelRepositoryMock = $this->getMockBuilder(ModelRepository::class)->setMethods(['getClassName'])->disableOriginalConstructor()->getMockForAbstractClass();
        $modelRepositoryMock->expects($this->exactly(2))->method('getClassName')->willReturn($classFQCN);

        $modelRepositoryFactory->addRepository($modelRepositoryMock);
    }

    public function testAddRepositoryAlreadyAdded()
    {
        $modelRepositoryFactory = $this->init();
        $classFQCN = 'App\\Model\\Object';

        $modelRepositoryMock = $this->getMockBuilder(ModelRepository::class)->setMethods(['getClassName'])->disableOriginalConstructor()->getMockForAbstractClass();
        $modelRepositoryMock->expects($this->exactly(3))->method('getClassName')->willReturn($classFQCN);

        $modelRepositoryFactory->addRepository($modelRepositoryMock);
        $modelRepositoryFactory->addRepository($modelRepositoryMock);
    }

    public function testGetRepositoryClassNameForClass()
    {
        $classFQCN = 'App\\Model\\Object';

        $expected = 'App\\ModelRepository\\ObjectRepository';

        $this->assertSame($expected, ModelRepositoryFactory::getRepositoryClassNameForClass($classFQCN));
    }
}
