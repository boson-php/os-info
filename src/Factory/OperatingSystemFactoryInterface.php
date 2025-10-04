<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory;

use Boson\Contracts\OsInfo\OperatingSystemInterface;

/**
 * Interface for factories that are guaranteed to create an instance of
 * {@see OperatingSystemInterface} based on external parameters.
 */
interface OperatingSystemFactoryInterface
{
    /**
     * Creates and returns a {@see OperatingSystemInterface} instance.
     */
    public function createOperatingSystemFromGlobals(): OperatingSystemInterface;
}
