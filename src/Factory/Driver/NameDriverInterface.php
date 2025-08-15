<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Driver;

use Boson\Contracts\OsInfo\FamilyInterface;

interface NameDriverInterface
{
    /**
     * @return non-empty-string|null
     */
    public function tryGetName(FamilyInterface $family): ?string;
}
