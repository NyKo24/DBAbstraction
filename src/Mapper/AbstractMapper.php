<?php

namespace App\Mapper;

use App\Manager\ORM\Proxy\ProxyFactory;
use App\Manager\ORM\Proxy\ProxyInterface;

class AbstractMapper
{
    private $className;
    /**
     * @var ProxyFactory
     */
    private $proxyFactory;

    public function __construct(string $className, ProxyFactory $proxyFactory)
    {
        $this->className = $className;
        $this->proxyFactory = $proxyFactory;
    }

    protected function getProxy(string $className = null)
    {
        return $this->proxyFactory->getProxyForClass($className ?? $this->className);
    }

    protected function initProxy(ProxyInterface $proxy)
    {
        $proxy->__setChanged__(false);
    }
}
