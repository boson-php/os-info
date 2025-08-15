<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Driver;

use Boson\Component\OsInfo\Family;
use Boson\Contracts\OsInfo\FamilyInterface;

final readonly class MacLicenseAwareDriver implements
    CodenameDriverInterface
{
    /**
     * @var non-empty-string
     */
    private const string SYS_LICENSE_PATHNAME = '/System/Library/CoreServices/Setup\ Assistant.app/Contents/Resources/en.lproj/OSXSoftwareLicense.rtf';

    public function __construct(
        /**
         * @var non-empty-string
         */
        private string $osxLicensePathname = self::SYS_LICENSE_PATHNAME,
    ) {}

    public function tryGetCodename(FamilyInterface $family): ?string
    {
        if (!$family->is(Family::Darwin)) {
            return null;
        }

        return $this->fetchCodenameFromLicense();
    }

    /**
     * @link https://unix.stackexchange.com/questions/234104/get-osx-codename-from-command-line/458401
     *
     * @return non-empty-string|null
     */
    private function fetchCodenameFromLicense(): ?string
    {
        if (!\is_readable($this->osxLicensePathname)) {
            return null;
        }

        $license = (string) @\file_get_contents($this->osxLicensePathname);

        \preg_match('/SOFTWARE LICENSE AGREEMENT FOR (?:OS X|macOS)\h+(.+?)\\\/isum', $license, $matches);

        if (isset($matches[1]) && $matches[1] !== '') {
            return $matches[1];
        }

        return null;
    }
}
