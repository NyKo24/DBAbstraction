<?php

namespace App\Manager\ORM\Proxy;

use ReflectionClass;
use ReflectionMethod;

class ProxyGenerator
{
    const PROXY_CLASS_NAME_PATTERN = '__Provies__%s';
    const PROXY_NAMESPACE = 'WyndpayProxyManager\\Proxies';

    private $proxyTemplate = '<?php
<namespace>
class <proxyClassName> extends <className> implements <proxyInterface> {
    <changedProperty>
    <collectionProperty>
    
    <setters>
    
    <isChanged>
    <hasCollection>
}
';

    private $hasCollection;

    public function generateProxyForClass(string $className): string
    {
        $template = $this->proxyTemplate;
        $this->hasCollection = false;

        $template = str_replace('<proxyClassName>', $this->generateProxyClassName($className), $template);
        $template = str_replace('<className>', '\\'.$className, $template);
        $template = str_replace('<namespace>', $this->generateProxyNamespace(), $template);
        $template = str_replace('<changedProperty>', $this->generateChangedProperty(), $template);
        $template = str_replace('<setters>', $this->generateSetters($className), $template);
        $template = str_replace('<isChanged>', $this->generateIsChanged(), $template);
        $template = str_replace('<hasCollection>', $this->generateHasCollection(), $template);
        $template = str_replace('<collectionProperty>', $this->generateCollectionProperty($this->hasCollection), $template);
        $template = str_replace('<proxyInterface>', $this->generateProxyInterface(), $template);

        return $template;
    }

    private function generateChangedProperty(): string
    {
        return <<<EOT
private \$__changed__ = false;
EOT;
    }

    private function generateCollectionProperty(bool $setTrue): string
    {
        $val = $setTrue ? 'true' : 'false';

        return <<<EOT
private \$__hasCollection__ = $val;
EOT;
    }

    private function generateProxyClassName(string $className): string
    {
        return substr(strrchr($className, '\\'), 1);
    }

    private function generateProxyNamespace(): string
    {
        return 'namespace '.self::PROXY_NAMESPACE.';';
    }

    private function generateSetters(string $className): string
    {
        $reflexion = new ReflectionClass($className);
        $methods = $reflexion->getMethods(ReflectionMethod::IS_PUBLIC);

        $setters = [];

        $strSetters = '';

        foreach ($methods as $method) {
            if (
                'set' !== substr($method->getName(), 0, 3)
                && 'add' !== substr($method->getName(), 0, 3)
                && 'remove' !== substr($method->getName(), 0, 6)
            ) {
                continue;
            }

            if ('add' === substr($method->getName(), 0, 3)) {
                $this->hasCollection = true;
            }

            $setters[$method->getName()] = [
                'signature' => [],
                'parrent' => [],
            ];
            $parameters = $method->getParameters();
            foreach ($parameters as $parameter) {
                $setters[$method->getName()]['parrent'][] = '$'.$parameter->getName();
                $type = '';
                if ($parameter->getType()) {
                    $type = $parameter->getType().' ';
                    if (!$parameter->getType()->isBuiltin()) {
                        $type = '\\'.$type;
                    }
                }
                $setters[$method->getName()]['signature'][] = $type.'$'.$parameter->getName().($parameter->isOptional() ? ' = '.$parameter->getDefaultValue() : '');
            }

            $template = $this->getSetterTemplate();
            $template = $this->generateSetterName($method->getName(), $template);
            $template = $this->generateSetterParams($method->getName(), $setters[$method->getName()]['signature'], $template);
            $template = $this->generateSetterParentParams($method->getName(), $setters[$method->getName()]['parrent'], $template);

            $strSetters .= $template;
        }

        return $strSetters;
    }

    private function generateSetterName(string $setterName, string $template): string
    {
        return str_replace('<setterName>', $setterName, $template);
    }

    private function generateSetterParams(string $setterName, array $setterParams, string $template): string
    {
        return str_replace('<setterParams>', implode(', ', $setterParams), $template);
    }

    private function generateSetterParentParams(string $setterName, array $setterParams, string $template): string
    {
        return str_replace('<setterParrentParams>', implode(', ', $setterParams), $template);
    }

    private function getSetterTemplate(): string
    {
        return '
    public function <setterName>(<setterParams>) {
        $this->__changed__ = true;
        
        return parent::<setterName>(<setterParrentParams>);
    }
';
    }

    private function generateIsChanged(): string
    {
        return '
    public function __isChanged__(): bool {
        return $this->__changed__;
    }
    
    public function __setChanged__(bool $changed) {
        $this->__changed__ = $changed;
    }
';
    }

    private function generateHasCollection(): string
    {
        return '
    public function __hasCollection__(): bool {
        return $this->__hasCollection__;
    }
    
    public function __setHasCollection__(bool $hasCollection) {
        $this->__hasCollection__ = $hasCollection;
    }
';
    }

    private function generateProxyInterface(): string
    {
        return '\\'.ProxyInterface::class;
    }
}
