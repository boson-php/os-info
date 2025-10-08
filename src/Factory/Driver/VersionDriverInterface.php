<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Driver;

use Boson\Component\OsInfo\FamilyInterface;

interface VersionDriverInterface
{
    /**
     * @return non-empty-string|null
     */
    public function tryGetVersion(FamilyInterface $family): ?string;
}
