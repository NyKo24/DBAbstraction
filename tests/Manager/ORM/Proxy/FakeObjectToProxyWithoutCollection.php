<?php
namespace App\Tests\Manager\ORM\Proxy;

class FakeObjectToProxyWithoutCollection
{
    private $propertyWithTyping;
    private $propertyWithoutTyping;
    private $propertyWithObjectType;

    public function getPropertyWithTyping()
    {
        return $this->propertyWithTyping;
    }

    public function setPropertyWithTyping(string $propertyWithTyping)
    {
        $this->propertyWithTyping = $propertyWithTyping;
    }

    public function getPropertyWithoutTyping()
    {
        return $this->propertyWithoutTyping;
    }

    public function setPropertyWithoutTyping($propertyWithoutTyping)
    {
        $this->propertyWithoutTyping = $propertyWithoutTyping;
    }

    public function getPropertyWithObjectType()
    {
        return $this->propertyWithObjectType;
    }

    public function setPropertyWithObjectType(\DateTime $propertyWithObjectType)
    {
        $this->propertyWithObjectType = $propertyWithObjectType;
    }
}
