<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Driver;

use Boson\Component\OsInfo\Family;
use Boson\Contracts\OsInfo\FamilyInterface;

final class LinuxOsReleaseDriver implements
    NameDriverInterface,
    VersionDriverInterface,
    CodenameDriverInterface
{
    /**
     * @var non-empty-string
     */
    private const string OS_RELEASE_PATHNAME = '/etc/os-release';

    /**
     * @var non-empty-string
     */
    private const string OS_RELEASE_NAME = 'NAME';

    /**
     * @var non-empty-string
     */
    private const string OS_RELEASE_VERSION = 'VERSION_ID';

    /**
     * @var non-empty-string
     */
    private const string OS_RELEASE_CODENAME = 'VERSION_CODENAME';

    /**
     * @var non-empty-string
     */
    private const string OS_RELEASE_CODENAME_FROM_VERSION = 'VERSION';

    /**
     * @var array<non-empty-string, string>
     */
    private array $info {
        get {
            if (!isset($this->info)) {
                if (!\is_readable($this->osReleasePathname)) {
                    return $this->info = [];
                }

                /** @phpstan-ignore-next-line : INI file contains array<non-empty-string, string> */
                $this->info = (array) @\parse_ini_file($this->osReleasePathname);
            }

            return $this->info;
        }
    }

    public function __construct(
        /**
         * @var non-empty-string
         */
        private readonly string $osReleasePathname = self::OS_RELEASE_PATHNAME,
    ) {}

    public function tryGetName(FamilyInterface $family): ?string
    {
        if (!$family->is(Family::Unix)) {
            return null;
        }

        return $this->tryReadName($this->info);
    }

    public function tryGetVersion(FamilyInterface $family): ?string
    {
        if (!$family->is(Family::Unix)) {
            return null;
        }

        return $this->tryReadVersion($this->info);
    }

    public function tryGetCodename(FamilyInterface $family): ?string
    {
        if (!$family->is(Family::Unix)) {
            return null;
        }

        return $this->tryReadCodename($this->info);
    }

    /**
     * @param array<non-empty-string, string> $info
     *
     * @return non-empty-string|null
     */
    private function tryReadCodename(array $info): ?string
    {
        return $this->tryReadCodenameFromVersion($info)
            ?? $this->tryReadRawCodename($info)
            ?? null;
    }

    /**
     * @param array<non-empty-string, string> $info
     *
     * @return non-empty-string|null
     */
    private function tryReadRawCodename(array $info): ?string
    {
        $rawCodename = $info[self::OS_RELEASE_CODENAME] ?? '';

        return $rawCodename === '' ? null : $rawCodename;
    }

    /**
     * @param array<non-empty-string, string> $info
     *
     * @return non-empty-string|null
     */
    private function tryReadCodenameFromVersion(array $info): ?string
    {
        $version = $info[self::OS_RELEASE_CODENAME_FROM_VERSION] ?? '';

        if ($version === '') {
            return null;
        }

        \preg_match('/\((.+?)\)$/u', $version, $matches);

        return $matches[1] ?? null;
    }

    /**
     * @param array<non-empty-string, string> $info
     *
     * @return non-empty-string|null
     */
    private function tryReadVersion(array $info): ?string
    {
        return $this->tryReadParsedVersion($info)
            ?? $this->tryReadRawVersion($info)
            ?? null;
    }

    /**
     * @param array<non-empty-string, string> $info
     *
     * @return non-empty-string|null
     */
    private function tryReadRawVersion(array $info): ?string
    {
        $rawVersion = $info[self::OS_RELEASE_VERSION] ?? '';

        return $rawVersion === '' ? null : $rawVersion;
    }

    /**
     * @param array<non-empty-string, string> $info
     *
     * @return non-empty-string|null
     */
    private function tryReadParsedVersion(array $info): ?string
    {
        \preg_match('/^\d+(?:\.\d+){0,3}/', $info[self::OS_RELEASE_VERSION] ?? '', $matches);

        /** @var non-empty-string|null */
        return $matches[0] ?? null;
    }

    /**
     * @param array<non-empty-string, string> $info
     *
     * @return non-empty-string|null
     */
    private function tryReadName(array $info): ?string
    {
        $name = $info[self::OS_RELEASE_NAME] ?? '';

        return $name === '' ? null : $name;
    }
}
