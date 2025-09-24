<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo;

use Boson\Contracts\ValueObject\StringValueObjectInterface;

/**
 * Interface representing an OS family.
 *
 * @template-extends StringValueObjectInterface<non-empty-string>
 */
interface FamilyInterface extends StringValueObjectInterface
{
    /**
     * Gets the name of the operating system family.
     *
     * The name should be a non-empty string that uniquely identifies this
     * family within the operating system hierarchy.
     *
     * @var non-empty-string
     */
    public string $name {
        get;
    }

    /**
     * Gets the parent operating system family, if any.
     *
     * Returns {@see null} if this family is a root family (has no parent).
     */
    public ?self $parent {
        get;
    }

    /**
     * Checks if this family is the same as or a descendant of the given family.
     */
    public function is(FamilyInterface $family): bool;
}
