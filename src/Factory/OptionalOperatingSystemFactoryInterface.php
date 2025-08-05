<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory;

use Boson\Component\OsInfo\OperatingSystemInterface;

interface OptionalOperatingSystemFactoryInterface
{
    public function createOperatingSystem(): ?OperatingSystemInterface;
}
