<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Tests\Family\Factory;

use Boson\Component\OsInfo\Family\Factory\DefaultFamilyFactory;
use Boson\Component\OsInfo\Tests\TestCase;
use Boson\Contracts\OsInfo\FamilyInterface;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/os-info')]
final class DefaultFamilyFactoryTest extends TestCase
{
    private DefaultFamilyFactory $factory;

    #[Before]
    public function setUpFactory(): void
    {
        $this->factory = new DefaultFamilyFactory();
    }

    public function testCreateFamilyFromGlobalsReturnsFamilyInterface(): void
    {
        $family = $this->factory->createFamilyFromGlobals();

        self::assertInstanceOf(FamilyInterface::class, $family);
    }

    public function testCreateFamilyFromGlobalsReturnsValidFamily(): void
    {
        $family = $this->factory->createFamilyFromGlobals();

        self::assertNotEmpty($family->name);
        self::assertInstanceOf(FamilyInterface::class, $family);
    }
}
