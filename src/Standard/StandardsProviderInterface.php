<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Standard;

use Boson\Component\OsInfo\StandardInterface;

interface StandardsProviderInterface
{
    /**
     * Gets the list of standards supported by this operating system.
     *
     * @var iterable<array-key, StandardInterface>
     */
    public iterable $standards { get; }
}
