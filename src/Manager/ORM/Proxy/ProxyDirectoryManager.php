<?php

namespace App\Manager\ORM\Proxy;

use Symfony\Component\Filesystem\Filesystem;

class ProxyDirectoryManager
{
    const CACHE_DIR = '/WyndPayManager/Proxies/';
    /**
     * @var string
     */
    private $cacheDirectory;
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(string $cacheDirectory, Filesystem $filesystem)
    {
        $this->cacheDirectory = $cacheDirectory;
        $this->filesystem = $filesystem;
    }

    public function getCacheDir(): string
    {
        return $this->cacheDirectory.self::CACHE_DIR;
    }

    public function create()
    {
        if (!$this->filesystem->exists($this->getCacheDir())) {
            $this->filesystem->mkdir($this->getCacheDir());
        }
    }

    public function writeProxyClass(string $content, string $className)
    {
        $filename = $this->getCacheDir().$this->getProxyClassNameForClass($className);

        $this->filesystem->remove($filename);
        $this->filesystem->touch($filename);
        $this->filesystem->appendToFile($filename, $content);
    }

    public function delete()
    {
        $this->filesystem->remove($this->getCacheDir());
    }

    public function clear()
    {
        $this->delete();
        $this->create();
    }

    public static function getProxyClassNameForClass(string $className)
    {
        return '__Proxy__'.str_replace('\\', '', $className.'.php');
    }
}
