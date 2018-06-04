<?php
namespace Tests\Manager\ORM\Proxy;

use App\Manager\ORM\Proxy\ProxyAutoloader;
use App\Manager\ORM\Proxy\ProxyGenerator;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProxyAutoloadTest extends TestCase
{
    public function testResolveProxyFileWithClassNotMatch()
    {
        $proxyDir = '/proxy';
        $className = 'MyClass';

        $this->expectExceptionObject(InvalidArgumentException::notProxyClass($className, ProxyGenerator::PROXY_NAMESPACE));

        ProxyAutoloader::resolveProxyFile($proxyDir, $className);
    }

    public function testResolveProxyFileWithCorrectClassName()
    {
        $proxyDir = '/proxy/';
        $className = 'WyndpayProxyManager\\Proxies\\MyClass';

        $this->assertSame('/proxy/__Proxy__AppModelMyClass.php', ProxyAutoloader::resolveProxyFile($proxyDir, $className));
    }

    public function testRegisterAutoloadWithInccorectClass()
    {
        $proxyDir = '/proxy/';
        $className = 'MyClass';

        $callback = ProxyAutoloader::register($proxyDir);

        $this->assertFalse($callback($className));
    }
}
