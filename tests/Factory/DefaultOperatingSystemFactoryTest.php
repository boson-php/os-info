<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Tests\Factory;

use Boson\Component\OsInfo\Factory\DefaultOperatingSystemFactory;
use Boson\Component\OsInfo\Family\Factory\FamilyFactoryInterface;
use Boson\Component\OsInfo\FamilyInterface;
use Boson\Component\OsInfo\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/os-info')]
final class DefaultOperatingSystemFactoryTest extends TestCase
{
    public function testConstructorWithDefaultFamilyFactory(): void
    {
        $factory = new DefaultOperatingSystemFactory();

        self::assertInstanceOf(DefaultOperatingSystemFactory::class, $factory);
    }

    public function testConstructorWithCustomFamilyFactory(): void
    {
        $familyFactory = $this->createMock(FamilyFactoryInterface::class);
        $factory = new DefaultOperatingSystemFactory($familyFactory);

        self::assertInstanceOf(DefaultOperatingSystemFactory::class, $factory);
    }

    public function testCreateOperatingSystemFromGlobalsReturnsValidOperatingSystem(): void
    {
        $factory = new DefaultOperatingSystemFactory();
        $os = $factory->createOperatingSystem();

        self::assertNotEmpty($os->name);
        self::assertNotEmpty($os->version);
        self::assertInstanceOf(FamilyInterface::class, $os->family);
    }
}
