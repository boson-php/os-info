<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo;

use Boson\Component\OsInfo\Family\FamilyProviderInterface;
use Boson\Component\OsInfo\Standard\StandardsProviderInterface;
use Boson\Component\OsInfo\Vendor\VendorInfoInterface;

interface OperatingSystemInterface extends
    FamilyProviderInterface,
    StandardsProviderInterface,
    VendorInfoInterface
{
    /**
     * Checks if this operating system supports the given standard.
     *
     * This method checks if any of the standards supported by this operating
     * system (including standards of its family) supports the given standard.
     *
     * @api
     */
    public function isSupports(StandardInterface $standard): bool;
}
