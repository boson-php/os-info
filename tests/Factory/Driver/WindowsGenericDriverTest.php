<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Tests\Factory\Driver;

use Boson\Component\OsInfo\Factory\Driver\WindowsGenericDriver;
use Boson\Component\OsInfo\Family;
use Boson\Component\OsInfo\FamilyInterface;
use Boson\Component\OsInfo\Tests\TestCase;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/os-info')]
final class WindowsGenericDriverTest extends TestCase
{
    private WindowsGenericDriver $driver;

    #[Before]
    protected function setUpWindowsGenericDriver(): void
    {
        $this->driver = new WindowsGenericDriver();
    }

    public function testTryGetNameWithWindowsFamily(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $family->method('is')
            ->with(Family::Windows)
            ->willReturn(true);

        $name = $this->driver->tryGetName($family);

        // On Windows, should return a non-empty string
        if (\str_contains(\strtolower(\php_uname('s')), 'windows')) {
            self::assertIsString($name);
            self::assertNotEmpty($name);
        } else {
            // On non-Windows systems, might return null or empty string
            self::assertTrue($name === null || $name === '');
        }
    }

    public function testTryGetNameWithNonWindowsFamily(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $family->method('is')
            ->with(Family::Windows)
            ->willReturn(false);

        $name = $this->driver->tryGetName($family);

        self::assertNull($name);
    }

    public function testTryGetNameExtractsFromVersionString(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $family->method('is')
            ->with(Family::Windows)
            ->willReturn(true);

        $name = $this->driver->tryGetName($family);

        if ($name !== null) {
            // Should be extracted from php_uname('v') using regex
            $versionString = \php_uname('v');
            self::assertStringContainsString($name, $versionString);
        }
    }

    public function testTryGetVersionWithWindowsFamily(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $family->method('is')
            ->with(Family::Windows)
            ->willReturn(true);

        $version = $this->driver->tryGetVersion($family);

        if (\defined('PHP_WINDOWS_VERSION_MAJOR')
            && \defined('PHP_WINDOWS_VERSION_MINOR')
            && \defined('PHP_WINDOWS_VERSION_BUILD')
        ) {
            // Should return a version string in format major.minor.build
            self::assertIsString($version);
            self::assertNotEmpty($version);
            self::assertMatchesRegularExpression('/^\d+\.\d+\.\d+$/', $version);
        } else {
            // If constants are not defined, should return null
            self::assertNull($version);
        }
    }

    public function testTryGetVersionWithNonWindowsFamily(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $family->method('is')
            ->with(Family::Windows)
            ->willReturn(false);

        $version = $this->driver->tryGetVersion($family);

        // Should still check for Windows constants and return version if available
        if (\defined('PHP_WINDOWS_VERSION_MAJOR')
            && \defined('PHP_WINDOWS_VERSION_MINOR')
            && \defined('PHP_WINDOWS_VERSION_BUILD')
        ) {
            self::assertIsString($version);
            self::assertNotEmpty($version);
        } else {
            self::assertNull($version);
        }
    }

    public function testTryGetVersionFormat(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $family->method('is')
            ->with(Family::Windows)
            ->willReturn(true);

        $version = $this->driver->tryGetVersion($family);

        if ($version !== null) {
            $parts = \explode('.', $version);
            self::assertCount(3, $parts);

            foreach ($parts as $part) {
                self::assertIsNumeric($part);
                self::assertGreaterThanOrEqual(0, (int) $part);
            }
        }
    }

    public function testTryGetVersionConsistency(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $family->method('is')
            ->with(Family::Windows)
            ->willReturn(true);

        $version1 = $this->driver->tryGetVersion($family);
        $version2 = $this->driver->tryGetVersion($family);

        // Should return the same result on subsequent calls
        self::assertSame($version1, $version2);
    }
}
