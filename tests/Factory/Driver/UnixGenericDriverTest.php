<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Tests\Factory\Driver;

use Boson\Component\OsInfo\Factory\Driver\UnixGenericDriver;
use Boson\Component\OsInfo\Tests\TestCase;
use Boson\Contracts\OsInfo\FamilyInterface;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/os-info')]
final class UnixGenericDriverTest extends TestCase
{
    private UnixGenericDriver $driver;

    #[Before]
    protected function setUpUnixGenericDriver(): void
    {
        $this->driver = new UnixGenericDriver();
    }

    public function testTryGetVersionReturnsStringOrNull(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $version = $this->driver->tryGetVersion($family);

        // Version can be null or a non-empty string
        if ($version !== null) {
            self::assertIsString($version);
            self::assertNotEmpty($version);
        }
    }

    public function testTryGetVersionMatchesExpectedPattern(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $version = $this->driver->tryGetVersion($family);

        if ($version !== null) {
            // Should match the pattern: digits followed by optional dots and digits
            self::assertMatchesRegularExpression('/^\d+(?:\.\d+){0,3}$/', $version);
        }
    }

    public function testTryGetVersionExtractsVersionFromSystemRelease(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $systemRelease = \php_uname('r');
        $version = $this->driver->tryGetVersion($family);

        if ($version !== null) {
            // The version should be a prefix of the system release
            self::assertStringStartsWith($version, $systemRelease);
        }
    }

    public function testTryGetVersionWithComplexReleaseString(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $version = $this->driver->tryGetVersion($family);

        if ($version !== null) {
            // Should not contain any non-numeric or non-dot characters
            self::assertMatchesRegularExpression('/^[\d.]+$/', $version);
        }
    }

    public function testTryGetVersionConsistency(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $version1 = $this->driver->tryGetVersion($family);
        $version2 = $this->driver->tryGetVersion($family);

        // Should return the same result on subsequent calls
        self::assertSame($version1, $version2);
    }
}
