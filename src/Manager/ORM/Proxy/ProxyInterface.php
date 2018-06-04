<?php

namespace App\Manager\ORM\Proxy;

interface ProxyInterface
{
    public function __isChanged__(): bool;

    public function __setChanged__(bool $changed);

    public function __hasCollection__(): bool;

    public function __setHasCollection__(bool $hasRelation);
}
