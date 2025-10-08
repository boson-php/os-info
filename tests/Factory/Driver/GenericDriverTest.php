<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Tests\Factory\Driver;

use Boson\Component\OsInfo\Factory\Driver\GenericDriver;
use Boson\Component\OsInfo\Family;
use Boson\Component\OsInfo\FamilyInterface;
use Boson\Component\OsInfo\Standard;
use Boson\Component\OsInfo\Tests\TestCase;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/os-info')]
final class GenericDriverTest extends TestCase
{
    private GenericDriver $driver;

    #[Before]
    protected function setUpGenericDriver(): void
    {
        $this->driver = new GenericDriver();
    }

    public function testTryGetNameReturnsNonEmptyString(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $name = $this->driver->tryGetName($family);

        self::assertIsString($name);
        self::assertNotEmpty($name);
    }

    public function testTryGetNameReturnsSystemName(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $name = $this->driver->tryGetName($family);

        // Should return the same as php_uname('s')
        self::assertSame(\php_uname('s'), $name);
    }

    public function testTryGetVersionReturnsNonEmptyString(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $version = $this->driver->tryGetVersion($family);

        self::assertIsString($version);
        self::assertNotEmpty($version);
    }

    public function testTryGetVersionReturnsSystemRelease(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $version = $this->driver->tryGetVersion($family);

        // Should return the same as php_uname('r')
        self::assertSame(\php_uname('r'), $version);
    }

    public function testTryGetStandardsWithUnixFamily(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $family->method('is')
            ->with(Family::Unix)
            ->willReturn(true);

        $standards = $this->driver->tryGetStandards($family);

        self::assertIsArray($standards);
        self::assertCount(1, $standards);
        self::assertContains(Standard::Posix, $standards);
    }

    public function testTryGetStandardsWithNonUnixFamily(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $family->method('is')
            ->with(Family::Unix)
            ->willReturn(false);

        $standards = $this->driver->tryGetStandards($family);

        self::assertIsArray($standards);
        self::assertCount(0, $standards);
    }

    public function testTryGetStandardsWithLinuxFamily(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $family->method('is')
            ->with(Family::Unix)
            ->willReturn(true); // Linux is a Unix family

        $standards = $this->driver->tryGetStandards($family);

        self::assertIsArray($standards);
        self::assertCount(1, $standards);
        self::assertContains(Standard::Posix, $standards);
    }

    public function testTryGetStandardsWithWindowsFamily(): void
    {
        $family = $this->createMock(FamilyInterface::class);
        $family->method('is')
            ->with(Family::Unix)
            ->willReturn(false); // Windows is not a Unix family

        $standards = $this->driver->tryGetStandards($family);

        self::assertIsArray($standards);
        self::assertCount(0, $standards);
    }
}
