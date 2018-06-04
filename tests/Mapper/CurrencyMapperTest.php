<?php
namespace App\Tests\Mapper;

use App\Manager\ORM\Proxy\ProxyFactory;
use App\Manager\ORM\Proxy\ProxyInterface;
use App\Mapper\CurrencyMapper;
use App\Mapper\MapperInterface;
use App\Model\Currency;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CurrencyMapperTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $proxyFactoryMock;

    public function setUp()
    {
        $this->proxyFactoryMock = $this->createMock(ProxyFactory::class);
    }

    public function init()
    {
        return new CurrencyMapper($this->proxyFactoryMock);
    }

    public function testImplementMapperInterface()
    {
        $this->assertInstanceOf(MapperInterface::class, $this->init());
    }

    public function testCreate()
    {
        $currencyMapper = $this->init();

        $currencyMock = $this->createMock(Currency::class);
        $currencyMock->expects($this->exactly(2))->method('getCode');
        $currencyMock->expects($this->exactly(2))->method('getIso');

        $this->assertInstanceOf(\App\Entity\Currency::class, $currencyMapper->create($currencyMock));
    }

    public function testUpdate()
    {
        $code = 'EUR';
        $iso = 'eur';

        $currencyMapper = $this->init();
        $currencyEntityMock = $this->createMock(\App\Entity\Currency::class);
        $currencyMock = $this->createMock(Currency::class);

        $currencyMock->expects($this->exactly(2))->method('getCode')->willReturn($code);
        $currencyMock->expects($this->exactly(2))->method('getIso')->willReturn($iso);

        $currencyEntityMock->expects($this->once())->method('setCode')->with($code);
        $currencyEntityMock->expects($this->once())->method('setNom')->with($code);
        $currencyEntityMock->expects($this->once())->method('setNumeriqueIso')->with($iso);
        $currencyEntityMock->expects($this->once())->method('setSymbole')->with($iso);

        $entity = $currencyMapper->update($currencyMock, $currencyEntityMock);

        $this->assertInstanceOf(\App\Entity\Currency::class, $entity);
    }

    public function testReverse()
    {
        $id = 42;
        $code = 'EUR';
        $iso = 'eur';

        $currencyMapper = $this->init();

        $entityMock = $this->createMock(\App\Entity\Currency::class);
        $proxyMock = $this->getMockBuilder(ProxyInterface::class)->setMethods(['setId', 'setCode', 'setIso'])->getMockForAbstractClass();

        $this->proxyFactoryMock->expects($this->once())->method('getProxyForClass')->with(Currency::class)->willReturn($proxyMock);
        $entityMock->expects($this->once())->method('getId')->willReturn($id);
        $entityMock->expects($this->once())->method('getCode')->willReturn($code);
        $entityMock->expects($this->once())->method('getNumeriqueIso')->willReturn($iso);

        $proxyMock->expects($this->once())->method('setId')->with($id);
        $proxyMock->expects($this->once())->method('setIso')->with($iso);
        $proxyMock->expects($this->once())->method('setCode')->with($code);
        $proxyMock->expects($this->once())->method('__setChanged__')->with(false);

        $this->assertSame($proxyMock, $currencyMapper->reverse($entityMock));
    }

    public function testSuppor()
    {
        $currencyMapper = $this->init();

        $this->assertSame(Currency::class, $currencyMapper->support());
    }
}
