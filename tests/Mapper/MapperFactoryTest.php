<?php
namespace App\Tests\Mapper;

use App\Manager\ORM\Proxy\ProxyFactory;
use App\Mapper\MapperFactory;
use App\Mapper\MapperInterface;
use PHPUnit\Framework\TestCase;

class MapperFactoryTest extends TestCase
{
    public function init()
    {
        return new MapperFactory();
    }

    public function testAddMapper()
    {
        $mapperFactory = $this->init();
        $className = 'MyObject';

        $mapperMock = $this->getMockForAbstractClass(MapperInterface::class);
        $mapperMock->expects($this->exactly(3))->method('support')->willReturn($className);

        $mapperFactory->addMapper($mapperMock);
    }

    public function testAddMapperAlreadyAdded()
    {
        $mapperFactory = $this->init();
        $className = 'MyObject';

        $mapperMock = $this->getMockForAbstractClass(MapperInterface::class);
        $mapperMock->expects($this->exactly(4))->method('support')->willReturn($className);

        // firsta add, method support call 3 times;
        $mapperFactory->addMapper($mapperMock);

        // second add, method support call 1 time;
        $mapperFactory->addMapper($mapperMock);
    }

    public function testGetMapper()
    {
        $mapperFactory = $this->init();
        $className = 'MyObject';

        $mapperMock = $this->getMockForAbstractClass(MapperInterface::class);
        $mapperMock->method('support')->willReturn($className);

        $mapperFactory->addMapper($mapperMock);

        $this->assertInstanceOf(MapperInterface::class, $mapperFactory->getMapperFor($className));
    }

    public function testGetMapperForProxy()
    {
        $mapperFactory = $this->init();
        $className = 'MyObject';
        $proxyClassName = ProxyFactory::getProxyNameForClass($className);

        $mapperMock = $this->getMockForAbstractClass(MapperInterface::class);
        $mapperMock->method('support')->willReturn($className);

        $mapperFactory->addMapper($mapperMock);

        $this->assertInstanceOf(MapperInterface::class, $mapperFactory->getMapperFor($proxyClassName));
    }

    public function testGetMapperNotFound()
    {
        $mapperFactory = $this->init();
        $className = 'MyObject';

        $this->expectExceptionMessage(sprintf('No mapper found for class %s', $className));
        $this->expectException(\RuntimeException::class);

        $mapperFactory->getMapperFor($className);
    }
}
