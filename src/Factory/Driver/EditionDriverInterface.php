<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Driver;

use Boson\Component\OsInfo\FamilyInterface;

interface EditionDriverInterface
{
    /**
     * @return non-empty-string|null
     */
    public function tryGetEdition(FamilyInterface $family): ?string;
}
