<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Driver;

use Boson\Component\OsInfo\Family;
use Boson\Contracts\OsInfo\FamilyInterface;

final readonly class WindowsGenericDriver implements
    NameDriverInterface,
    VersionDriverInterface
{
    /**
     * @return non-empty-string|null
     */
    public function tryGetName(FamilyInterface $family): ?string
    {
        if (!$family->is(Family::Windows)) {
            return null;
        }

        \preg_match('/.+\((.+?)\)/', \php_uname('v'), $matches);

        /** @var non-empty-string */
        return $matches[1];
    }

    /**
     * @return non-empty-string|null
     */
    public function tryGetVersion(FamilyInterface $family): ?string
    {
        if (!\defined('PHP_WINDOWS_VERSION_MAJOR')
            || !\defined('PHP_WINDOWS_VERSION_MINOR')
            || !\defined('PHP_WINDOWS_VERSION_BUILD')
        ) {
            return null;
        }

        return \vsprintf('%d.%d.%d', [
            \PHP_WINDOWS_VERSION_MAJOR,
            \PHP_WINDOWS_VERSION_MINOR,
            \PHP_WINDOWS_VERSION_BUILD,
        ]);
    }
}
