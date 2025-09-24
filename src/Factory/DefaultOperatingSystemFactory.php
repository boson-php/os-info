<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory;

use Boson\Component\OsInfo\Factory\Driver\EnvDriver;
use Boson\Component\OsInfo\Factory\Driver\GenericDriver;
use Boson\Component\OsInfo\Factory\Driver\LinuxOsReleaseDriver;
use Boson\Component\OsInfo\Factory\Driver\MacLicenseAwareDriver;
use Boson\Component\OsInfo\Factory\Driver\MacSysVersionDriver;
use Boson\Component\OsInfo\Factory\Driver\UnixGenericDriver;
use Boson\Component\OsInfo\Factory\Driver\WindowsGenericDriver;
use Boson\Component\OsInfo\Factory\Driver\WindowsRegistryDriver;
use Boson\Component\OsInfo\Family\Factory\DefaultFamilyFactory;
use Boson\Component\OsInfo\Family\Factory\FamilyFactoryInterface;
use Boson\Component\OsInfo\OperatingSystem;

final readonly class DefaultOperatingSystemFactory implements OperatingSystemFactoryInterface
{
    private OperatingSystemFactoryInterface $factory;

    public function __construct(
        FamilyFactoryInterface $familyFactory = new DefaultFamilyFactory(),
    ) {
        $this->factory = new OperatingSystemFactory(
            familyFactory: $familyFactory,
            drivers: [
                EnvDriver::createForOverrideEnvVariables(),
                new WindowsRegistryDriver(),
                new WindowsGenericDriver(),
                new MacLicenseAwareDriver(),
                new MacSysVersionDriver(),
                new LinuxOsReleaseDriver(),
                new UnixGenericDriver(),
                new GenericDriver(),
            ]
        );
    }

    public function createOperatingSystem(): OperatingSystem
    {
        return $this->factory->createOperatingSystem();
    }
}
