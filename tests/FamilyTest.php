<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Tests;

use Boson\Component\OsInfo\Family;
use Boson\Contracts\OsInfo\FamilyInterface;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/os-info')]
final class FamilyTest extends TestCase
{
    public function testConstantsAreDefined(): void
    {
        self::assertInstanceOf(FamilyInterface::class, Family::Windows);
        self::assertInstanceOf(FamilyInterface::class, Family::Linux);
        self::assertInstanceOf(FamilyInterface::class, Family::Unix);
        self::assertInstanceOf(FamilyInterface::class, Family::BSD);
        self::assertInstanceOf(FamilyInterface::class, Family::Solaris);
        self::assertInstanceOf(FamilyInterface::class, Family::Darwin);
    }

    public function testTryFromWithValidValues(): void
    {
        self::assertSame(Family::Windows, Family::tryFrom('windows'));
        self::assertSame(Family::Linux, Family::tryFrom('linux'));
        self::assertSame(Family::Unix, Family::tryFrom('unix'));
        self::assertSame(Family::BSD, Family::tryFrom('bsd'));
        self::assertSame(Family::Solaris, Family::tryFrom('solaris'));
        self::assertSame(Family::Darwin, Family::tryFrom('darwin'));
    }

    public function testTryFromWithCaseInsensitiveValues(): void
    {
        self::assertSame(Family::Windows, Family::tryFrom('WINDOWS'));
        self::assertSame(Family::Linux, Family::tryFrom('Linux'));
        self::assertSame(Family::Unix, Family::tryFrom('Unix'));
        self::assertSame(Family::BSD, Family::tryFrom('BSD'));
        self::assertSame(Family::Solaris, Family::tryFrom('SOLARIS'));
        self::assertSame(Family::Darwin, Family::tryFrom('Darwin'));
    }

    public function testTryFromWithInvalidValues(): void
    {
        self::assertNull(Family::tryFrom('invalid'));
        self::assertNull(Family::tryFrom(''));
        self::assertNull(Family::tryFrom('win'));
        self::assertNull(Family::tryFrom('lin'));
    }

    public function testFromWithValidValues(): void
    {
        self::assertSame(Family::Windows, Family::from('windows'));
        self::assertSame(Family::Linux, Family::from('linux'));
        self::assertSame(Family::Unix, Family::from('unix'));
        self::assertSame(Family::BSD, Family::from('bsd'));
        self::assertSame(Family::Solaris, Family::from('solaris'));
        self::assertSame(Family::Darwin, Family::from('darwin'));
    }

    public function testFromWithInvalidValuesThrowsValueError(): void
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage('"invalid" is not a valid backing value for enum-like Boson\Component\OsInfo\Family');

        Family::from('invalid');
    }

    public function testFromWithEmptyStringThrowsValueError(): void
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage('"" is not a valid backing value for enum-like Boson\Component\OsInfo\Family');

        Family::from('');
    }

    public function testCasesReturnsAllFamilies(): void
    {
        $cases = Family::cases();

        self::assertCount(6, $cases);
        self::assertContains(Family::Windows, $cases);
        self::assertContains(Family::Linux, $cases);
        self::assertContains(Family::Unix, $cases);
        self::assertContains(Family::BSD, $cases);
        self::assertContains(Family::Solaris, $cases);
        self::assertContains(Family::Darwin, $cases);
    }

    public function testCasesReturnsUniqueInstances(): void
    {
        $cases = Family::cases();

        self::assertSame($cases, \array_unique($cases, \SORT_REGULAR));
    }

    public function testCreateFromGlobalsReturnsFamilyInterface(): void
    {
        $family = Family::createFromGlobals();

        self::assertInstanceOf(FamilyInterface::class, $family);
    }

    public function testCreateFromGlobalsReturnsSameInstanceOnSubsequentCalls(): void
    {
        $family1 = Family::createFromGlobals();
        $family2 = Family::createFromGlobals();

        self::assertSame($family1, $family2);
    }

    public function testCreateFromGlobalsReturnsValidFamily(): void
    {
        $family = Family::createFromGlobals();

        self::assertNotEmpty($family->name);
        self::assertInstanceOf(FamilyInterface::class, $family);
    }

    public function testFamilyHierarchy(): void
    {
        // Linux is a descendant of Unix
        self::assertTrue(Family::Linux->is(Family::Unix));
        
        // BSD is a descendant of Unix
        self::assertTrue(Family::BSD->is(Family::Unix));
        
        // Solaris is a descendant of BSD and Unix
        self::assertTrue(Family::Solaris->is(Family::BSD));
        self::assertTrue(Family::Solaris->is(Family::Unix));
        
        // Darwin is a descendant of BSD and Unix
        self::assertTrue(Family::Darwin->is(Family::BSD));
        self::assertTrue(Family::Darwin->is(Family::Unix));
        
        // Windows is not a descendant of Unix
        self::assertFalse(Family::Windows->is(Family::Unix));
        
        // Unix is not a descendant of Linux
        self::assertFalse(Family::Unix->is(Family::Linux));
    }

    public function testFamilyNames(): void
    {
        self::assertSame('Windows', Family::Windows->name);
        self::assertSame('Linux', Family::Linux->name);
        self::assertSame('Unix', Family::Unix->name);
        self::assertSame('BSD', Family::BSD->name);
        self::assertSame('Solaris', Family::Solaris->name);
        self::assertSame('Darwin', Family::Darwin->name);
    }

    public function testFamilyParents(): void
    {
        self::assertNull(Family::Windows->parent);
        self::assertNull(Family::Unix->parent);
        self::assertSame(Family::Unix, Family::Linux->parent);
        self::assertSame(Family::Unix, Family::BSD->parent);
        self::assertSame(Family::BSD, Family::Solaris->parent);
        self::assertSame(Family::BSD, Family::Darwin->parent);
    }
} 