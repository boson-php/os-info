<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Tests;

use Boson\Component\OsInfo\Family;
use Boson\Component\OsInfo\OperatingSystem;
use Boson\Component\OsInfo\Standard;
use Boson\Contracts\OsInfo\FamilyInterface;
use Boson\Contracts\OsInfo\OperatingSystemInterface;
use Boson\Contracts\OsInfo\StandardInterface;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/os-info')]
final class OperatingSystemTest extends TestCase
{
    public function testConstructorWithAllParameters(): void
    {
        $family = Family::Linux;
        $name = 'Ubuntu';
        $version = '22.04';
        $codename = 'Jammy Jellyfish';
        $edition = 'LTS';
        $standards = [Standard::Posix];

        $os = new OperatingSystem(
            family: $family,
            name: $name,
            version: $version,
            codename: $codename,
            edition: $edition,
            standards: $standards,
        );

        self::assertSame($family, $os->family);
        self::assertSame($name, $os->name);
        self::assertSame($version, $os->version);
        self::assertSame($codename, $os->codename);
        self::assertSame($edition, $os->edition);
        self::assertSame($standards, $os->standards);
    }

    public function testConstructorWithMinimalParameters(): void
    {
        $family = Family::Windows;
        $name = 'Windows';
        $version = '11';

        $os = new OperatingSystem(
            family: $family,
            name: $name,
            version: $version,
        );

        self::assertSame($family, $os->family);
        self::assertSame($name, $os->name);
        self::assertSame($version, $os->version);
        self::assertNull($os->codename);
        self::assertNull($os->edition);
        self::assertSame([], $os->standards);
    }

    public function testConstructorWithEmptyStandards(): void
    {
        $family = Family::Darwin;
        $name = 'macOS';
        $version = '14.0';

        $os = new OperatingSystem(
            family: $family,
            name: $name,
            version: $version,
            standards: [],
        );

        self::assertSame([], $os->standards);
    }

    public function testConstructorWithIterableStandards(): void
    {
        $family = Family::Linux;
        $name = 'CentOS';
        $version = '8';
        $standards = new \ArrayIterator([Standard::Posix]);

        $os = new OperatingSystem(
            family: $family,
            name: $name,
            version: $version,
            standards: $standards,
        );

        self::assertSame([Standard::Posix], $os->standards);
    }

    public function testIsSupportsWithSupportedStandard(): void
    {
        $os = new OperatingSystem(
            family: Family::Linux,
            name: 'Ubuntu',
            version: '22.04',
            standards: [Standard::Posix],
        );

        self::assertTrue($os->isSupports(Standard::Posix));
    }

    public function testIsSupportsWithUnsupportedStandard(): void
    {
        $os = new OperatingSystem(
            family: Family::Windows,
            name: 'Windows',
            version: '11',
            standards: [],
        );

        self::assertFalse($os->isSupports(Standard::Posix));
    }

    public function testIsSupportsWithMultipleStandards(): void
    {
        $customStandard = new class('Custom') implements StandardInterface {
            use \Boson\Component\OsInfo\Standard\StandardImpl;
        };

        $os = new OperatingSystem(
            family: Family::Linux,
            name: 'Ubuntu',
            version: '22.04',
            standards: [Standard::Posix, $customStandard],
        );

        self::assertTrue($os->isSupports(Standard::Posix));
        self::assertTrue($os->isSupports($customStandard));
    }

    public function testCreateFromGlobalsReturnsOperatingSystemInterface(): void
    {
        $os = OperatingSystem::createFromGlobals();

        self::assertInstanceOf(OperatingSystemInterface::class, $os);
        self::assertInstanceOf(OperatingSystem::class, $os);
    }

    public function testCreateFromGlobalsReturnsSameInstanceOnSubsequentCalls(): void
    {
        $os1 = OperatingSystem::createFromGlobals();
        $os2 = OperatingSystem::createFromGlobals();

        self::assertSame($os1, $os2);
    }

    public function testCreateFromGlobalsReturnsValidOperatingSystem(): void
    {
        $os = OperatingSystem::createFromGlobals();

        self::assertNotEmpty($os->name);
        self::assertNotEmpty($os->version);
        self::assertInstanceOf(FamilyInterface::class, $os->family);
    }
} 