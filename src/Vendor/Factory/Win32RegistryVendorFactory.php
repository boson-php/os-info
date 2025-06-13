<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Vendor\Factory;

use Boson\Component\OsInfo\Family;
use Boson\Component\OsInfo\FamilyInterface;
use Boson\Component\OsInfo\Vendor\VendorInfo;
use FFI\CData;
use FFI\Env\Runtime;

final readonly class Win32RegistryVendorFactory implements VendorFactoryInterface
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

    public function __construct(
        private VendorFactoryInterface $delegate,
    ) {}

    public function createVendor(FamilyInterface $family): VendorInfo
    {
        if (!$family->is(Family::Windows) || !Runtime::isAvailable()) {
            return $this->delegate->createVendor($family);
        }

        try {
            return $this->tryCreateFromRegistry($family);
        } catch (\Throwable) {
            return $this->delegate->createVendor($family);
        }
    }

    private function tryCreateFromRegistry(FamilyInterface $family): VendorInfo
    {
        $ffi = \FFI::cdef(
            code: (string) @\file_get_contents(__FILE__, offset: __COMPILER_HALT_OFFSET__),
            lib: 'Advapi32.dll',
        );

        $fallback = $this->delegate->createVendor($family);

        $major = $this->getDwordKey($ffi, 'CurrentMajorVersionNumber');
        $minor = $this->getDwordKey($ffi, 'CurrentMinorVersionNumber');
        $build = $this->getStringKey($ffi, 'CurrentBuildNumber');

        // Detect that the version is present
        if ($major !== 0) {
            $version = \sprintf('%d.%d.%s', $major, $minor, $build);
        } else {
            $version = $fallback->version;
        }

        $name = $this->getStringKey($ffi, 'ProductName');

        if ($name === '') {
            $name = $fallback->name;
        }

        // TODO Windows 11 contain registry bug:
        //      https://superuser.com/questions/1834479/windows-registry-shows-windows-10-pro-despite-running-windows-11-pro
        //      https://learn.microsoft.com/en-us/answers/questions/555857/windows-11-product-name-in-registry
        if ($build !== '' && \version_compare($build, '22000', '>=')) {
            $name = \str_replace(' 10', ' 11', $name);
        }

        $edition = $this->getStringKey($ffi, 'EditionID');
        $codename = $this->getStringKey($ffi, 'DisplayVersion');

        return new VendorInfo(
            name: $name,
            version: $version,
            codename: $codename === '' ? $fallback->codename : $codename,
            edition: $edition === '' ? $fallback->edition : $edition,
        );
    }

    private function getStringKey(\FFI $advapi32, string $name): string
    {
        /** @phpstan-ignore-next-line : PHPStan false-positive */
        $buffer = $advapi32->new('char[255]');

        try {
            $size = $this->getKey($advapi32, $name, self::RRF_RT_REG_SZ, $buffer);
        } catch (\Throwable) {
            return '';
        }

        return \rtrim(\FFI::string($buffer, $size), "\0");
    }

    private function getDwordKey(\FFI $advapi32, string $name): int
    {
        /** @phpstan-ignore-next-line : PHPStan false-positive */
        $buffer = $advapi32->new('DWORD');

        try {
            $this->getKey($advapi32, $name, self::RRF_RT_REG_DWORD, $buffer);
        } catch (\Throwable) {
            return 0;
        }

        /** @var int */
        return $buffer->cdata;
    }

    private function getKey(\FFI $advapi32, string $name, int $type, CData $buffer): int
    {
        /** @phpstan-ignore-next-line : PHPStan false-positive */
        $size = $advapi32->new('DWORD');
        $size->cdata = \FFI::sizeof($buffer);

        /** @phpstan-ignore-next-line : PHPStan false-positive */
        $status = $advapi32->RegGetValueA(
            /** @phpstan-ignore-next-line : PHPStan false-positive */
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

        return $size->cdata;
    }
}

__halt_compiler();

typedef unsigned short wchar_t;
typedef intptr_t LONG_PTR;
typedef wchar_t WCHAR;
typedef char CHAR;
typedef long LONG;
typedef unsigned long DWORD;
typedef void *PVOID;
typedef const CHAR *LPCSTR;
typedef const WCHAR *LPCWSTR;
typedef PVOID HANDLE;
typedef HANDLE HKEY;
typedef LONG LSTATUS;
typedef DWORD *LPDWORD;

LSTATUS RegGetValueA(
    HKEY    hkey,
    LPCSTR  lpSubKey,
    LPCSTR  lpValue,
    DWORD   dwFlags,
    LPDWORD pdwType,
    PVOID   pvData,
    LPDWORD pcbData
);

LSTATUS RegGetValueW(
    HKEY    hkey,
    LPCWSTR lpSubKey,
    LPCWSTR lpValue,
    DWORD   dwFlags,
    LPDWORD pdwType,
    PVOID   pvData,
    LPDWORD pcbData
);
