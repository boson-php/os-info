<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Driver;

use Boson\Component\OsInfo\Family;
use Boson\Component\OsInfo\Standard;
use Boson\Contracts\OsInfo\FamilyInterface;
use Boson\Contracts\OsInfo\StandardInterface;

final readonly class GenericDriver implements
    NameDriverInterface,
    VersionDriverInterface,
    StandardsDriverInterface
{
    /**
     * @return non-empty-string
     */
    public function tryGetName(FamilyInterface $family): string
    {
        /** @var non-empty-string */
        return \php_uname('s');
    }

    /**
     * @return non-empty-string
     */
    public function tryGetVersion(FamilyInterface $family): string
    {
        /** @var non-empty-string */
        return \php_uname('r');
    }

    /**
     * @return list<StandardInterface>
     */
    public function tryGetStandards(FamilyInterface $family): array
    {
        if ($family->is(Family::Unix)) {
            return [Standard::Posix];
        }

        return [];
    }
}
