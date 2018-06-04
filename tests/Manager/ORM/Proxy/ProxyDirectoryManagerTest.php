<?php
namespace App\Tests\Manager\ORM\Proxy;

use App\Manager\ORM\Proxy\ProxyDirectoryManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class ProxyDirectoryManagerTest extends TestCase
{
    const TEMP_DIR = '/tmp/Wyndpay/ProxyDirectoryManagerTest';
    /**
     * @var MockObject
     */
    private $fileSystemMock;

    public function init(string $cacheDir = self::TEMP_DIR)
    {
        return new ProxyDirectoryManager($cacheDir, $this->fileSystemMock);
    }

    public function setUp()
    {
        $this->fileSystemMock = $this->createMock(Filesystem::class);
    }

    public function testGetCacheDir()
    {
        $proxyDirectoryManager = $this->init('');

        $this->assertSame(ProxyDirectoryManager::CACHE_DIR, $proxyDirectoryManager->getCacheDir());

        $proxyDirectoryManager = $this->init('toto');
        $this->assertSame('toto'.ProxyDirectoryManager::CACHE_DIR, $proxyDirectoryManager->getCacheDir());
    }

    public function testCreateCacheDirectoryWithDirectoryAlreadyExist()
    {
        $proxyDirectoryManager = $this->init();

        $this->fileSystemMock->expects($this->once())->method('exists')->willReturn(true);

        $this->fileSystemMock->expects($this->never())->method('mkdir')->with($proxyDirectoryManager->getCacheDir());
        $proxyDirectoryManager->create();
    }

    public function testCreateCacheDirectory()
    {
        $proxyDirectoryManager = $this->init();

        $this->fileSystemMock->expects($this->once())->method('exists')->willReturn(false);

        $this->fileSystemMock->expects($this->once())->method('mkdir')->with($proxyDirectoryManager->getCacheDir());
        $proxyDirectoryManager->create();
    }

    public function testDeleteCacheDirectory()
    {
        $proxyDirectoryManager = $this->init();

        $proxyDirectoryManager->create();

        $this->fileSystemMock->expects($this->once())->method('remove')->with($proxyDirectoryManager->getCacheDir());
        $proxyDirectoryManager->delete();
    }

    public function testClearCache()
    {
        $proxyDirectoryManager = $this->init();

        $this->fileSystemMock->expects($this->once())->method('remove')->with($proxyDirectoryManager->getCacheDir());
        $this->fileSystemMock->expects($this->once())->method('exists')->with($proxyDirectoryManager->getCacheDir());
        $this->fileSystemMock->expects($this->once())->method('mkdir')->with($proxyDirectoryManager->getCacheDir());

        $proxyDirectoryManager->clear();
    }

    public function testWriteProxyClass()
    {
        $proxyDirectoryManager = $this->init();
        $proxyDirectoryManager->create();

        $className = 'App\\Toto\\TyTyClass';
        $proxyFileName = '__Proxy__AppTotoTyTyClass.php';
        $classContent = 'Hello World !!';

        $this->fileSystemMock->expects($this->once())->method('touch')->with($proxyDirectoryManager->getCacheDir().$proxyFileName);
        $this->fileSystemMock->expects($this->once())->method('appendToFile')->with($proxyDirectoryManager->getCacheDir().$proxyFileName, $classContent);
        $proxyDirectoryManager->writeProxyClass($classContent, $className);
    }
}
