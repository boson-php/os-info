<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory;

use Boson\Contracts\OsInfo\OperatingSystemInterface;

final class InMemoryOperatingSystemFactory implements OperatingSystemFactoryInterface
{
    private ?OperatingSystemInterface $current = null;

    public function __construct(
        private readonly OperatingSystemFactoryInterface $delegate,
    ) {}

    public function createOperatingSystemFromGlobals(): OperatingSystemInterface
    {
        return $this->current ??= $this->delegate->createOperatingSystemFromGlobals();
    }
}
