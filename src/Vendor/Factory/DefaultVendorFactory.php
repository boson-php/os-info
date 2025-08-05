<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Vendor\Factory;

use Boson\Component\OsInfo\FamilyInterface;
use Boson\Component\OsInfo\Vendor\VendorInfoInterface;

final readonly class DefaultVendorFactory implements VendorFactoryInterface
{
    private OptionalVendorFactoryInterface $default;

    public function __construct()
    {
        $this->default = EnvVendorFactory::createForOverrideEnvVariables(
            delegate: new CompoundVendorFactory(
                default: new GenericVendorFactory(),
                factories: [
                    new LinuxVendorFactory(),
                    new Win32VendorFactory(),
                    new MacOSVendorFactory(),
                ],
            ),
        );
    }

    public function createVendor(FamilyInterface $family): VendorInfoInterface
    {
        return $this->default->createVendor($family);
    }
}
