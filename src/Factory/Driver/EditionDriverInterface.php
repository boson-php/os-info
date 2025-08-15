<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Driver;

use Boson\Contracts\OsInfo\FamilyInterface;

interface EditionDriverInterface
{
    /**
     * @return non-empty-string|null
     */
    public function tryGetEdition(FamilyInterface $family): ?string;
}
