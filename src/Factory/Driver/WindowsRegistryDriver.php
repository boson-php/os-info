<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Driver;

use Boson\Component\OsInfo\Factory\Driver\WindowsRegistry\Advapi32;
use Boson\Component\OsInfo\Family;
use Boson\Contracts\OsInfo\FamilyInterface;
use FFI\CData;
use FFI\Env\Runtime;

final readonly class WindowsRegistryDriver implements
    NameDriverInterface,
    VersionDriverInterface,
    CodenameDriverInterface,
    EditionDriverInterface
{
    /**
     * restrict type to REG_SZ
     */
    private const int RRF_RT_REG_SZ = 0x00000002;

    /**
     * restrict type to REG_DWORD
     */
    private const int RRF_RT_REG_DWORD = 0x00000010;

    /**
     * Contains registry key for WebView2 runtime.
     *
     * ```
     *  ((HKEY)(LONG_PTR)(LONG)0x80000002)
     * ```
     */
    private const int HKEY_LOCAL_MACHINE = 0xFFFFFFFF << 32 | 0x80000002;

    /**
     * @var non-empty-string
     */
    private const string REG_PATH_OS_INFO = 'SOFTWARE\Microsoft\Windows NT\CurrentVersion';

    /**
     * @var non-empty-string
     */
    private const string REG_KEY_NAME = 'ProductName';

    /**
     * @var non-empty-string
     */
    private const string REG_KEY_VERSION_MAJOR = 'CurrentMajorVersionNumber';

    /**
     * @var non-empty-string
     */
    private const string REG_KEY_VERSION_MINOR = 'CurrentMinorVersionNumber';

    /**
     * @var non-empty-string
     */
    private const string REG_KEY_VERSION_BUILD = 'CurrentBuildNumber';

    /**
     * @var non-empty-string
     */
    private const string REG_KEY_EDITION = 'EditionID';

    /**
     * @var non-empty-string
     */
    private const string REG_KEY_CODENAME = 'DisplayVersion';

    /**
     * @var non-empty-string
     */
    private const string WIN11_VERSION = '10.0.22000';

    private Advapi32 $advapi32;

    public function __construct()
    {
        $this->advapi32 = new \ReflectionClass(Advapi32::class)
            ->newLazyProxy(static fn() => new Advapi32());
    }

    public function tryGetName(FamilyInterface $family): ?string
    {
        if (!$family->is(Family::Windows) || !Runtime::isAvailable()) {
            return null;
        }

        return $this->tryReadName($this->advapi32);
    }

    public function tryGetVersion(FamilyInterface $family): ?string
    {
        if (!$family->is(Family::Windows) || !Runtime::isAvailable()) {
            return null;
        }

        return $this->tryReadVersion($this->advapi32);
    }

    public function tryGetCodename(FamilyInterface $family): ?string
    {
        if (!$family->is(Family::Windows) || !Runtime::isAvailable()) {
            return null;
        }

        return $this->tryReadCodename($this->advapi32);
    }

    public function tryGetEdition(FamilyInterface $family): ?string
    {
        if (!$family->is(Family::Windows) || !Runtime::isAvailable()) {
            return null;
        }

        return $this->tryReadEdition($this->advapi32);
    }

    /**
     * @return non-empty-string|null
     */
    private function tryReadName(Advapi32 $advapi32): ?string
    {
        $name = $this->getStringKey($advapi32, self::REG_KEY_NAME);

        if ($name === '') {
            return null;
        }

        $version = $this->tryReadVersion($advapi32)
            ?? $this->getVersionFromFallback();

        // TODO Windows 11 contain registry bug:
        //      https://superuser.com/questions/1834479/windows-registry-shows-windows-10-pro-despite-running-windows-11-pro
        //      https://learn.microsoft.com/en-us/answers/questions/555857/windows-11-product-name-in-registry
        if (\version_compare($version, self::WIN11_VERSION, '>=')) {
            $name = \str_replace(' 10', ' 11', $name);
        }

        /** @var non-empty-string */
        return $name;
    }

    /**
     * @return non-empty-string|null
     */
    private function tryReadVersion(Advapi32 $advapi32): ?string
    {
        $major = $this->getDwordKey($advapi32, self::REG_KEY_VERSION_MAJOR);
        $minor = $this->getDwordKey($advapi32, self::REG_KEY_VERSION_MINOR);
        $build = $this->getStringKey($advapi32, self::REG_KEY_VERSION_BUILD);

        // Detect that the version is present
        if ($major !== 0) {
            return \sprintf('%d.%d.%s', $major, $minor, $build);
        }

        return null;
    }

    /**
     * @return non-empty-string
     */
    private function getVersionFromFallback(): string
    {
        if (!\defined('PHP_WINDOWS_VERSION_MAJOR')
            || !\defined('PHP_WINDOWS_VERSION_MINOR')
            || !\defined('PHP_WINDOWS_VERSION_BUILD')
        ) {
            /** @var non-empty-string */
            return \php_uname('r');
        }

        return \vsprintf('%d.%d.%d', [
            \PHP_WINDOWS_VERSION_MAJOR,
            \PHP_WINDOWS_VERSION_MINOR,
            \PHP_WINDOWS_VERSION_BUILD,
        ]);
    }

    /**
     * @return non-empty-string|null
     */
    private function tryReadEdition(Advapi32 $advapi32): ?string
    {
        $edition = $this->getStringKey($advapi32, self::REG_KEY_EDITION);

        if ($edition === '') {
            return null;
        }

        return $edition;
    }

    /**
     * @return non-empty-string|null
     */
    private function tryReadCodename(Advapi32 $advapi32): ?string
    {
        $codename = $this->getStringKey($advapi32, self::REG_KEY_CODENAME);

        if ($codename === '') {
            return null;
        }

        return $codename;
    }

    private function getStringKey(Advapi32 $advapi32, string $name): string
    {
        $buffer = $advapi32->new('char[255]');

        try {
            $size = $this->getKey($advapi32, $name, self::RRF_RT_REG_SZ, $buffer);
        } catch (\Throwable) {
            return '';
        }

        return \rtrim(\FFI::string($buffer, $size), "\0");
    }

    private function getDwordKey(Advapi32 $advapi32, string $name): int
    {
        $buffer = $advapi32->new('DWORD');

        try {
            $this->getKey($advapi32, $name, self::RRF_RT_REG_DWORD, $buffer);
        } catch (\Throwable) {
            return 0;
        }

        /** @var int */
        return $buffer->cdata;
    }

    /**
     * @param int<0, 4294967295> $type
     *
     * @return int<0, 4294967295>
     */
    private function getKey(Advapi32 $advapi32, string $name, int $type, CData $buffer): int
    {
        $size = $advapi32->new('DWORD');
        $size->cdata = \FFI::sizeof($buffer);

        $status = $advapi32->RegGetValueA(
            $advapi32->cast('HKEY', self::HKEY_LOCAL_MACHINE),
            self::REG_PATH_OS_INFO,
            $name,
            $type,
            null,
            \FFI::addr($buffer),
            \FFI::addr($size),
        );

        if ($status !== 0) {
            throw new \RuntimeException('Could not read registry key ' . $name);
        }

        /** @var int<0, 4294967295> */
        return $size->cdata;
    }
}
