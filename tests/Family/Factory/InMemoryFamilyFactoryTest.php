<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Tests\Family\Factory;

use Boson\Component\OsInfo\Family\Factory\InMemoryFamilyFactory;
use Boson\Component\OsInfo\Family\Factory\FamilyFactoryInterface;
use Boson\Component\OsInfo\Tests\TestCase;
use Boson\Contracts\OsInfo\FamilyInterface;
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
            ->method('createFamilyFromGlobals')
            ->willReturn($expectedFamily);

        $factory = new InMemoryFamilyFactory($delegate);
        $family = $factory->createFamilyFromGlobals();

        self::assertSame($expectedFamily, $family);
    }

    public function testCreateFamilyFromGlobalsCachesResult(): void
    {
        $expectedFamily = $this->createMock(FamilyInterface::class);
        $delegate = $this->createMock(FamilyFactoryInterface::class);
        $delegate->expects(self::once())
            ->method('createFamilyFromGlobals')
            ->willReturn($expectedFamily);

        $factory = new InMemoryFamilyFactory($delegate);

        // First call should call delegate
        $family1 = $factory->createFamilyFromGlobals();

        // Second call should return cached result
        $family2 = $factory->createFamilyFromGlobals();

        self::assertSame($expectedFamily, $family1);
        self::assertSame($expectedFamily, $family2);
        self::assertSame($family1, $family2);
    }

    public function testCreateFamilyFromGlobalsReturnsFamilyInterface(): void
    {
        $expectedFamily = $this->createMock(FamilyInterface::class);
        $delegate = $this->createMock(FamilyFactoryInterface::class);
        $delegate->method('createFamilyFromGlobals')
            ->willReturn($expectedFamily);

        $factory = new InMemoryFamilyFactory($delegate);
        $family = $factory->createFamilyFromGlobals();

        self::assertInstanceOf(FamilyInterface::class, $family);
    }
}
