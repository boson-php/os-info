<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Family;

use Boson\Contracts\OsInfo\FamilyInterface;

/**
 * @phpstan-require-implements FamilyInterface
 */
trait FamilyImpl
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public readonly string $name,
        public readonly ?FamilyInterface $parent = null,
    ) {}

    public function is(FamilyInterface $family): bool
    {
        return $this === $family || $this->parent?->is($family) === true;
    }

    public function equals(mixed $other): bool
    {
        return $other === $this
            || ($other instanceof FamilyInterface
                && $other->name === $this->name);
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
