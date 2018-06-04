<?php

namespace App\Finder;

interface FinderInterface
{
    public function support(): string;

    public function find($id);
}
