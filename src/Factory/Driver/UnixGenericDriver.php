<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Driver;

use Boson\Contracts\OsInfo\FamilyInterface;

final readonly class UnixGenericDriver implements VersionDriverInterface
{
    /**
     * @return non-empty-string|null
     */
    public function tryGetVersion(FamilyInterface $family): ?string
    {
        \preg_match('/^\d+(?:\.\d+){0,3}/', \php_uname('r'), $matches);

        /** @var non-empty-string|null */
        return $matches[0] ?? null;
    }
}
