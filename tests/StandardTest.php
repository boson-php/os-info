<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Tests;

use Boson\Component\OsInfo\Standard;
use Boson\Component\OsInfo\StandardInterface;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/os-info')]
final class StandardTest extends TestCase
{
    public function testConstantsAreDefined(): void
    {
        self::assertInstanceOf(StandardInterface::class, Standard::Posix);
    }

    public function testTryFromWithValidValues(): void
    {
        self::assertSame(Standard::Posix, Standard::tryFrom('posix'));
    }

    public function testTryFromWithCaseInsensitiveValues(): void
    {
        self::assertSame(Standard::Posix, Standard::tryFrom('POSIX'));
        self::assertSame(Standard::Posix, Standard::tryFrom('Posix'));
    }

    public function testTryFromWithInvalidValues(): void
    {
        self::assertNull(Standard::tryFrom('invalid'));
        self::assertNull(Standard::tryFrom(''));
        self::assertNull(Standard::tryFrom('pos'));
    }

    public function testFromWithValidValues(): void
    {
        self::assertSame(Standard::Posix, Standard::from('posix'));
    }

    public function testFromWithInvalidValuesThrowsValueError(): void
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage('"invalid" is not a valid backing value for enum-like Boson\Component\OsInfo\Standard');

        Standard::from('invalid');
    }

    public function testFromWithEmptyStringThrowsValueError(): void
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage('"" is not a valid backing value for enum-like Boson\Component\OsInfo\Standard');

        Standard::from('');
    }

    public function testCasesReturnsAllStandards(): void
    {
        $cases = Standard::cases();

        self::assertCount(1, $cases);
        self::assertContains(Standard::Posix, $cases);
    }

    public function testCasesReturnsUniqueInstances(): void
    {
        $cases = Standard::cases();

        self::assertSame($cases, \array_unique($cases, \SORT_REGULAR));
    }

    public function testStandardName(): void
    {
        self::assertSame('POSIX', Standard::Posix->name);
    }

    public function testStandardParent(): void
    {
        self::assertNull(Standard::Posix->parent);
    }

    public function testStandardIsSelf(): void
    {
        self::assertTrue(Standard::Posix->is(Standard::Posix));
    }

    public function testStandardIsWithNullParent(): void
    {
        self::assertTrue(Standard::Posix->is(Standard::Posix));
    }
}
