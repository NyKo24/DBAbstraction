<?php

namespace App\Manager\ORM\Proxy;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;

class ProxyAutoloader
{
    public static function resolveProxyFile($proxyDir, $className)
    {
        if (0 !== strpos($className, ProxyGenerator::PROXY_NAMESPACE)) {
            throw InvalidArgumentException::notProxyClass($className, ProxyGenerator::PROXY_NAMESPACE);
        }

        // remove proxy namespace from class name
        $classNameRelativeToProxyNamespace = substr($className, strlen(ProxyGenerator::PROXY_NAMESPACE));

        // remove namespace separators from remaining class name
        $fileName = str_replace('\\', '', $classNameRelativeToProxyNamespace);

        return $proxyDir.'__Proxy__AppModel'.$fileName.'.php';
    }

    public static function register($proxyDir)
    {
        $autoload = function ($classname) use ($proxyDir) {
            if (0 !== strpos($classname, ProxyGenerator::PROXY_NAMESPACE)) {
                return false;
            }

            $file = ProxyAutoloader::resolveProxyFile($proxyDir, $classname);

            require $file;
        };

        spl_autoload_register($autoload);

        return $autoload;
    }
}
