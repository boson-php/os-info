<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Vendor;

interface VendorInfoInterface
{
    /**
     * Gets the name of the operating system.
     *
     * The name should be a non-empty string that uniquely identifies this
     * operating system. For example, "Ubuntu 22.04 LTS" or "Windows 11".
     *
     * @var non-empty-string
     */
    public string $name { get; }

    /**
     * Gets the version of the operating system.
     *
     * @var non-empty-string
     */
    public string $version { get; }

    /**
     * Gets the codename of the operating system.
     *
     * @var non-empty-string|null
     */
    public ?string $codename { get; }

    /**
     * Gets the edition of the operating system.
     *
     * @var non-empty-string|null
     */
    public ?string $edition { get; }
}
