<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Family\Factory;

use Boson\Component\OsInfo\FamilyInterface;

/**
 * Interface for factories that are guaranteed to create an instance of
 * {@see FamilyInterface} based on external parameters.
 */
interface FamilyFactoryInterface
{
    /**
     * Creates and returns a {@see FamilyInterface} instance.
     */
    public function createFamily(): FamilyInterface;
}
