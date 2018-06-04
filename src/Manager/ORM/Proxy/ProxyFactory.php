<?php

namespace App\Manager\ORM\Proxy;

class ProxyFactory
{
    public function getProxyForClass(string $className)
    {
        $proxyFQCN = self::getProxyNameForClass($className);

        return new $proxyFQCN();
    }

    public static function getProxyNameForClass(string $className): string
    {
        return ProxyGenerator::PROXY_NAMESPACE.'\\'.substr(strrchr($className, '\\'), 1);
    }
}
