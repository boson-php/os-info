<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Tests\Standard;

use Boson\Component\OsInfo\Standard\BuiltinStandard;
use Boson\Component\OsInfo\StandardInterface;
use Boson\Component\OsInfo\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/os-info')]
final class BuiltinStandardTest extends TestCase
{
    public function testConstructorWithNameOnly(): void
    {
        $standard = new BuiltinStandard('TestStandard');

        self::assertSame('TestStandard', $standard->name);
        self::assertNull($standard->parent);
    }

    public function testConstructorWithNameAndParent(): void
    {
        $parent = new BuiltinStandard('ParentStandard');
        $standard = new BuiltinStandard('TestStandard', $parent);

        self::assertSame('TestStandard', $standard->name);
        self::assertSame($parent, $standard->parent);
    }

    public function testIsWithSameStandard(): void
    {
        $standard = new BuiltinStandard('TestStandard');

        self::assertTrue($standard->is($standard));
    }

    public function testIsWithDifferentStandard(): void
    {
        $standard1 = new BuiltinStandard('TestStandard1');
        $standard2 = new BuiltinStandard('TestStandard2');

        self::assertFalse($standard1->is($standard2));
    }

    public function testIsWithParentStandard(): void
    {
        $parent = new BuiltinStandard('ParentStandard');
        $child = new BuiltinStandard('ChildStandard', $parent);

        self::assertTrue($child->is($parent));
        self::assertFalse($parent->is($child));
    }

    public function testIsWithGrandparentStandard(): void
    {
        $grandparent = new BuiltinStandard('GrandparentStandard');
        $parent = new BuiltinStandard('ParentStandard', $grandparent);
        $child = new BuiltinStandard('ChildStandard', $parent);

        self::assertTrue($child->is($grandparent));
        self::assertTrue($child->is($parent));
        self::assertFalse($grandparent->is($child));
        self::assertFalse($parent->is($child));
    }

    public function testEqualsWithSameInstance(): void
    {
        $standard = new BuiltinStandard('TestStandard');

        self::assertTrue($standard->equals($standard));
    }

    public function testEqualsWithDifferentInstanceSameName(): void
    {
        $standard1 = new BuiltinStandard('TestStandard');
        $standard2 = new BuiltinStandard('TestStandard');

        self::assertTrue($standard1->equals($standard2));
    }

    public function testEqualsWithDifferentInstanceDifferentName(): void
    {
        $standard1 = new BuiltinStandard('TestStandard1');
        $standard2 = new BuiltinStandard('TestStandard2');

        self::assertFalse($standard1->equals($standard2));
    }

    public function testEqualsWithNonStandardInterface(): void
    {
        $standard = new BuiltinStandard('TestStandard');
        $other = new \stdClass();

        self::assertFalse($standard->equals($other));
    }

    public function testToString(): void
    {
        $standard = new BuiltinStandard('TestStandard');

        self::assertSame('TestStandard', $standard->toString());
    }

    public function testMagicToString(): void
    {
        $standard = new BuiltinStandard('TestStandard');

        self::assertSame('TestStandard', (string) $standard);
    }

    public function testImplementsStandardInterface(): void
    {
        $standard = new BuiltinStandard('TestStandard');

        self::assertInstanceOf(StandardInterface::class, $standard);
    }
}
