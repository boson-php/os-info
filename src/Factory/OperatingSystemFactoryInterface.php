<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory;

use Boson\Component\OsInfo\OperatingSystem;

/**
 * Interface for factories that are guaranteed to create an instance of
 * {@see OperatingSystem} based on external parameters.
 */
interface OperatingSystemFactoryInterface
{
    /**
     * Creates and returns a {@see OperatingSystem} instance.
     */
    public function createOperatingSystem(): OperatingSystem;
}
