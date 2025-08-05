<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Family;

use Boson\Component\OsInfo\FamilyInterface;

interface FamilyProviderInterface
{
    /**
     * Gets the family this operating system belongs to.
     */
    public FamilyInterface $family { get; }
}
