<?php
namespace Tests\Manager\ORM\Proxy;

use App\Manager\ORM\Proxy\ProxyDirectoryManager;
use App\Manager\ORM\Proxy\ProxyFactory;
use App\Manager\ORM\Proxy\ProxyGenerator;
use App\Manager\ORM\Proxy\ProxyManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ProxyManagerTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $proxyDirectoryManagerMock;

    /**
     * @var MockObject
     */
    private $proxyGeneratorMock;

    /**
     * @var MockObject
     */
    private $finderMock;

    public function setUp()
    {
        $this->proxyDirectoryManagerMock = $this->createMock(ProxyDirectoryManager::class);
        $this->proxyGeneratorMock = $this->createMock(ProxyGenerator::class);
        $this->finderMock = $this->createMock(Finder::class);
    }

    public function init()
    {
        return new ProxyManager($this->proxyDirectoryManagerMock, $this->proxyGeneratorMock, $this->finderMock);
    }

    public function testConstructor()
    {
        $this->proxyDirectoryManagerMock->expects($this->once())->method('create');

        $this->init();
    }

    public function testGenerateProxyForClass()
    {
        $className = 'FakeClass';
        $proxyClassContent = 'hello world';

        $this->proxyGeneratorMock->expects($this->once())->method('generateProxyForClass')->with($className)->willReturn($proxyClassContent);
        $this->proxyDirectoryManagerMock->expects($this->once())->method('writeProxyClass')->with($proxyClassContent, $className);

        $proxyManager = $this->init();
        $proxyManager->generateProxyForClass($className);
    }

    public function testWarmUp()
    {
        $classNameFileName = 'HelloWorld.php';
        $classFQCN = 'App\\Model\\HelloWorld';
        $proxyClassContent = 'My amazing proxy content';

        $this->proxyDirectoryManagerMock->expects($this->once())->method('clear');

        $splFileInfoMock = $this->createMock(SplFileInfo::class);

        $this->finderMock->expects($this->once())->method('in');
        $this->finderMock->expects($this->once())->method('files');
        $this->finderMock->expects($this->once())->method('getIterator')->willReturn([$splFileInfoMock]);
        $splFileInfoMock->expects($this->once())->method('getFilename')->willReturn($classNameFileName);
        $this->proxyGeneratorMock->expects($this->once())->method('generateProxyForClass')->with($classFQCN)->willReturn($proxyClassContent);
        $this->proxyDirectoryManagerMock->expects($this->once())->method('writeProxyClass')->with($proxyClassContent, $classFQCN);

        $proxyManager = $this->init();
        $proxyManager->warmUp();
    }
}
