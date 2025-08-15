<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Tests\Factory;

use Boson\Component\OsInfo\Factory\DefaultOperatingSystemFactory;
use Boson\Component\OsInfo\Family\Factory\FamilyFactoryInterface;
use Boson\Component\OsInfo\Tests\TestCase;
use Boson\Contracts\OsInfo\OperatingSystemInterface;
use Boson\Contracts\OsInfo\FamilyInterface;
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

    public function testCreateOperatingSystemFromGlobalsReturnsOperatingSystemInterface(): void
    {
        $factory = new DefaultOperatingSystemFactory();
        $os = $factory->createOperatingSystemFromGlobals();

        self::assertInstanceOf(OperatingSystemInterface::class, $os);
    }

    public function testCreateOperatingSystemFromGlobalsReturnsValidOperatingSystem(): void
    {
        $factory = new DefaultOperatingSystemFactory();
        $os = $factory->createOperatingSystemFromGlobals();

        self::assertNotEmpty($os->name);
        self::assertNotEmpty($os->version);
        self::assertInstanceOf(FamilyInterface::class, $os->family);
    }
}
