<?php

namespace App\Manager\ORM\Proxy;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ProxyManager
{
    /**
     * @var ProxyDirectoryManager
     */
    private $proxyDirectoryManager;
    /**
     * @var ProxyGenerator
     */
    private $proxyGenerator;
    /**
     * @var Finder
     */
    private $finder;

    public function __construct(ProxyDirectoryManager $proxyDirectoryManager, ProxyGenerator $proxyGeneratorManager, Finder $finder)
    {
        $this->proxyDirectoryManager = $proxyDirectoryManager;

        $this->proxyDirectoryManager->create();
        $this->proxyGenerator = $proxyGeneratorManager;
        $this->finder = $finder;
    }

    public function generateProxyForClass(string $className)
    {
        $proxyClassContent = $this->proxyGenerator->generateProxyForClass($className);
        $this->proxyDirectoryManager->writeProxyClass($proxyClassContent, $className);
    }

    public function warmUp()
    {
        $this->proxyDirectoryManager->clear();

        $this->finder->in(__DIR__.'/../../../Model');
        $this->finder->files();

        /** @var SplFileInfo $file */
        foreach ($this->finder->getIterator() as $file) {
            $className = 'App\\Model\\'.str_replace('.php', '', $file->getFilename());

            $this->generateProxyForClass($className);
        }
    }
}
