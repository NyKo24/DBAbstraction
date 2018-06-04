<?php
namespace App\Tests\Manager\ORM\Proxy;

use App\Manager\ORM\Proxy\ProxyGenerator;
use App\Manager\ORM\Proxy\ProxyInterface;
use PHPUnit\Framework\TestCase;

class ProxyGeneratorTest extends TestCase
{
    private $objectToProxyWithCollection;
    private $objectToProxyWithoutCollection;

    public function setUp()
    {
        $this->objectToProxyWithCollection = new FakeObjectToProxyWithCollection();
        $this->objectToProxyWithoutCollection = new FakeObjectToProxyWithoutCollection();
    }

    public function init()
    {
        return new ProxyGenerator();
    }

    public function testGenerateProxyClassName()
    {
        $proxyGenerator = $this->init();

        $proxyClassContent = $proxyGenerator->generateProxyForClass($this->getObjectNameWithCollectionToProxy());

        $this->assertContains('class FakeObjectToProxy', $proxyClassContent);
    }

    public function testGenerateClassName()
    {
        $proxyGenerator = $this->init();

        $proxyClassContent = $proxyGenerator->generateProxyForClass($this->getObjectNameWithCollectionToProxy());

        $this->assertContains('extends \App\Tests\Manager\ORM\Proxy\FakeObjectToProxy', $proxyClassContent);
    }

    public function testGenerateProxyNamespace()
    {
        $proxyGenerator = $this->init();

        $proxyClassContent = $proxyGenerator->generateProxyForClass($this->getObjectNameWithCollectionToProxy());

        $this->assertContains(sprintf('namespace %s;', ProxyGenerator::PROXY_NAMESPACE), $proxyClassContent);
    }

    public function testGenerateChangedProperty()
    {
        $proxyGenerator = $this->init();

        $proxyClassContent = $proxyGenerator->generateProxyForClass($this->getObjectNameWithCollectionToProxy());

        $this->assertContains('private $__changed__ = false;', $proxyClassContent);
    }

    public function testGenerateSetters()
    {
        $proxyGenerator = $this->init();

        $proxyClassContent = $proxyGenerator->generateProxyForClass($this->getObjectNameWithCollectionToProxy());

        $expectAddCollection = '
    public function addCollection($item) {
        $this->__changed__ = true;
        
        return parent::addCollection($item);
    }';

        $expectRemoveCollection = '
    public function removeCollection($item) {
        $this->__changed__ = true;
        
        return parent::removeCollection($item);
    }';

        $expectSetPropertyWithTyping = '
    public function setPropertyWithTyping(string $propertyWithTyping) {
        $this->__changed__ = true;
        
        return parent::setPropertyWithTyping($propertyWithTyping);
    }';

        $expectSetPropertyWithoutTyping = '
    public function setPropertyWithoutTyping($propertyWithoutTyping) {
        $this->__changed__ = true;
        
        return parent::setPropertyWithoutTyping($propertyWithoutTyping);
    }';

        $expectSetPropertyWithObjectType = '
    public function setPropertyWithObjectType(\DateTime $propertyWithObjectType) {
        $this->__changed__ = true;
        
        return parent::setPropertyWithObjectType($propertyWithObjectType);
    }';

        $this->assertContains($expectAddCollection, $proxyClassContent);
        $this->assertContains($expectRemoveCollection, $proxyClassContent);
        $this->assertContains($expectSetPropertyWithTyping, $proxyClassContent);
        $this->assertContains($expectSetPropertyWithoutTyping, $proxyClassContent);
        $this->assertContains($expectSetPropertyWithObjectType, $proxyClassContent);
        $this->assertNotContains('getCollection', $proxyClassContent);
        $this->assertNotContains('getPropertyWithTyping', $proxyClassContent);
        $this->assertNotContains('getPropertyWithoutTyping', $proxyClassContent);
        $this->assertNotContains('getPropertyWithObjectType', $proxyClassContent);
    }

    public function testGenerateIsChanged()
    {
        $proxyGenerator = $this->init();

        $proxyClassContent = $proxyGenerator->generateProxyForClass($this->getObjectNameWithCollectionToProxy());

        $exepectedGetter = '
    public function __isChanged__(): bool {
        return $this->__changed__;
    }';

        $exepectedSetter = '
    public function __setChanged__(bool $changed) {
        $this->__changed__ = $changed;
    }';

        $this->assertContains($exepectedGetter, $proxyClassContent);
        $this->assertContains($exepectedSetter, $proxyClassContent);
    }

    public function testGenerateHasCollection()
    {
        $proxyGenerator = $this->init();

        $proxyClassContent = $proxyGenerator->generateProxyForClass($this->getObjectNameWithCollectionToProxy());

        $exepectedGetter = '
    public function __hasCollection__(): bool {
        return $this->__hasCollection__;
    }';

        $exepectedSetter = '
    public function __setHasCollection__(bool $hasCollection) {
        $this->__hasCollection__ = $hasCollection;
    }';

        $this->assertContains($exepectedGetter, $proxyClassContent);
        $this->assertContains($exepectedSetter, $proxyClassContent);
    }

    public function testGenerateCollectionProperty()
    {
        $proxyGenerator = $this->init();

        $proxyClassContent = $proxyGenerator->generateProxyForClass($this->getObjectNameWithCollectionToProxy());

        $this->assertContains('private $__hasCollection__ = true;', $proxyClassContent);

        $proxyClassContent = $proxyGenerator->generateProxyForClass($this->getObjectNameWithoutCollectionToProxy());

        $this->assertContains('private $__hasCollection__ = false;', $proxyClassContent);
    }

    public function testGenerateProxyInterface()
    {
        $proxyGenerator = $this->init();

        $proxyClassContent = $proxyGenerator->generateProxyForClass($this->getObjectNameWithCollectionToProxy());

        $this->assertContains(sprintf('implements \\%s {', ProxyInterface::class), $proxyClassContent);
    }

    private function getObjectNameWithCollectionToProxy()
    {
        return FakeObjectToProxyWithCollection::class;
    }

    private function getObjectNameWithoutCollectionToProxy()
    {
        return FakeObjectToProxyWithoutCollection::class;
    }
}
