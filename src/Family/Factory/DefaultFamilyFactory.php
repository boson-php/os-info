<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Family\Factory;

use Boson\Contracts\OsInfo\FamilyInterface;

final readonly class DefaultFamilyFactory implements FamilyFactoryInterface
{
    private FamilyFactoryInterface $factory;

    public function __construct()
    {
        $this->factory = EnvFamilyFactory::createForOverrideEnvVariables(
            delegate: new GenericFamilyFactory(),
        );
    }

    public function createFamilyFromGlobals(): FamilyInterface
    {
        return $this->factory->createFamilyFromGlobals();
    }
}
