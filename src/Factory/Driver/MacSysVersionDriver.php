<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Driver;

use Boson\Component\OsInfo\Family;
use Boson\Contracts\OsInfo\FamilyInterface;

final class MacSysVersionDriver implements
    NameDriverInterface,
    VersionDriverInterface
{
    /**
     * @var non-empty-string
     */
    private const string SYS_VERSION_PATHNAME = '/System/Library/CoreServices/SystemVersion.plist';

    /**
     * @var non-empty-string
     */
    private const string SYS_NAME_PCRE = '/<key>ProductName<\/key>\n\h*<string>(.+?)<\/string>/';

    /**
     * @var non-empty-string
     */
    private const string SYS_VERSION_PCRE = '/<key>ProductVersion<\/key>\n\h*<string>(.+?)<\/string>/';

    private string $systemVersionContents {
        get {
            return $this->systemVersionContents
                ??= (string) @\file_get_contents($this->systemVersionPathname);
        }
    }

    public function __construct(
        /**
         * @var non-empty-string
         */
        private readonly string $systemVersionPathname = self::SYS_VERSION_PATHNAME,
    ) {}

    public function tryGetName(FamilyInterface $family): ?string
    {
        if (!$family->is(Family::Darwin)) {
            return null;
        }

        return $this->tryReadName($this->systemVersionContents);
    }

    public function tryGetVersion(FamilyInterface $family): ?string
    {
        if (!$family->is(Family::Darwin)) {
            return null;
        }

        return $this->tryReadVersion($this->systemVersionContents);
    }

    /**
     * @return non-empty-string|null
     */
    private function tryReadName(string $info): ?string
    {
        if (!\str_starts_with($info, '<?xml')) {
            return null;
        }

        \preg_match(self::SYS_NAME_PCRE, $info, $matches);

        if (isset($matches[1]) && $matches[1] !== '') {
            return $matches[1];
        }

        return null;
    }

    /**
     * @return non-empty-string|null
     */
    private function tryReadVersion(string $info): ?string
    {
        if (!\str_starts_with($info, '<?xml')) {
            return null;
        }

        \preg_match(self::SYS_VERSION_PCRE, $info, $matches);

        if (isset($matches[1]) && $matches[1] !== '') {
            return $matches[1];
        }

        return null;
    }
}
