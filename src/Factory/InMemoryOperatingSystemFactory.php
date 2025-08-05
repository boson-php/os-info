<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory;

use Boson\Component\OsInfo\OperatingSystemInterface;

final class InMemoryOperatingSystemFactory implements OperatingSystemFactoryInterface
{
    private ?OperatingSystemInterface $current = null;

    public function __construct(
        private readonly OperatingSystemFactoryInterface $delegate,
    ) {}

    public function createOperatingSystem(): OperatingSystemInterface
    {
        return $this->current ??= $this->delegate->createOperatingSystem();
    }
}
