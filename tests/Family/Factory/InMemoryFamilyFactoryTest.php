<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Tests\Family\Factory;

use Boson\Component\OsInfo\Family\Factory\FamilyFactoryInterface;
use Boson\Component\OsInfo\Family\Factory\InMemoryFamilyFactory;
use Boson\Component\OsInfo\FamilyInterface;
use Boson\Component\OsInfo\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/os-info')]
final class InMemoryFamilyFactoryTest extends TestCase
{
    public function testConstructorWithDelegate(): void
    {
        $delegate = $this->createMock(FamilyFactoryInterface::class);
        $factory = new InMemoryFamilyFactory($delegate);

        self::assertInstanceOf(InMemoryFamilyFactory::class, $factory);
    }

    public function testCreateFamilyFromGlobalsCallsDelegate(): void
    {
        $expectedFamily = $this->createMock(FamilyInterface::class);
        $delegate = $this->createMock(FamilyFactoryInterface::class);
        $delegate->expects(self::once())
            ->method('createFamily')
            ->willReturn($expectedFamily);

        $factory = new InMemoryFamilyFactory($delegate);
        $family = $factory->createFamily();

        self::assertSame($expectedFamily, $family);
    }

    public function testCreateFamilyFromGlobalsCachesResult(): void
    {
        $expectedFamily = $this->createMock(FamilyInterface::class);
        $delegate = $this->createMock(FamilyFactoryInterface::class);
        $delegate->expects(self::once())
            ->method('createFamily')
            ->willReturn($expectedFamily);

        $factory = new InMemoryFamilyFactory($delegate);

        // First call should call delegate
        $family1 = $factory->createFamily();

        // Second call should return cached result
        $family2 = $factory->createFamily();

        self::assertSame($expectedFamily, $family1);
        self::assertSame($expectedFamily, $family2);
        self::assertSame($family1, $family2);
    }

    public function testCreateFamilyFromGlobalsReturnsFamilyInterface(): void
    {
        $expectedFamily = $this->createMock(FamilyInterface::class);
        $delegate = $this->createMock(FamilyFactoryInterface::class);
        $delegate->method('createFamily')
            ->willReturn($expectedFamily);

        $factory = new InMemoryFamilyFactory($delegate);
        $family = $factory->createFamily();

        self::assertInstanceOf(FamilyInterface::class, $family);
    }
}
