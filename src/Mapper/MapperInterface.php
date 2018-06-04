<?php

namespace App\Mapper;

interface MapperInterface
{
    public function create($businessObject);

    public function update($businessObject, $entity);

    public function reverse($entity);

    public function support(): string;
}
