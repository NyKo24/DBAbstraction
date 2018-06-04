<?php
namespace App\Tests\Manager\ORM\Proxy;

use Doctrine\Common\Collections\ArrayCollection;

class FakeObjectToProxyWithCollection
{
    private $collection;
    private $propertyWithTyping;
    private $propertyWithoutTyping;
    private $propertyWithObjectType;

    public function __construct()
    {
        $this->collection = new ArrayCollection();
    }

    public function getCollection()
    {
        return $this->collection;
    }

    public function addCollection($item) {
        if (!$this->collection->contains($item)) {
            $this->collection->add($item);
        }
    }

    public function removeCollection($item) {
        if ($this->collection->contains($item)) {
            $this->collection->removeElement($item);
        }
    }

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
