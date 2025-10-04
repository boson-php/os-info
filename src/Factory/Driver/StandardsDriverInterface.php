<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Driver;

use Boson\Contracts\OsInfo\FamilyInterface;
use Boson\Contracts\OsInfo\StandardInterface;

interface StandardsDriverInterface
{
    /**
     * @return iterable<array-key, StandardInterface>|null
     */
    public function tryGetStandards(FamilyInterface $family): ?iterable;
}
