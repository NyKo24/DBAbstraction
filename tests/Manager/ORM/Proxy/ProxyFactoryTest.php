<?php
namespace Tests\Manager\ORM\Proxy;

use App\Manager\ORM\Proxy\ProxyFactory;
use App\Manager\ORM\Proxy\ProxyGenerator;
use PHPUnit\Framework\TestCase;

class ProxyFactoryTest extends TestCase
{
    public function init()
    {
        return new ProxyFactory();
    }

    public function testGetProxyNameFormClass()
    {
        $className = 'App\\Toto\\TestClass';
        $proxyFQCN = ProxyGenerator::PROXY_NAMESPACE.'\\'.'TestClass';

        $this->assertSame($proxyFQCN, ProxyFactory::getProxyNameForClass($className));
    }
}
